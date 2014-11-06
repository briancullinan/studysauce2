
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

    body.on('loaded', '#checkout', function () {
        checkoutFunc();
    });

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
                if(typeof data.error != 'undefined')
                    checkout.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
            }
        });
    });

});