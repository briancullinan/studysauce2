jQuery(document).ready(function() {

    var body = $('body');

    function getHash()
    {
        var account = jQuery('#account');
        return account.find('.first-name input').val().trim() + account.find('.last-name input').val().trim() +
            account.find('.email input').val().trim() + account.find('.password input').val() +
            account.find('.new-password input').val();
    }

    function accountFunc() {
        var account = jQuery('#account');
        var valid = true;

        if (getHash() == account.data('state') ||
            account.find('.password input').val() == '' ||
            account.find('.first-name input').val() == '' ||
            account.find('.last-name input').val() == '' ||
            account.find('.email input').val().trim() == '' ||
            !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(account.find('.email input').val()))
            valid = false;

        if (!valid)
            account.find('.form-actions').removeClass('valid').addClass('invalid');
        else {
            account.find('.form-actions').removeClass('invalid').addClass('valid');
            account.find('.form-actions .error').remove();
        }
    }
    body.on('show', '#account', function () {
        $(this).data('state', getHash());
        accountFunc();
    });
    body.on('change', '#account .first-name input, #account .last-name input, #account .email input, ' +
        '#account .password input, #account .new-password input', accountFunc);
    body.on('keyup', '#account .first-name input, #account .last-name input, #account .email input, ' +
        '#account .password input, #account .new-password input', accountFunc);
    body.on('keydown', '#account .first-name input, #account .last-name input, #account .email input, ' +
        '#account .password input, #account .new-password input', accountFunc);

    body.on('click', '#account a[href="#edit-account"]', function (evt) {
        var account = jQuery('#account');
        evt.preventDefault();
        account.find('.account-info').removeClass('read-only').addClass('edit');
        account.find('.password').css('visibility', 'visible');
        account.find('.new-password').css('visibility', 'hidden');
        account.find('.edit-icons').toggle();
        account.find('[value="#save-account"]').show();
    });

    body.on('click', '#account a[href="#edit-password"]', function (evt) {
        var account = jQuery('#account');
        evt.preventDefault();
        account.find('.password,.new-password').css('visibility', 'visible');
        account.find('.edit-icons').toggle();
        account.find('[value="#save-account"]').show();
    });

    body.on('click', '#cancel-confirm a[href="#cancel-account"]', function (evt) {
        var account = jQuery('#account'),
            cancel = $('#cancel-confirm');
        evt.preventDefault();
        if(cancel.is('invalid'))
            return;
        cancel.removeClass('valid').addClass('invalid');
        jQuery.ajax({
            url: window.callbackPaths['cancel_payment'],
            type: 'POST',
            dataType: 'json',
            data: {
                cancel: true,
                csrf_token: account.find('input[name="csrf_token"]').val()
            },
            success: function () {
                // redirected and logged out automatically by server
                $('#cancel-confirm').modal('hide');
                account.find('.type label').replaceWith('<label><span>Account type</span>Free</label>');
            },
            error: function () {
                cancel.removeClass('invalid').addClass('valid');
            }
        })
    });

    function submitAccount()
    {
        var account = jQuery('#account');
        if(account.find('.form-actions').is('.invalid'))
            return;
        account.find('.form-actions').removeClass('valid').addClass('invalid');
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
                if(typeof data.error != 'undefined') {
                    account.find('.form-actions').prepend($('<span class="error">' + data.error + '</span>'));
                }
                account.find('.password input').val('');
                account.find('.new-password input').val('');
                account.find('.edit-icons').toggle();
                account.find('[value="#save-account"]').hide();
                account.find('.account-info').removeClass('edit').addClass('read-only');
                account.find('.password').css('visibility', 'hidden');
                account.find('.new-password').css('visibility', 'hidden');
            }
        });
    }

    body.on('submit', '#account form', function (evt) {
        evt.preventDefault();
        loadingAnimation($(this).find('[value="#save-account"]'));
        setTimeout(submitAccount, 100);
    });

});