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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

window.addEventListener('load',function() {
    $(document).on('click', '#wi-customproductinfo-save', function() {
        if ($('input[name="id_product"]').length) {
            var id_product = $('input[name="id_product"]').val();
        } else {
            var id_product = $('#form_id_product').val();
        }
        var text = {};
        $('.wi_customproductinfo_text').each(function(i) {
            var id_lang = $(this).data('lang');
            text[id_lang] = $(this).val();
            console.log(id_lang);
        });
        var hooks = [];
		$("input[name='WI_CUSTOMPRODUCTINFO_HOOKS']:checked").each(function(i) {
			hooks.push($(this).val());
		});
        $.ajax({
            url: './index.php',
            method: 'post',
            dataType: 'json',
            cache: false,
            data: {
                'ajax': 1,
                'controller': 'AdminWicustomproductinfoAjax',
                'module_name': 'wi_customproductinfo',
                'configure': 'wi_customproductinfo',
                'action': 'saveProductInfo',
                'id_product': id_product,
                'text': text,
                'hooks': hooks,
                'token': wi_token,
                'rand': new Date().getTime()
            },
            success: function (response) {
                /*
                if (action == 'import') {
                    if (100 > response.percent) {
                        wiProcessProducts(action);
                    }
                    $('.wi-percent-complete').css('width', response.percent+'%');                
                } else {
                    $('.lds-dual-ring').css('display', 'none');
                }
                */
            },
            error: function (response) {
                console.log("error");
            }
        });
    });
});