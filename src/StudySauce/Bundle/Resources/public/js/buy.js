
jQuery(document).ready(function($) {
    var body = $('body');

    function checkoutFunc()
    {
        var checkout = $('#checkout');
        var valid = false;
        if(checkout.find('input[name="reoccurs"]:checked').val().trim() != '' &&
            checkout.find('.first-name input').val().trim() != '' &&
            checkout.find('.last-name input').val().trim() != '' &&
            checkout.find('input[name="street1"]').val().trim() != '' &&
            //checkout.find('input[name="street2"]').val().trim() != '' &&
            checkout.find('.city input').val().trim() != '' &&
            checkout.find('.zip input').val().trim() != '' &&
            checkout.find('select[name="state"]').val().trim() != '' &&
            checkout.find('select[name="country"]').val().trim() != '' &&
            checkout.find('input[name="cc-number"]').val().trim() != '' &&
            checkout.find('select[name="cc-month"]').val().trim() != '' &&
            checkout.find('select[name="cc-year"]').val().trim() != '' &&
            checkout.find('input[name="cc-ccv"]').val().trim() != '')
            valid = true;
        if(!valid)
            checkout.find('.form-actions').removeClass('valid').addClass('invalid');
        else {
            checkout.find('.form-actions').removeClass('invalid').addClass('valid');
            checkout.find('.form-actions .error').remove();
        }
    }

    body.on('show', '#checkout', checkoutFunc);
    body.on('change', '#checkout input, #checkout select', checkoutFunc);
    body.on('keyup', '#checkout input[type="text"]', checkoutFunc);

    body.on('click', '#checkout a[href="#show-coupon"]', function (evt) {
        var checkout = $('#checkout');
        evt.preventDefault();
        $(this).hide();
        checkout.find('#coupon-pane')
            .css('display', 'inline-block')
            .css('visibility', 'visible')
            .animate({opacity:1,height: 115});
    });

    body.on('click', '#checkout a[href="#coupon-apply"]', function (evt) {
        var checkout = $('#checkout');
        evt.preventDefault();
        $.ajax({
            url: window.callbackPaths['checkout_coupon'],
            type: 'POST',
            dataType: 'json',
            data: {
                coupon: checkout.find('.coupon-code input').val().trim()
            },
            success: function (data) {
                // this should redirect anyways
                if (typeof data.error != 'undefined') {
                    checkout.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
                }
                else {
                    // set new line item
                    checkout.find('.product-option .radio').first().find('span').html('$' + data.options[0] + '/mo');
                    checkout.find('.product-option .radio').last().find('span').html('$' + data.options[1] + '/year <sup class="premium">Recommended</sup>');

                    // set coupon form
                    checkout.find('.line-item').remove();
                    for(var i = 0; i < data.lines.length; i++) {
                        $('<div class="line-item">' + data.lines[i] + '</div>').appendTo(checkout.find('.product-option'));
                    }

                    // update coupon pane
                    var code = checkout.find('.coupon-code input').val().trim();
                    checkout.find('.coupon-code, a[href="#coupon-apply"]').remove();
                    $('<div class="coupon-code"><strong>' + code + ' - </strong>' + data.lines.join('<br />') + '</div>' +
                    '<a href="#coupon-remove" class="more">Remove</a>').appendTo(checkout.find('#coupon-pane'));
                }
            }
        });
    });

    body.on('click', '#checkout a[href="#coupon-remove"]', function (evt) {
        var checkout = $('#checkout');
        evt.preventDefault();
        $.ajax({
            url: window.callbackPaths['checkout_coupon'],
            type: 'POST',
            dataType: 'json',
            data: {
                remove: true
            },
            success: function (data) {
                // this should redirect anyways
                if (typeof data.error != 'undefined') {
                    checkout.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
                }
                else {
                    // set new line item
                    checkout.find('.product-option .radio').first().find('span').html('$9.99/mo');
                    checkout.find('.product-option .radio').last().find('span').html('$99/year <sup class="premium">Recommended</sup>');

                    // set coupon form
                    checkout.find('.line-item').remove();

                    // update coupon pane
                    checkout.find('.coupon-code, a[href="#coupon-remove"]').remove();
                    $('<div class="coupon-code"><label class="input"><input name="coupon-code" type="text" placeholder="Enter code" value=""></label></div>' +
                    '<a href="#coupon-apply" class="more">Apply to order</a>').appendTo(checkout.find('#coupon-pane'));
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
        $.ajax({
            url: window.callbackPaths['checkout_pay'],
            type: 'POST',
            dataType: 'json',
            data: {
                reoccurs: checkout.find('input[name="reoccurs"]:checked').val().trim(),
                first: checkout.find('.first-name input').val().trim(),
                last: checkout.find('.last-name input').val().trim(),
                email: checkout.find('.email input').val().trim(),
                street1: checkout.find('input[name="street1"]').val().trim(),
                street2: checkout.find('input[name="street2"]').val().trim(),
                city: checkout.find('.city input').val().trim(),
                zip: checkout.find('.zip input').val().trim(),
                state: checkout.find('select[name="state"]').val().trim(),
                country: checkout.find('select[name="country"]').val().trim(),
                number: checkout.find('input[name="cc-number"]').val().trim(),
                month: checkout.find('select[name="cc-month"]').val().trim(),
                year: checkout.find('select[name="cc-year"]').val().trim(),
                ccv: checkout.find('input[name="cc-ccv"]').val().trim()
            },
            success: function (data) {
                // this should redirect anyways
                if(typeof data.error != 'undefined') {
                    checkout.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
                }
            }
        });
    });

});