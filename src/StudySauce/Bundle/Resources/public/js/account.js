jQuery(document).ready(function() {

    var account = jQuery('#account'),
        body = $('body');

    function getHash()
    {
        return account.find('.first-name input').val().trim() + account.find('.last-name input').val().trim() +
            account.find('.email input').val().trim() + account.find('.new-password input').val();
    }

    function accountFunc() {
        var valid = true;

        if (getHash() == account.data('state') ||
            account.find('.first-name input').val() == '' ||
            account.find('.last-name input').val() == '' ||
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

    body.on('change', '#account .first-name input, #account .last-name input, #account .email input, ' +
        '#account .password input, #account .new-password input', accountFunc);
    body.on('keyup', '#account .first-name input, #account .last-name input, #account .email input, ' +
        '#account .password input, #account .new-password input', accountFunc);
    body.on('keydown', '#account .first-name input, #account .last-name input, #account .email input, ' +
        '#account .password input, #account .new-password input', accountFunc);

    body.on('click', '#account a[href="#edit-account"]', function (evt) {
        evt.preventDefault();
        account.find('.account-info').removeClass('read-only').addClass('edit');
        account.find('.new-password').hide();
        account.find('.pass-info').show();
    });

    body.on('click', '#account a[href="#edit-password"]', function (evt) {
        evt.preventDefault();
        account.find('.new-password').show();
        account.find('.pass-info').show();
    });

    body.on('click', '#account a[href="#cancel-account"]', function (evt) {
        evt.preventDefault();
        jQuery.ajax({
            url: window.callbackPaths['account_remove'],
            type: 'POST',
            dataType: 'json',
            data: {
                cancel: true,
                csrf_token: account.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                account.find('input[name="csrf_token"]').val(data.csrf_token);
                window.location = '/';
            }
        })
    });

    body.on('click', '#account a[href="#save-account"]', function (evt) {
        evt.preventDefault();

        if(account.find('.form-actions').is('.invalid'))
            return;
        account.find('.form-actions').removeClass('valid').addClass('invalid');

        account.find('.password').removeClass('passwordError');
        var hash = getHash();

        jQuery.ajax({
            url:window.callbackPaths['account_update'],
            type: 'POST',
            dataType: 'json',
            data: {
                first: account.find('.first-name input').val(),
                last: account.find('.last-name input').val(),
                email: account.find('.email input').val(),
                pass: account.find('.password input').val(),
                newPass: account.find('.new-password input').val(),
                csrf_token: account.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                account.find('input[name="csrf_token"]').val(data.csrf_token);
                account.data('state', hash);
                if(data.password_error)
                {
                    account.find('.password').addClass('passwordError');
                }
                account.find('.password input').val('');
                account.find('.new-password input').val('');
            }
        })
    });

});