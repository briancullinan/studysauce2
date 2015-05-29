$(document).ready(function () {
    var body = $('body');

    function getHash()
    {
        var partner = jQuery('#partner');
        return partner.find('.first-name input').val().trim() + partner.find('.last-name input').val().trim()
            + partner.find('.email input').val().trim()
            + $('#partner').find('.permissions input:checked').map(function (i, o) {
                return jQuery(o).val();}).toArray().join(',')
            + partner.find('input[name="partner-plupload"]').val();
    }

    function partnerFunc() {
        var partner = jQuery('#partner');
        var valid = true;
        if(partner.find('.first-name input').val().trim() == '') {
            partner.addClass('first-required');
        }
        else {
            partner.removeClass('first-required');
        }
        if(partner.find('.last-name input').val().trim() == '') {
            partner.addClass('last-required');
        }
        else {
            partner.removeClass('last-required');
        }
        if(partner.find('.email input').val().trim() == '' ||
            !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(partner.find('.email input').val())) {
            partner.addClass('email-required');
        }
        else {
            partner.removeClass('email-required');
        }
        if(getHash() == partner.data('state') || partner.is('.first-required') || partner.is('.last-required') ||
            partner.is('.email-required'))
            partner.find('.form-actions').removeClass('valid').addClass('invalid');
        else
            partner.removeClass('.invalid-only').find('.form-actions').removeClass('invalid').addClass('valid');
    }

    body.on('change', '#partner .first-name input, #partner .last-name input, #partner .email input, #partner .permissions input', partnerFunc);
    body.on('keyup', '#partner .first-name input, #partner .last-name input, #partner .email input', partnerFunc);
    body.on('change', '#partner .permissions input', function () {
        submitPartner(false);
    });
    body.on('show', '#partner', function () {
        var partner = $(this);
        if(partner.data('state') == null) {
            partner.data('state', getHash());

            var upload = new plupload.Uploader({
                chunk_size: '5MB',
                runtimes : 'html5,flash,silverlight,html4',
                browse_button : 'partner-upload-select', // you can pass in id...
                container: partner.find('.plupload')[0], // ... or DOM Element itself
                url : window.callbackPaths['file_create'],
                unique_names: true,
                max_files: 1,
                multipart: false,
                multiple_queues: true,
                urlstream_upload: false,
                filters : {
                    max_file_size : '1gb',
                    mime_types: [
                        {
//                        title : "Video files",
//                        extensions : "mov,avi,mpg,mpeg,wmv,mp4,webm,flv,m4v,mkv,ogv,ogg,rm,rmvb,m4v"
                            title : "Image files",
                            extensions : "jpg,jpeg,gif,png,bmp,tiff"
                        }
                    ]
                },
                flash_swf_url : window.callbackPaths['_welcome'] + 'bundles/studysauce/js/plupload/js/Moxie.swf',
                silverlight_xap_url : window.callbackPaths['_welcome'] + 'bundles/studysauce/js/plupload/js/Moxie.xap',
                init: {
                    PostInit: function(up) {
                        partner.find('.plupload').addClass('init');
                        partner.find('#partner-upload-select').on('click', function () {
                            up.splice();
                        });
                    },
                    FilesAdded: function(up) {
                        up.start();
                    },
                    UploadProgress: function(up) {
                        var squiggle;
                        if((squiggle = partner.find('.squiggle')).length == 0)
                            squiggle = $('<small class="squiggle">&nbsp;</small>').appendTo(partner.find('.plup-filelist'));
                        squiggle.stop().animate({width: up.total.percent + '%'}, 1000, 'swing');
                    },
                    FileUploaded: function(up, file, response) {
                        var data = JSON.parse(response.response);
                        partner.find('input[name="partner-plupload"]').val(data.fid);
                        partner.find('.plup-filelist .squiggle').stop().remove();
                        partner.find('.plupload img').attr('src', data.src);
                        // update masthead picture
                        body.find('#partner-message img').attr('src', data.src);
                        partnerFunc();
                        submitPartner(false);
                    },
                    Error: function(up, err) {
                    }
                }
            });

            setTimeout(function () {upload.init();}, 200);
        }
        partnerFunc();
    });

    function submitPartner(showConfirmation)
    {
        var partner = jQuery('#partner');
        if(partner.find('.form-actions').is('.invalid') || partner.find('#partner-invite').is('read-only')) {
            if(partner.is('.first-required') || partner.is('.last-required') || partner.is('.email-required')) {
                partner.addClass('invalid-only');
                if(partner.is('.first-required')) {
                    partner.find('.first-name input').focus();
                }
                else if(partner.is('.last-required')) {
                    partner.find('.last-name input').focus();
                }
                else if(partner.is('.email-required')) {
                    partner.find('.email input').focus();
                }
            }
            return;
        }
        partner.find('.form-actions').removeClass('valid').addClass('invalid');
        loadingAnimation(partner.find('[value="#partner-save"]'));
        var hash = getHash();
        jQuery.ajax({
            url: window.callbackPaths['partner_update'],
            type: 'POST',
            dataType: 'json',
            data: {
                photo: partner.find('input[name="partner-plupload"]').val(),
                first: partner.find('.first-name input').val(),
                last: partner.find('.last-name input').val(),
                email: partner.find('.email input').val(),
                permissions: $('#partner').find('.permissions input:checked').map(function (i, o) {return jQuery(o).val();}).toArray().join(',')
            },
            success: function () {
                partner.find('.squiggle').stop().remove();
                partner.data('state', hash);
                if(showConfirmation)
                    $('#partner-confirm').modal({show:true});
                // update masthead
                body.find('#partner-message > div > span, #partner-message > div > a')
                    .first().replaceWith('<span>' + partner.find('.first-name input').val() + ' ' + partner.find('.last-name input').val() + '</span>');

            },
            error: function () {
                partner.find('.squiggle').stop().remove();
            }
        });
    }
    body.on('submit', '#partner form', function (evt) {
        evt.preventDefault();
        submitPartner.apply(true);
    });

});