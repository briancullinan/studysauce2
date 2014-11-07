$(document).ready(function () {

    var body = $('body');

    function goalsFunc() {
        var goals = $('#goals');
        jQuery(this).each(function () {
            var row = $(this).closest('.goal-row'),
                valid = true;
            if(row.find('select:visible').val() == '_none' ||
                row.find('.reward textarea').val().trim() == '')
                valid = false;
            if(!valid)
                row.removeClass('valid').addClass('invalid');
            else
                row.removeClass('invalid').addClass('valid');
        });
        if(goals.find('.goal-row.valid.edit:visible, .goal-row.edit.valid:visible').length > 0)
            goals.find('.form-actions').removeClass('invalid').addClass('valid');
        else
            goals.find('.form-actions').removeClass('valid').addClass('invalid');
    }

    body.on('click', '#goals a[href="#claim"]', function (evt) {
        evt.preventDefault();
        var row = jQuery(this).parents('.goal-row'),
            gid = (/gid([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        var claim = $('#claim').modal({
            backdrop: 'static',
            keyboard: false,
            modalOverflow: true
        });
        claim.addClass('gid' + gid);
        if(claim.find('.plupload').is('.init'))
            return;
        var upload = new plupload.Uploader({
            chunk_size: '5MB',
            runtimes : 'html5,flash,silverlight,html4',
            browse_button : 'goals-plupload-select', // you can pass in id...
            container: claim.find('.plupload')[0], // ... or DOM Element itself
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
                    claim.find('.plupload').addClass('init');
                    claim.find('#goals-plupload-select').on('click', function () {
                        up.splice();
                    });
                },
                FilesAdded: function(up) {
                    up.start();
                },
                UploadProgress: function(up) {
                    var squiggle;
                    if((squiggle = claim.find('.squiggle')).length == 0)
                        squiggle = $('<small class="squiggle">&nbsp;</small>').appendTo(claim.find('.plup-filelist'));
                    squiggle.stop().animate({width: up.total.percent + '%'}, 1000, 'swing');
                },
                FileUploaded: function(up, file, response) {
                    var data = JSON.parse(response.response);
                    claim.find('input[name="goals-plupload"]').val(data.fid);
                    claim.find('.plup-filelist .squiggle').stop().remove();
                    claim.find('.plupload img').attr('src', data.src);
                },
                Error: function(up, err) {
                }
            }
        });

        setTimeout(function () {upload.init();}, 200);
    });

    body.on('click', '#goals .goal-row a[href="#goal-edit"]', function (evt) {
        var goals = $('#goals');
        evt.preventDefault();
        var row = $(this).parents('.goal-row');
        goalsFunc.apply(row.removeClass('read-only').addClass('edit'));
        goals.find('.form-actions').css('visibility', 'visible');
    });

    body.on('change', '#goals .goal-row select, #goals .goal-row textarea', function () {
        goalsFunc.apply(jQuery(this).parents('.goal-row'));
    });
    body.on('keyup', '#goals .goal-row textarea', function () {
        goalsFunc.apply(jQuery(this).parents('.goal-row'));
    });

    body.on('click', '#goals a[href="#save-goal"]', function (evt) {
        var goals = $('#goals');
        evt.preventDefault();
        if(goals.find('.form-actions').is('.invalid'))
            return;
        goals.find('.form-actions').removeClass('valid').addClass('invalid');

        var goalRows = [];
        goals.find('.goal-row.edit.valid:visible, .class-row.valid.edit:visible').each(function () {
            var row = $(this);
            goalRows[goalRows.length] = {
                type: row.find('.behavior, .milestone, .outcome').attr('class'),
                value: row.find('.behavior select, .milestone select, .outcome select').val(),
                reward: row.find('.reward textarea').val()
            };
        });

        $.ajax({
            url: window.callbackPaths['update_goals'],
            type: 'POST',
            dataType: 'text',
            data: {
                goals: goalRows,
                csrf_token: goals.find('input[name="csrf_token"]').val()
            },
            success: function (data) {
                var response = $(data);
                goals.find('input[name="csrf_token"]').val(response.find('input[name="csrf_token"]').val());

                goals.find('.goal-row').remove();
                if($(response).find('.goal-row').insertAfter(goals.find('header')).length == 3)
                    goals.find('.form-actions').css('visibility', 'hidden');
                else
                    goals.find('.form-actions').css('visibility', 'visible');
                // TODO: make new rows fade in to place

                goalsFunc.apply(goals.find('.goal-row'));
            },
            error: function () {
            }
        });
    });

    goalsFunc.apply(goals.find('.goal-row'));

    body.on('click', '#claim a[href="#submit-claim"]', function (evt) {
        evt.preventDefault();
        var claim = jQuery('#claim'),
            gid = (/gid([0-9]+)(\s|$)/ig).exec(claim.attr('class'))[1];

        if(claim.is('.invalid'))
            return;
        claim.addClass('invalid');

        $.ajax({
            url: window.callbackPaths['claim_goals'],
            type: 'POST',
            dataType: 'json',
            data: {
                id: gid,
                photo: claim.find('input[name="goals-plupload"]').val(),
                message: claim.find('textarea').val()
            },
            success: function (data) {
                claim.removeClass('invalid');
                claim.removeClass('gid' + gid);
                claim.modal('hide');

                // clear uploads
                claim.find('.plupload img').not(claim.find('.plupload img').first()).remove();
                claim.find('.plupload img').first().attr('src', window.callbackPaths['_welcome'] + 'bundles/studysauce/images/upload.png');
                claim.find('input[name="goals-plupload"]').val('');
                claim.find('textarea').val('');

                // update achievements
                jQuery('#achievements > *').remove();
                jQuery(data.achievements).appendTo(jQuery('#achievements'));
            }
        });
    });
});