{*
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
*}
<div class="row">
    <div class="col-md-12">
        <h2>{l s='Set the custom text block.' mod='wi_customproductinfo'}</h2>
        <div class="translations tabbable">
            <div class="translationsFields tab-content">
                {foreach $wi_customproductinfo.text as $lang}
                    <div data-locale="{$lang.iso_code|escape:'htmlall':'UTF-8'}" class="translation-field translation-label-{$lang.iso_code|escape:'htmlall':'UTF-8'}{if $lang.id_lang == $id_lang} show active{/if}">
                        <textarea id="WI_CUSTOMPRODUCTINFO_TEXT_{$lang.id_lang|intval}" class="form-control autoload_rte wi_customproductinfo_text" data-lang="{$lang.id_lang|intval}">{$lang.text|escape:'htmlall':'UTF-8'}</textarea>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
    <div class="col-md-12">        
        <h2>{l s='Select the hooks where you wish this text will be displayed.' mod='wi_customproductinfo'}</h2>
        <div class="btn-group" role="group" aria-label="hooks">
            {assign var=pos value=1}
            {foreach $wi_customproductinfo.hooks as $hook => $desc key=i}
                <button type="button" class="btn btn-hook {if in_array($hook, $wi_customproductinfo.hooks_selected)}btn-primary active{else}btn-default{/if}" data-target="WI_CUSTOMPRODUCTINFO_HOOKS_{$pos|intval}">
                    {$desc|escape:'html':'UTF-8'}
                </button>
                {assign var=pos value=$pos+1}
            {/foreach}
        </div>
        <div style="visibility:hidden;height:0px">
        {assign var=pos value=1}
        {foreach $wi_customproductinfo.hooks as $hook => $desc key=i}
            <input type="checkbox" 
                name="WI_CUSTOMPRODUCTINFO_HOOKS" 
                id="WI_CUSTOMPRODUCTINFO_HOOKS_{$pos|intval}" 
                value="{$hook|escape:'html':'UTF-8'}" {if in_array($hook, $wi_customproductinfo.hooks_selected)}checked="checked"{/if}
            >
            {assign var=pos value=$pos+1}
        {/foreach}
        </div>
        <script>
        var wi_token = '{$wi_customproductinfo.token|escape:'html':'UTF-8'}';
        window.addEventListener('load',function() {
            $(document).on('click', '.btn-hook', function() {
                var id = $(this).data('target');
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active').removeClass('btn-primary');
                    $(this).addClass('btn-default');
                    $('#'+id).removeAttr('checked');
                } else {
                    $('#'+id).attr('checked', 'checked');
                    $(this).addClass('btn-primary');
                    $(this).removeClass('btn-default');
                    $(this).addClass('active');
                }
            });
        });
        </script>
    </div>
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-primary" id="wi-customproductinfo-save">{l s='Save' mod='wi_customproductinfo'}</button>
    </div>
</div>
