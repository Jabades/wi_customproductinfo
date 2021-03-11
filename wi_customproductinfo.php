<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2021 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once 'classes' . DIRECTORY_SEPARATOR . 'WiCustomInfo.php';

class Wi_customproductinfo extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'wi_customproductinfo';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Jesús Abades';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Webimpacto Custom Product Info Block');
        $this->description = $this->l('Shows a personalized text block in producto info block.');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';
        $this->addTab($this->name, 'AdminWicustomproductinfoAjax', -1, 'Ajax');
        Configuration::updateValue('WI_CUSTOMPRODUCTINFO_ENABLED', '0');
        $res = parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('displayProductPriceBlock') &&
            $this->registerHook('DisplayProductAdditionalInfo') &&
            $this->registerHook('displayReassurance');
        return $res;
    }

    public function uninstall()
    {
        include dirname(__FILE__) . '/sql/uninstall.php';
        Configuration::deleteByName('WI_CUSTOMPRODUCTINFO_ENABLED');
        return parent::uninstall();
    }

    public function getAvailableHooks()
    {
        return array(
            'displayProductPriceBlock' => $this->l('Price Block'),
            'displayProductAdditionalInfo' => $this->l('Product info Block'),
            'displayReassurance' => $this->l('Reassurance Block'),
        );
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {        
        /**
         * If values have been submitted in the form, process.
         */

        $html = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/help.tpl');

        $params = array(
            'wi_customproductinfo' => array(
                'module_dir' => $this->_path,
                'module_name' => $this->name,
                'base_url' => _MODULE_DIR_ . $this->name . '/',
                'iso_code' => $this->context->language->iso_code,
                'menu' => $this->getMenu(),
                'html' => $html,
                'errors' => empty($this->errors) ? array() : $this->errors,
            ),
        );

        $this->context->smarty->assign($params);

        $header = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/header.tpl');
        $body = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/body.tpl');
        $footer = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/footer.tpl');

        return $header . $body . $footer;
    }

    protected function getMenu()
    {
        $tab = Tools::getValue('tab_sec');
        $tab_link = $this->context->link->getAdminLink('AdminModules', true)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&tab_sec=';
        return array(
            array(
                'label' => $this->l('Module usage information'),
                'link' => $tab_link . 'help',
                'active' => ($tab == 'help' || empty($tab) ? 1 : 0),
            ),
        );
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name ||
            Tools::getValue('configure') == $this->name ||
            $this->context->controller instanceof ProductController ||
            $this->context->controller instanceof AdminLegacyLayoutControllerCore ||
            $this->context->controller instanceof AdminProductsController
        ) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        if (in_array($this->context->shop->getContext(), array(Shop::CONTEXT_GROUP, Shop::CONTEXT_ALL))) {
            $this->content = $this->l('Please firstly select a shop in the dropdown menu.');
            return $this->content;
        }        
        if (!isset($params['id_product'])) {
            $id_product = Tools::getValue('id_product');
        } else {
            $id_product = $params['id_product'];
        }

        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $this->context->controller->addJquery();
            $this->context->controller->addJqueryPlugin('jgrowl');
        }
        
        $token = Tools::getAdminTokenLite('AdminWicustomproductinfoAjax');
        $text_lang = array();
        $languages = Language::getLanguages(true);
        $ci = WiCustomInfo::newInstance($id_product);
        foreach ($languages as $k => $lang) {            
            foreach ($ci->text as $id_lang => $text) {                
                if ($id_lang == $lang['id_lang']) {
                    $text_lang[$id_lang] = $lang;
                    $text_lang[$id_lang]['text'] = $text;
                }                
            }
        }        
        $hooks_selected = Tools::jsonDecode($ci->hooks);

        $params = array(
            'wi_customproductinfo' => array(
                'text' => $text_lang,
                'hooks' => $this->getAvailableHooks(),
                'hooks_selected' => $hooks_selected,
                'token' => $token,
            )
        );
        
        $this->context->smarty->assign($params);
        return $this->context->smarty->fetch($this->local_path . 'views/templates/hook/displayAdminProductsExtra.tpl');
    }

    public function hookDisplayProductAdditionalInfo($params)
    {        
        if ((int)Tools::getValue('id_product') > 0) {
            return $this->getHookContent(Tools::getValue('id_product'), 'displayProductAdditionalInfo');
        }
    }

    public function hookDisplayProductPriceBlock($params)
    {
        if ($this->context->controller instanceof ProductController &&
            (int)Tools::getValue('id_product') > 0 &&
            $params['type'] == 'after_price'
        ) {
            $id_product = Tools::getValue('id_product');
            return $this->getHookContent($id_product, 'displayProductPriceBlock');            
        }
    }

    public function hookDisplayReassurance($params)
    {
        if ((int)Tools::getValue('id_product') > 0) {
            return $this->getHookContent(Tools::getValue('id_product'), 'displayReassurance');
        }
    }

    protected function getHookContent($id_product = null, $hook = null)
    {
        $id_lang = Context::getContext()->language->id;
        $ci = WiCustomInfo::newInstance($id_product);
        if (Validate::isLoadedObject($ci)) {
            return $ci->getHook($id_product, $hook, $id_lang);
        }
    }

    /**
     * Function to add the controller for AJAX functions.
     */
    public function addTab($module, $tabClass, $id_parent = 0, $title)
    {			
        $tab = new Tab();
        $tab->class_name = $tabClass;
        $tab->id_parent  = $id_parent;
        $tab->module     = $module;
        $languages       = Language::getLanguages();
        foreach ($languages as $language) {
            $tab->name[$language['id_lang']] = $title;
        }
        $tab->add();
		return (int)$tab->id;
    }
}
