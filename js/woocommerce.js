jQuery(document).ready(function ($) {

    $('#billing_first_name').attr('tabindex', 1);
    $('#billing_last_name').attr('tabindex', 2);
    $('#billing_company').attr('tabindex', 3);
    $('#billing_eu_vat_number').attr('tabindex', 4);
    $('#billing_country').attr('tabindex', 5);
    $('#billing_address_1').attr('tabindex', 6);
    $('#billing_address_2').attr('tabindex', 7);
    $('#billing_city').attr('tabindex', 8);
    $('#billing_state').attr('tabindex', 9);
    $('#billing_postcode').attr('tabindex', 10);
    $('#billing_country_code').attr('tabindex', 11);
    $('#billing_phone').attr('tabindex', 12);
    $('#billing_email').attr('tabindex', 13);
    $('#account_username').attr('tabindex', 14);
    $('#account_password').attr('tabindex', 15);
    $('#account_password2').attr('tabindex', 16);

    if (0 < $('#customer_details').length) {
        $('#customer_details input').each(function (i) {
            show_hide_placeholder(this);
        });
        show_hide_placeholder($('.checkout_coupon input'));
    }

    function show_hide_placeholder(el) {
        let item = el;
        let get_placeholder = $(item).attr('placeholder');
        let res_val = $(item).val();
        if (1 < get_placeholder.length) {
            $(item).closest('.form-row').find('label').remove();
            $(item).removeAttr('placeholder');
            $(item).parent().prepend($(`<label display="block" class="placeholder">${get_placeholder}</label>`));
        }
        if (0 < res_val.length) {
            $(item).parent().find('.placeholder').hide();
        }
    }


    $(document.body).on('keyup', '#customer_details input, #account_password, #account_password2, .checkout_coupon input', function () {
        let current_input = $(this).val();
        if ('' !== current_input && 0 <= current_input.length) {
            $(this).parent().find('label').hide();
        }
        else {
            $(this).parent().find('label').show();
        }
    });

    $(document.body).on('paste', '#customer_details input, #account_password, #account_password2, .checkout_coupon input', function (e) {
        var pastedData = e.originalEvent.clipboardData.getData('text');
        if (0 < pastedData.length) {
            $(this).parent().find('label').hide();
        }
        else {
            $(this).parent().find('label').show();
        }
    });

    $('body').on('click', '#refund_policy_giperlink', function (e) {
        e.preventDefault();
        $('.refund_policy-bg').fadeIn().css('display', 'flex');
        $('.refund_policy-content').slideDown('slow');
        $('body').css('overflow', 'hidden');
    });

    $('.close-refund_policy').on("click", function () {
        $('.refund_policy-bg').fadeOut('slow', function () {
            $(this).css('display', 'none')
        });
        $('.refund_policy-content').slideUp('slow');
        $('body').css('overflow', 'auto');
    });

    $(window).on('resize load scroll', function () {
        locate_error();
    });

    locate_error();

    function locate_error() {
        if (0 < $('.woocommerce-NoticeGroup .woocommerce-notices-wrapper').length) {
            $('.woocommerce-NoticeGroup .woocommerce-notices-wrapper').css('top', $('.elementor-location-header').height());
        }
    }

    //menu hide
    $(document).on('DOMNodeInserted', function (e) {
        if (($(e.target).is('.woocommerce-error')) ||
            ($(e.target).is('.woocommerce-message')) ||
            ($(e.target).is('.woocommerce-alert')) ||
            ($(e.target).is('.woocommerce-NoticeGroup'))) {

            setTimeout(function () {
                $('.woocommerce-error, .woocommerce-alert, .woocommerce-message, .woocommerce-NoticeGroup').fadeOut('fast');
            }, 5000);
        }
    });

    let replace = function () {
        setTimeout(function () {
            if ($('.woocommerce-password-strength').hasClass('short') || $('.woocommerce-password-strength').hasClass('bad')) {
                let text = $('.woocommerce-password-strength').text();
                let my_str = text.replace('-', '');
                $('.woocommerce-password-strength').text(my_str);
            };
        }, 10);

    };

    $('.create-account .input-text').on('keyup click', replace);
    $('.woocommerce-EditAccountForm .input-text').on('keyup click', replace);
    var rounded = function (number) {
        return +number.toFixed(2);
    }
    $(document).on('keyup change', '#password_2, form.edit-account #password_1', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var password = $('#password_1').val(),
            confirm_password = $('#password_2').val();
        if (password === confirm_password) {
            $('.woocommerce-Button').prop('disabled', false);
        } else {
            $('.woocommerce-Button').prop('disabled', true);
        }
    });
    //payment select
    $('body').on('click', '.wc_payment_method', function () {
        $('.select-payment').removeClass('wc_payment_method__active');
        $(this).find('.select-payment').addClass('wc_payment_method__active');
    });

    //checkout page custom event
    $(document.body).on('updated_checkout', function () {
        $('.order-total_custom').remove();
        $('.order-total').show();
        $('.tax-total').show();

        let billing_eu_vat = $('#billing_eu_vat_number').val();
        let billing_country = $('#billing_country').val();

        if (0 < billing_eu_vat.length && "MT" !== billing_country) {
            let subtotal = $('.cart_item .woocommerce-Price-amount').text();
            let disc = $('.cart-discount');
            let total_price_desc = false;
            $('.order-total, .tax-total').hide();
            if (0 < $(disc).length) {
                let currency = $(disc).find('.woocommerce-Price-currencySymbol').text();
                let disc_price = $(disc).find('.woocommerce-Price-amount').text().substring(1);
                subtotal = subtotal.substring(1);
                total_price_desc = `${currency}${(subtotal - disc_price).toFixed(2)}`;
            }
            let order_total_custom = $(`<tr class="order-total_custom"><th>Total</th><td><strong><span class="woocs_special_price_code_custom"><span class="woocommerce-Price-amount_custom amount_custom"><bdi>${total_price_desc ? total_price_desc : subtotal}</bdi></span></span></strong> </td></tr>`);
            $('.woocommerce-checkout-review-order-table tfoot').append(order_total_custom);
        }


        let current_country_code = $('#billing_country_code_field .select2-selection__rendered').text();
        $('#payment_method_paypal').removeAttr('checked');
        $('.wc_payment_method__active').removeClass('wc_payment_method__active');

        let req = $('<abbr class="required" title="required"> *</abbr>')
        $('#billing_country_field .select2-selection__placeholder').text(`Country`);
        $('#billing_country_field .select2-selection__placeholder').append(req);
        let label = $('#billing_state_field').find('label');
        if (0 < $('#billing_state_field').find('select').length) {
            $('#billing_state_field').find('label').remove();
            $('#billing_state_field .select2-selection__placeholder').text(`State / County / Province`);
            $('#billing_state_field .select2-selection__placeholder').append(req);
        }
        else if (0 == $('#billing_state_field').find('select').length && 0 == $(label).length) {
            $('#billing_state_field .woocommerce-input-wrapper').append(`<label display="block" class="placeholder">State / County / Province</label>`);
            $('#billing_state_field .woocommerce-input-wrapper .placeholder').append(req);
        }
        if ($('#billing_state').hasClass('undefined')) {
            $('#billing_state').attr('placeholder', ` `);
        }
        $('.optional').text('*');
        if (0 < $("#billing_country_code_field #billing_country_code").length) {
            var base_url = $('article').attr('attr-url');
            $("#billing_country_code_field #billing_country_code").select2({
                templateResult: formatState,
            });
            function formatState(option) {
                if (option.id !== '0') {
                    if (!option.id) return option.text.toUpperCase();
                    option_image = $(option.element);
                    if (option_image) {
                        if (option.text.toUpperCase() === 'COUNTRY CODE') {
                            option = $(`<span>${option.text}</span>`);
                        } else {
                            option = $(`<span><div class="contry-flag_wrapper"><img class="contry-flag" src="${base_url}/images/flags/${option.id.toLowerCase()}.svg"></div> ${option.text}</span>`);
                        }
                    }
                    return option;
                }
            };
            $('#billing_country_code option').each((index, element) => {
                let result = $(element).text().split(' ');
                $(result).each((i, it) => {
                    if (it == current_country_code) {
                        var base_url = $('article').attr('attr-url');
                        let country_cod = $(element).attr('value');
                        let url = `${base_url}/images/flags/${country_cod.toLowerCase()}.svg`;
                        let img = $(`<img style="margin-right: 2px;" class="contry-flag" src="${url}">`);

                        $('#billing_country_code_field .select2-selection__rendered').text(`${current_country_code}`);
                        $('#billing_country_code_field .select2-selection__rendered').prepend($(img)[0]);
                    }
                });
            });
        }
        $('#billing_country_field .select2-selection').attr('tabindex', 5);
        $('#billing_country_code_field .select2-selection').attr('tabindex', 11);
    });

    $(document.body).on('checkout_error', function () {
        let el = $(this).find('.woocommerce-error');
        $('.woocommerce-notices-wrapper').prepend(el);
    });
    $('.woocommerce-edit-address #billing_city_field label').append($('<span class="required_city">*</span>'));

    //ban on scroll for payment method, woocommerce checkout
    if ($('body').hasClass('woocommerce-add-payment-method' || $('body').hasClass('woocommerce-checkout'))) {
        const targetNode = document.body;
        const config = {
            childList: true,
            subtree: true
        };
        const callback = function (mutationsList, observer) {
            for (let mutation of mutationsList) {
                if (mutation.type === 'childList') {
                    $('html, body').stop();
                }
            }
        };
        const observer = new MutationObserver(callback);
        observer.observe(targetNode, config);
    }

    $(document).on('click', '.woocommerce-privacy-policy-link', function (e) {
        e.preventDefault();
        $('.privacy_policy-bg').fadeIn('slow').css('display', 'flex');
        $('.privacy_policy-content').fadeIn('slow');
        $('body').css('overflow', 'hidden');
    });

    $(document).on('click', '.close-privacy_policy', function () {
        $('.privacy_policy-bg').fadeOut('slow');
        $('.privacy_policy-content').fadeOut('slow');
        $('body').css('overflow', 'auto');
    });

    /*   $(document).on('click', '.menuSet .quiz-submit', function (e) {
           if ($('.quiz-submit').hasClass('complete')) {
               if ($('.popp-finish').length === 0) {
                   e.preventDefault();
                   let classes_string_array = $('body').attr('class').split(' ');
                   var id_less = '';
                   $(classes_string_array).each(function (i, el) {
                       if (el.indexOf('postid-') === 0) {
                           id_less = el.substr(7);
                       }
                   });
                   $.ajax({
                       type: "post",
                       url: "/wp-admin/admin-ajax.php",
                       data: {
                           action: "lesson_item_complate",
                           id: id_less,
                       },
                       success: function () {
                           let progress_bar = $('#sensei_course_progress-2')[0];
                           let lessons = $(progress_bar).find('.completed a');
                           let href = $(lessons).attr('href');
                           window.location.href = href;
                       }
                   });
               }
           }
       });*/

    $('.landing-popp').on('click', function (e) {
        e.preventDefault();

        if ($('.privacy_policy-bg').length > 0) {
            $('.privacy_policy-bg').fadeIn();
            $('.privacy_policy-content').show();
        } else {
            let href = $(this).find('a').attr('href');
            $.ajax({
                type: "post",
                url: "/wp-admin/admin-ajax.php",
                data: {
                    action: "get_post_by_url",
                    url: href,
                },
                success: function (res) {
                    $('body').append($(res)[0]);
                    $($(res)[0]).show();
                    // Start by creating an empty `<script />` tag element
                },
            });
        }
    });

    $('.woocommerce-terms-and-conditions-link').on('click', function (e) {
        e.preventDefault();

        $('.woocommerce-terms-and-conditions-bg-custom').fadeIn().css('display', 'flex');
        $('.woocommerce-terms-and-conditions-custom').fadeIn('slow');
        $('body').css('overflow', 'hidden');
    });

    $('.close-term-cond').on('click', function () {
        $('.woocommerce-terms-and-conditions-bg-custom').fadeOut('slow', function () {
            $(this).css('display', 'none')
        });
        $('.woocommerce-terms-and-conditions-custom').fadeOut('slow');
        $('body').css('overflow', 'auto');
    });

    if ($("#billing_country_code").length > 0) {
        function formatState(option) {
            if (option.id !== '0') {
                if (!option.id) return option.text.toUpperCase();
                option_image = $(option.element);
                if (option_image) {
                    if (option.text.toUpperCase() === 'COUNTRY CODE') {
                        option = $(`<span>${option.text}</span>`);
                    } else {
                        option = $(`<span><div class="contry-flag_wrapper"><img class="contry-flag" src="${base_url}/images/flags/${option.id.toLowerCase()}.svg"></div> ${option.text}</span>`);
                    }
                }
                return option;
            }
        };
        let current_country_code;
        var base_url = $('article').attr('attr-url');
        $("#billing_country_code").select2({
            templateResult: formatState,
            templateSelection: function (data, container) {
                // Add custom attributes to the <option> tag for the selected option
                $(data.element).attr('data-custom-attribute', data.customValue);
                current_country_code = data.text;
            }
        });

        current_country_code = current_country_code.replace(/[^+\d]/g, '')
        $('#billing_country_code option').each((index, element) => {
            let result = $(element).text().split(' ');
            $(result).each((i, it) => {
                if (it == current_country_code) {
                    var base_url = $('article').attr('attr-url');
                    let country_cod = $(element).attr('value');
                    let url = `${base_url}/images/flags/${country_cod.toLowerCase()}.svg`;
                    let img = $(`<img style="margin-right: 2px;" class="contry-flag" src="${url}">`);

                    $('#billing_country_code_field .select2-selection__rendered').text(`${current_country_code}`);
                    $('#billing_country_code_field .select2-selection__rendered').prepend($(img)[0]);
                }
            });
        });
    }

    $('#billing_country_code').on('select2:select', function (e) {
        var data = e.params.data;
        var base_url = $('article').attr('attr-url');

        let url = `${base_url}/images/flags/${data.id.toLowerCase()}.svg`;
        let img = $(`<img style="margin-right: 2px;" class="contry-flag" src="${url}">`);
        $('#billing_country_code_field .select2-selection__rendered').text(`${data.text.replace(/[^+\d]/g, '')}`);
        $('#billing_country_code_field .select2-selection__rendered').prepend($(img)[0]);

    });

    $('#billing_state_field').on('select2:select', function (e) {
        $(this).find('label').remove();
    });

    $(document).on('focus', '.select2.select2-container', function (e) {
        var isOriginalEvent = e.originalEvent;
        var related = e.relatedTarget;
        var isSingleSelect = $(this).find(".select2-selection--single").length > 0;
        if (isOriginalEvent && isSingleSelect && related !== null) {
            $(this).siblings('select:enabled').select2('open');
        }
    });
});
