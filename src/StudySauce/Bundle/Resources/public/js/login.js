jQuery(document).ready(function() {

    var body = $('body');

    function getHash()
    {
        var account = jQuery('#login');
        return account.find('.email input').val().trim() + account.find('.new-password input').val();
    }

    function accountFunc() {
        var account = jQuery('#login');
        var valid = true;

        if (getHash() == account.data('state') ||
            account.find('.email input').val().trim() == '' ||
            !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(account.find('.email input').val()))
            valid = false;

        if (!valid)
            account.find('.form-actions').removeClass('valid').addClass('invalid');
        else
            account.find('.form-actions').removeClass('invalid').addClass('valid');
    }
    body.on('show', '#login', function () {
        $(this).data('state', getHash());
        accountFunc();
    });
    body.find('#login:visible').trigger('show');

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

        account.find('.password').removeClass('passwordError');
        var hash = getHash();

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
                account.data('state', hash);
                account.find('input[name="csrf_token"]').val(data.csrf_token);
                if(typeof data.redirect != 'undefined' && (/\/login/i).test(data.redirect))
                {
                    account.find('.password').addClass('passwordError');
                }
                account.find('.password input').val('');
            }
        })
    });

});