$(document).ready(function () {
    var partner = jQuery('#partner');

    function getHash()
    {
        return partner.find('.first-name input').val().trim() + partner.find('.last-name input').val().trim() +
                partner.find('.email input').val().trim() +
        $('#partner').find('.permissions input:checked').map(function (i, o) {return jQuery(o).val();}).toArray().join(',');
    }

    function partnerFunc() {
        var valid = true;
        if(getHash() == partner.data('state') ||
            partner.find('.first-name input').val().trim() == '' ||
            partner.find('.last-name input').val().trim() == '' ||
            partner.find('.email input').val().trim() == '' ||
            !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(partner.find('.email input').val()))
            valid = false;
        if(valid)
            partner.find('.form-actions').removeClass('invalid').addClass('valid');
        else
            partner.find('.form-actions').removeClass('valid').addClass('invalid');
    }

    partner.on('change', '.first-name input, .last-name input, .email input', partnerFunc);
    partner.on('keyup', '.first-name input, .last-name input, .email input', partnerFunc);
    partner.on('change', '.permissions input', function () {
        partnerFunc();
        partner.find('a[href="#partner-save"]').first().trigger('click');
    });
    partner.data('state', getHash());
    partnerFunc();

    partner.on('click', 'a[href="#partner-save"]', function (evt) {
        evt.preventDefault();
        if($(this).parent().is('.invalid'))
            return;
        $(this).parent().removeClass('valid').addClass('invalid');

        var hash = getHash();

        jQuery.ajax({
            url: window.callbackPaths['update_partner'],
            type: 'POST',
            dataType: 'json',
            data: {
                first: partner.find('.first-name input').val(),
                last: partner.find('.last-name input').val(),
                email: partner.find('.email input').val(),
                permissions: $('#partner').find('.permissions input:checked').map(function (i, o) {return jQuery(o).val();}).toArray().join(',')
            },
            success: function () {
                partner.data('state', hash);
                $('#partner-sent').modal();
                // update masthead
                // update masthead picture
            }
        });

    });


});