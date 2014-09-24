jQuery(document).ready(function () {

    var body = jQuery('body');

    // look at every link with a hash, store with google analytics and search for dialogs.
    body.on('click', 'a[href^="#"]', function (evt)
    {
        var that = jQuery(this),
            link = that.attr('href');
        if(jQuery(link).length > 0 && jQuery(link).is('.dialog'))
        {
            evt.preventDefault();
            jQuery(link).parent('.fixed-centered').show();
            jQuery(link).show(500);
        }
    });

    body.on('click', '.dialog a[href="#close"]', function (evt)
    {
        evt.preventDefault();
        jQuery(this).parents('.dialog').hide(500, function () {
            jQuery(this).parent('.fixed-centered').hide();
        });
    });

});
