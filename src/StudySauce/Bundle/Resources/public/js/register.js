jQuery(document).ready(function() {

    var body = $('body');

    function getHash()
    {
        var account = jQuery('#register');
        return account.find('.first-name input').val().trim() + account.find('.last-name input').val().trim() +
        account.find('.email input').val().trim();
    }

    function accountFunc() {
        var account = jQuery('#register');
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
    body.on('show', '#register', function () {
        if($(this).data('state') == null)
            $(this).data('state', getHash());
        accountFunc();
    });
    body.find('#register:visible').trigger('show');

    body.on('change', '#register .first-name input, #register .last-name input, #register .email input, #register .password input', accountFunc);
    body.on('keyup', '#register .first-name input, #register .last-name input, #register .email input, #register .password input', accountFunc);
    body.on('keydown', '#register .first-name input, #register .last-name input, #register .email input, #register .password input', accountFunc);

    body.on('click', '#register a[href="#sign-in-with-email"]', function (evt) {
        var account = jQuery('#register');
        evt.preventDefault();
        $(this).remove();
        account.find('.email,.password,.form-actions').show();
        account.find('.first-name,.last-name').css('display', 'inline-block');
    });

    body.on('click', '#register a[href="#user-register"]', function (evt) {
        var account = jQuery('#register');
        evt.preventDefault();

        if(account.find('.form-actions').is('.invalid'))
            return;
        account.find('.form-actions').removeClass('valid').addClass('invalid');

        account.find('.password').removeClass('passwordError');
        var hash = getHash();

        jQuery.ajax({
            url:window.callbackPaths['account_create'],
            type: 'POST',
            dataType: 'json',
            data: {
                _remember_me: true,
                first: account.find('.first-name input').val(),
                last: account.find('.last-name input').val(),
                email: account.find('.email input').val(),
                pass: account.find('.password input').val(),
                csrf_token: account.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                account.find('input[name="csrf_token"]').val(data.csrf_token);
                account.data('state', hash);
                if(data.error)
                {
                    account.find('.password').addClass('passwordError');
                }
                account.find('.password input').val('');
            }
        })
    });

});