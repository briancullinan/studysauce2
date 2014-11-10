jQuery(document).ready(function() {

    var body = $('body');

    function accountFunc() {
        var account = jQuery('#login');
        var valid = true;

        if (account.find('.email input').val().trim() == '' ||
            !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(account.find('.email input').val()))
            valid = false;

        if (!valid)
            account.find('.form-actions').removeClass('valid').addClass('invalid');
        else {
            account.find('.form-actions').removeClass('invalid').addClass('valid');
            account.find('.form-actions .error').remove();
        }
    }

    body.on('click', '#login a[href="#sign-in-with-email"]', function (evt) {
        var account = jQuery('#login');
        evt.preventDefault();
        $(this).remove();
        account.find('.email,.password,.form-actions').show();
    });

    body.on('change', '#login .email input, #login .password input', accountFunc);
    body.on('keyup', '#login .email input, #login .password input', accountFunc);
    body.on('keydown', '#login .email input, #login .password input', accountFunc);

    body.on('click', '#login a[href="#user-login"]', function (evt) {
        var account = jQuery('#login');
        evt.preventDefault();

        if(account.find('.form-actions').is('.invalid'))
            return;
        account.find('.form-actions').removeClass('valid').addClass('invalid');

        jQuery.ajax({
            url:window.callbackPaths['account_auth'],
            type: 'POST',
            dataType: 'json',
            data: {
                _remember_me: true,
                email: account.find('.email input').val(),
                pass: account.find('.password input').val(),
                csrf_token: account.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                account.find('input[name="csrf_token"]').val(data.csrf_token);
                if(typeof data.redirect != 'undefined' && (/\/login/i).test(data.redirect))
                {
                    account.find('.form-actions').prepend($('<span class="error">Invalid password</span>'));
                }
                account.find('.password input').val('');
            }
        })
    });

});