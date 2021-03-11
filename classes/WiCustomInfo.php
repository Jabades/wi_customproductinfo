<?php

class WiCustomInfo extends ObjectModel
{
    public $id_product;
    public $hooks;
    public $text;
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'wi_customproductinfo',
        'primary' => 'id_wi_customproductinfo',
        'multilang' => true,
        'fields' => array(
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'hooks' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            /* Lang fields */
            'text' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml'
            ),
        ),
    );

    public static function newInstance($id_product = null)
    {
        $sql = 'SELECT `' . self::$definition['primary'] . '` 
            FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` 
            WHERE `id_product` = ' . (int)$id_product;
        $id = Db::getInstance()->getValue($sql);
        return new WiCustomInfo($id);
    }

    public function getHook($id_product = null, $hook = null, $id_lang = null)
    {
        $sql = 'SELECT `wcil`.`text` FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` `wci` 
            JOIN `' . _DB_PREFIX_ . self::$definition['table'] . '_lang` `wcil` 
                ON `wci`.`' . self::$definition['primary'] . '` = `wcil`.`' . self::$definition['primary'] . '` 
            WHERE `wci`.`id_product` = ' . (int) $id_product .' AND `wcil`.`id_lang` = ' . (int) $id_lang . ' 
            AND `wci`.`hooks` LIKE "%\"' . $hook . '\"%"';
        return Db::getInstance()->getValue($sql);
    }
}