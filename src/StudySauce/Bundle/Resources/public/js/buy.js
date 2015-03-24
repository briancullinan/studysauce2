
jQuery(document).ready(function($) {
    var body = $('body');

    function checkoutFunc()
    {
        var checkout = $('#checkout');
        var valid = false,
            valid2 = false;
        if(checkout.find('input[name="reoccurs"]:checked').val().trim() != '' &&
            checkout.find('#billing-pane .first-name input').val().trim() != '' &&
            checkout.find('#billing-pane .last-name input').val().trim() != '' &&
            checkout.find('#billing-pane .email input').val().trim() != '' &&
            (/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(checkout.find('#billing-pane .email input').val()) &&
            checkout.find('input[name="street1"]').val().trim() != '' &&
            //checkout.find('input[name="street2"]').val().trim() != '' &&
            checkout.find('.city input').val().trim() != '' &&
            checkout.find('.zip input').val().trim() != '' &&
            checkout.find('select[name="state"]').val().trim() != '' &&
            checkout.find('select[name="country"]').val().trim() != '' &&
            checkout.find('input[name="cc-number"]').val().trim() != '' &&
            checkout.find('select[name="cc-month"]').val().trim() != '' &&
            checkout.find('select[name="cc-year"]').val().trim() != '' &&
            checkout.find('input[name="cc-ccv"]').val().trim() != '' &&
            (checkout.find('input[name="password"]:visible').length == 0 || checkout.find('input[name="password"]:visible').val().trim() != ''))
            valid = true;
        if(!checkout.find('#gift-pane').is(':visible') ||
            // checked if everything is blank, that's ok too
            (
                !checkout.find('#gift-pane').is('.shown-by-default') &&
                checkout.find('#gift-pane .first-name input').val().trim() == '' &&
                checkout.find('#gift-pane .last-name input').val().trim() == '' &&
                checkout.find('#gift-pane .email input').val().trim() == ''
            ) ||
            checkout.find('#gift-pane .first-name input').val().trim() != '' &&
            checkout.find('#gift-pane .last-name input').val().trim() != '' &&
            checkout.find('#gift-pane .email input').val().trim() != '' &&
            (/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(checkout.find('#gift-pane .email input').val()))
            valid2 = true;
        if(!valid || !valid2)
            checkout.find('.form-actions').removeClass('valid').addClass('invalid');
        else {
            checkout.find('.form-actions').removeClass('invalid').addClass('valid');
            checkout.find('.form-actions .error').remove();
        }
    }

    body.on('show', '#checkout', checkoutFunc);
    body.on('change', '#checkout input, #checkout select', checkoutFunc);
    body.on('keyup', '#checkout input[type="text"], #checkout input[type="password"]', checkoutFunc);

    body.on('click', '#checkout a[href="#show-coupon"]', function (evt) {
        var checkout = $('#checkout');
        evt.preventDefault();
        $(this).hide();
        checkout.find('#coupon-pane')
            .css('display', 'inline-block')
            .css('visibility', 'visible')
            .animate({opacity:1,height: 100});
    });

    body.on('click', '#checkout a[href="#show-gift"]', function (evt) {
        var checkout = $('#checkout');
        evt.preventDefault();
        $(this).hide();
        checkout.find('#gift-pane')
            .css('display', 'inline-block')
            .css('visibility', 'visible')
            .animate({opacity:1,height: 166});
    });

    body.on('click', '#checkout a[href="#coupon-apply"]', function (evt) {
        var checkout = $('#checkout');
        evt.preventDefault();
        checkout.find('.form-actions .error').remove();
        $.ajax({
            url: window.callbackPaths['checkout_coupon'],
            type: 'POST',
            dataType: 'text',
            data: {
                coupon: checkout.find('.coupon-code input').val().trim()
            },
            success: function (data) {
                var content = $(data);
                if (typeof data.error != 'undefined') {
                    checkout.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
                }
                else {
                    // set new line item
                    checkout.find('.product-option').replaceWith(content.find('.product-option'));

                    // update coupon pane
                    checkout.find('#coupon-pane').html(content.find('#coupon-pane').html());
                }
            }
        });
    });

    body.on('click', '#checkout a[href="#coupon-remove"]', function (evt) {
        var checkout = $('#checkout');
        evt.preventDefault();
        checkout.find('.form-actions .error').remove();
        $.ajax({
            url: window.callbackPaths['checkout_coupon'],
            type: 'POST',
            dataType: 'text',
            data: {
                remove: true
            },
            success: function (data) {
                var content = $(data);
                if (typeof data.error != 'undefined') {
                    checkout.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
                }
                else {
                    // set new line item
                    checkout.find('.product-option').replaceWith(content.find('.product-option'));

                    // update coupon pane
                    checkout.find('#coupon-pane').html(content.find('#coupon-pane').html());
                }
            }
        });
    });

    body.on('click', '#checkout a[href="#submit-order"]', function (evt) {
        var checkout = $('#checkout');
        evt.preventDefault();
        if(!checkout.find('.form-actions').is('.valid'))
            return;

        checkout.find('.form-actions').removeClass('valid').addClass('invalid');
        loadingAnimation(checkout.find('a[href="#submit-order"]'));

        $.ajax({
            url: window.callbackPaths['checkout_pay'],
            type: 'POST',
            dataType: 'json',
            data: {
                reoccurs: checkout.find('input[name="reoccurs"]:checked').val().trim(),
                first: checkout.find('#billing-pane .first-name input').val().trim(),
                last: checkout.find('#billing-pane .last-name input').val().trim(),
                email: checkout.find('#billing-pane .email input').val().trim(),
                pass: checkout.find('input[name="password"]:visible').length == 0 ? null : checkout.find('input[name="password"]:visible').val(),
                street1: checkout.find('input[name="street1"]').val().trim(),
                street2: checkout.find('input[name="street2"]').val().trim(),
                city: checkout.find('.city input').val().trim(),
                zip: checkout.find('.zip input').val().trim(),
                state: checkout.find('select[name="state"]').val().trim(),
                country: checkout.find('select[name="country"]').val().trim(),
                number: checkout.find('input[name="cc-number"]').val().trim(),
                month: checkout.find('select[name="cc-month"]').val().trim(),
                year: checkout.find('select[name="cc-year"]').val().trim(),
                ccv: checkout.find('input[name="cc-ccv"]').val().trim(),
                invite: checkout.find('#gift-pane').is(':visible') ? {
                    first: checkout.find('#gift-pane .first-name input').val().trim(),
                    last: checkout.find('#gift-pane .last-name input').val().trim(),
                    email: checkout.find('#gift-pane .email input').val().trim()
                } : null
            },
            success: function (data) {
                checkout.find('.squiggle').stop().remove();
                // this should redirect anyways
                if(typeof data.error != 'undefined') {
                    checkout.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
                }
            },
            error: function () {
                checkout.find('.squiggle').stop().remove();
            }
        });
    });

});