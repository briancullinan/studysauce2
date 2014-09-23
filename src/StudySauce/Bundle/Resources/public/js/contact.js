jQuery(document).ready(function () {

    jQuery('.landing-home').on('click', 'a[href="#contact-support"]', function (evt)
    {
        evt.preventDefault();

        jQuery('#drupalchat .chatbox').remove();
        if(jQuery('#chatpanel .item-list a').length > 0)
        {
            jQuery('#drupalchat').detach().insertAfter(jQuery('#node-11'));
            jQuery('#chatpanel .item-list a').first().trigger('click');
        }
    });

});