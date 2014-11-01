$(document).ready(function () {
    var body = $('body'),
        partner = jQuery('#partner');

    function getHash()
    {
        return partner.find('.first-name input').val().trim() + partner.find('.last-name input').val().trim() +
                partner.find('.email input').val().trim() +
        $('#partner').find('.permissions input:checked').map(function (i, o) {return jQuery(o).val();}).toArray().join(',') +
            partner.find('input[name="partner-plupload"]').val();
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

    body.on('change', '#partner .first-name input, #partner .last-name input, #partner .email input', partnerFunc);
    body.on('keyup', '#partner .first-name input, #partner .last-name input, #partner .email input', partnerFunc);
    body.on('change', '#partner .permissions input', function () {
        partnerFunc();
        partner.find('a[href="#partner-save"]').first().trigger('click');
    });
    partner.data('state', getHash());
    partnerFunc();

    body.on('click', '#partner a[href="#partner-save"]', function (evt) {
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
                photo: partner.find('input[name="partner-plupload"]').val(),
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
                partnerFunc();
                partner.find('a[href="#partner-save"]').trigger('click');
            },
            Error: function(up, err) {
            }
        }
    });

    setTimeout(function () {upload.init();}, 200);
});