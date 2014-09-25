$(document).ready(function () {

    $('.landing-home').on('click', 'a[href="#contact-support"]', function (evt)
    {
        evt.preventDefault();

        $('#drupalchat .chatbox').remove();
        if($('#chatpanel .item-list a').length > 0)
        {
            $('#drupalchat').detach().insertAfter($('#node-11'));
            $('#chatpanel .item-list a').first().trigger('click');
        }
    });

});