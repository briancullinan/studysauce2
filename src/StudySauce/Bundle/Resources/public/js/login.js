jQuery(document).ready(function() {

    var account = jQuery('#account');

    function getHash()
    {
        return account.find('.email input').val().trim() + account.find('.new-password input').val();
    }

    function accountFunc() {
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
    account.data('state', getHash());
    accountFunc();

    account.on('change', '.email input, .password input', accountFunc);
    account.on('keyup', '.email input, .password input', accountFunc);
    account.on('keydown', '.email input, .password input', accountFunc);

    account.on('click', 'a[href="#user-login"]', function (evt) {
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
                if(data.error)
                {
                    account.find('.password').addClass('passwordError');
                }
                account.find('.password input').val('');
            }
        })
    });

});