$(document).ready(function () {
    var body = $('body'),
        plans = $('#plan');

    /*
     function showFirst(type)
     {
     setTimeout(function () {
     var row = plans.find('.session-row.default-' + type + ':visible').first();
     if(!row.is('.selected'))
     row.find('.field-name-field-assignment').trigger('click');
     row.scrollintoview({padding: {top:120,bottom:100,left:0,right:0}});
     }, 500);
     }
     */

    function videoPoller(fid, eid) {
        var row = plans.find('.session-row.eid' + eid);
        jQuery.ajax({
            url: '/aws/checkstatus',
            type: 'POST',
            dataType: 'json',
            data: {
                fid: fid
            },
            success: function (file) {
                if(file == 'error')
                    return;
                if(file != null && typeof file['src'] != 'undefined')
                {
                    row.find('img[src*="empty-play.png"]').remove();
                    var thumb = '<video width="184" height="184" preload="auto" controls="controls" poster="https://s3-us-west-2.amazonaws.com/studysauce/' + file.thumb + '">' +
                        '<source src="https://s3-us-west-2.amazonaws.com/studysauce/' + file.src + '" type="video/webm" />';
                    row.addClass('uploaded');
                    row.find('.plup-progress').hide();
                    row.find('.plup-list li').append('<div class="plup-thumb-wrapper">' + thumb + '</div>');
                    if(!row.find('video')[0].canPlayType('video/webm; codecs="vp8, vorbis"'))
                    {
                        jQuery('<div><a href="https://tools.google.com/dlpage/webmmf/">Can\'t play the video? Click here to install.</a></div>').insertAfter(jQuery('#plan-' + eid + '-plupload').find('video'));
                    }
                }
                else
                    setTimeout('videoPoller("' + fid + '", "' + eid + '");', 5000);
            }
        });
    }

    function renderStrategies()
    {
        var row = jQuery(this).closest('.session-row'),
            that = row.find('select[name="strategy-select"]'),
            strategy = plans.find('.strategy-' + that.val()).length == 0 // make sure this type of strategy still exists
                ? (/default-([a-z]*)(\s|$)/ig).exec(row.attr('class'))[1]
                : that.val(),
            eid = row.attr('id').substring(4),
            classname = row.find('.class-name').text();

        // add strategy if they haven't used it before
        if(row.find('.strategy-' + strategy).length == 0 && plans.find('.strategy-' + strategy).length > 0)
        {
            var newStrategy = plans.find('.strategy-' + strategy).first().clone();
            row.append(newStrategy);
            newStrategy.html(newStrategy.html().replace(/\{classname}/g, classname).replace(/\{eid}/g, eid));
            if(strategy == 'active')
            {
                // copy values back in to newly rendered fields
                if(typeof window.strategies != 'undefined' && typeof window.strategies[eid] != 'undefined' &&
                    typeof window.strategies[eid]['active'] != 'undefined')
                {
                    newStrategy.find('textarea[name="strategy-skim"]').val(window.strategies[eid]['active'].skim);
                    newStrategy.find('textarea[name="strategy-why"]').val(window.strategies[eid]['active'].why);
                    newStrategy.find('textarea[name="strategy-questions"]').val(window.strategies[eid]['active'].questions);
                    newStrategy.find('textarea[name="strategy-summarize"]').val(window.strategies[eid]['active'].summarize);
                    newStrategy.find('textarea[name="strategy-exam"]').val(window.strategies[eid]['active'].exam);
                }
            }
            if(strategy == 'other')
            {
                // copy values back in to newly rendered fields
                if(typeof window.strategies != 'undefined' && typeof window.strategies[eid] != 'undefined' &&
                    typeof window.strategies[eid]['other'] != 'undefined')
                {
                    newStrategy.find('textarea[name="strategy-notes"]').val(window.strategies[eid]['other'].notes);
                }
            }
            if(strategy == 'spaced')
            {
                var dates = window.planEvents[row.attr('id').substring(4)]['dates'];
                if(typeof dates != 'undefined')
                {
                    var dateStr = dates.map(function ($d, $i) {
                        return '<label class="checkbox"><input name="strategy-from-' + (604800 * $i) + '-' + eid + '"' +
                            ' type="checkbox" value="' + (604800 * $i) + '" /><i></i><span>' + $d + '</span></label>';
                    }).join('');
                    newStrategy.find('.strategy-review').append(dateStr);
                }
                if(typeof window.strategies != 'undefined' && typeof window.strategies[eid] != 'undefined' &&
                    typeof window.strategies[eid]['spaced'] != 'undefined')
                {
                    newStrategy.find('textarea[name="strategy-notes"]').val(window.strategies[eid]['spaced'].notes);
                    if(window.strategies[eid]['spaced'].review != '')
                        window.strategies[eid]['spaced'].review.split(',').forEach(function (x) {
                            newStrategy.find('input[value="' + x + '"]').prop('checked', true);
                        });
                }
            }
            if(strategy == 'prework')
            {
                if(typeof window.strategies != 'undefined' && typeof window.strategies[eid] != 'undefined' &&
                    typeof window.strategies[eid]['prework'] != 'undefined')
                {
                    newStrategy.find('textarea[name="strategy-notes"]').val(window.strategies[eid]['prework'].notes);
                    if(window.strategies[eid]['prework'].prepared != '')
                        window.strategies[eid]['prework'].prepared.split(',').forEach(function (x) {
                            newStrategy.find('input[value="' + x + '"]').prop('checked', true);
                        });
                }
            }
            if(strategy == 'teach')
            {
                var upload = new plupload.Uploader({
                    chunk_size: '5MB',
                    runtimes : 'html5,flash,silverlight,html4',
                    browse_button : 'teach-' + eid + '-select', // you can pass in id...
                    container: newStrategy.find('.plupload')[0], // ... or DOM Element itself
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
                                title : "Video files",
                                extensions : "mov,avi,mpg,mpeg,wmv,mp4,webm,flv,m4v,mkv,ogv,ogg,rm,rmvb,m4v"
                            }
                        ]
                    },
                    flash_swf_url : window.callbackPaths['_welcome'] + 'bundles/studysauce/js/plupload/js/Moxie.swf',
                    silverlight_xap_url : window.callbackPaths['_welcome'] + 'bundles/studysauce/js/plupload/js/Moxie.xap',
                    init: {
                        PostInit: function(up) {
                            newStrategy.find('.plupload').addClass('init');
                            newStrategy.find('#teach-' + eid + '-select').on('click', function () {
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
                            newStrategy.find('input[name="teach-' + eid + '-plupload"]').val(data.fid);
                            newStrategy.find('.plup-filelist .squiggle').stop().remove();
                            newStrategy.find('.plupload img').attr('src', data.src);
                            newStrategy.removeClass('invalid').addClass('valid');
                            newStrategy.find('a[href="#save-strategy"]').first().trigger('click');
                            setTimeout('videoPoller("' + fileSaved.fid + '", "' + eid + '");', 5000);
                        },
                        Error: function(up, err) {
                        }
                    }
                });

                setTimeout(function () {upload.init();}, 200);

                if(typeof window.strategies != 'undefined' && typeof window.strategies[eid] != 'undefined' &&
                    typeof window.strategies[eid]['teach'] != 'undefined')
                {
                    newStrategy.find('input[name="strategy-title"]').val(window.strategies[eid]['teach'].title);
                    newStrategy.find('textarea[name="strategy-notes"]').val(window.strategies[eid]['teach'].notes);
                    newStrategy.find('.plupload img').attr('scr', window.strategies[eid]['teach'].uploads);
                    // TODO: load video
                    //setTimeout('videoPoller("' + window.strategies[eid]['teach'].uploads[0].value + '", "' + eid + '");', 100);
                }
            }
        }

        row.find('.strategy-spaced, .strategy-active, .strategy-teach, .strategy-other, .strategy-prework').hide();
        row.find('.strategy-' + strategy).show();

    }

    body.on('click', '#plan a[href="#expand"]', function (evt) {
        evt.preventDefault();
        var row = jQuery(this).parents('.session-row');
        row.toggleClass('expanded').scrollintoview();
    });

    body.on('change', '#plan select[name="strategy-select"]', renderStrategies);

    body.on('click', '#plan .assignment, #plan .class-name, #plan .percent', function () {
            var row = $(this).parents('.session-row'),
                strategy = (/default-([a-z]*)(\s|$)/ig).exec(row.attr('class'))[1],
                cid = ((/cid([0-9]+)(\s|$)/ig).exec(row.attr('class')) || ['', ''])[1],
                classname = row.find('.class-name').not('span').text().trim();
            row.toggleClass('selected');

            // add mini-checkin if class number is set
            if(cid != null && row.find('.mini-checkin').length == 0 && strategy != 'other' &&
                plans.find('.mini-checkin').length > 0)
            {
                var newMiniCheckin = plans.find('.mini-checkin').first().clone();
                row.append(newMiniCheckin);
                newMiniCheckin.html(newMiniCheckin.html().replace(/\{classname}/g, classname).replace(/\{cid}/g, cid));
                setTimeout(function () {newMiniCheckin.setClock();}, 200);
            }

            // add the default strategy
            if(cid != null && row.find('.strategy-' + strategy).length == 0 &&
                plans.find('.strategy-' + strategy).length > 0 && strategy != 'other' && strategy != 'prework')
            {
                var newStrategySelect = plans.find('.field-select-strategy').first().clone();
                row.append(newStrategySelect);
            }

            // display the default strategy
            renderStrategies.apply(this);

            //
            if(!row.is('.selected'))
                row.find('.strategy-spaced, .strategy-active, .strategy-teach, .strategy-other, .strategy-prework').hide();
            else
                row.find('.mini-checkin:visible, .strategy-spaced:visible, .strategy-active:visible, .strategy-teach:visible, .strategy-other:visible, .strategy-prework:visible').first().scrollintoview({padding: {top:120,bottom:100,left:0,right:0}});

        });

    body.on('keyup', '#plan div[class^="strategy"] input[type="text"], #plan div[class^="strategy"] textarea', function () {
        jQuery(this).parents('div[class^="strategy"]').removeClass('invalid').addClass('valid');
    });
    body.on('change', '#plan div[class^="strategy"] input[type="checkbox"], #plan div[class^="strategy"] input[type="radio"], ' +
    '#plan div[class^="strategy"] input[type="text"], #plan div[class^="strategy"] textarea', function () {
        jQuery(this).parents('div[class^="strategy"]').removeClass('invalid').addClass('valid');
    });

    body.on('click', '#plan a[href="#save-strategy"]', function (evt) {
        evt.preventDefault();
        var that = jQuery(this),
            row = that.parents('.session-row'),
            eid = row.attr('id').substring(4),
            strategies = [];
        row.find('.strategy-active, .strategy-spaced, .strategy-teach, .strategy-other, .strategy-prework').each(function () {
            var that = jQuery(this);
            if(that.is('.strategy-active'))
            {
                var active = {
                    type: 'active',
                    eid:      eid,
                    skim:      that.find('textarea[name="strategy-skim"]').val() || '',
                    why:       that.find('textarea[name="strategy-why"]').val() || '',
                    questions: that.find('textarea[name="strategy-questions"]').val() || '',
                    summarize: that.find('textarea[name="strategy-summarize"]').val() || '',
                    exam:      that.find('textarea[name="strategy-exam"]').val() || ''
                };
                if(active.skim.trim() == '' &&
                    active.why.trim() == '' &&
                    active.questions.trim() == '' &&
                    active.summarize.trim() == '' &&
                    active.exam.trim() == '')
                    active = {
                        type: 'active',
                        eid:eid,
                        remove:true
                    };
                strategies[strategies.length] = active;
            }
            else if(that.is('.strategy-teach'))
            {
                var teach = {
                    type: 'teach',
                    eid:  eid,
                    title: that.find('input[name="strategy-title"]').val() || '',
                    notes: that.find('textarea[name="strategy-notes"]').val() || '',
                    fid: that.find('input[name="teach-' + eid + '-plupload"]').val() || ''
                };
                if(teach.title.trim() == '' &&
                    teach.notes.trim() == '' &&
                    teach.fid.length == 0)
                    teach = {
                        type:   'teach',
                        eid:   eid,
                        remove: true
                    };
                strategies[strategies.length] = teach;
            }
            else if(that.is('.strategy-spaced'))
            {
                var spaced = {
                    type:   'spaced',
                    eid:   eid,
                    notes:  that.find('textarea[name="strategy-notes"]').val() || '',
                    review: that.find('input[name^="strategy-from"]:checked').map(function () {
                        return jQuery(this).val();}).toArray().join(',')
                };
                if(spaced.notes.trim() == '' &&
                    spaced.review.trim() == '')
                    spaced = {
                        type: 'spaced',
                        eid:eid,
                        remove:true
                    };
                strategies[strategies.length] = spaced;
            }
            else if(that.is('.strategy-other'))
            {
                var other = {
                    type:  'other',
                    eid:  eid,
                    notes: that.find('textarea[name="strategy-notes"]').val() || ''
                };
                if(other.notes.trim() == '')
                    other = {
                        type:   'other',
                        eid:   eid,
                        remove: true
                    };
                strategies[strategies.length] = other;
            }
            else if(that.is('.strategy-prework'))
            {
                var prework = {
                    type:   'prework',
                    eid:    eid,
                    notes:   that.find('textarea[name="strategy-notes"]').val() || '',
                    prepared: that.find('input[name^="strategy-"]:checked').map(function () {
                        return jQuery(this).val();
                    }).toArray().join(',')
                };
                if(prework.notes.trim() == '' &&
                    prework.prepare.trim() == '')
                    prework = {
                        type:   'prework',
                        eid:   eid,
                        remove: true
                    };
                strategies[strategies.length] = prework;
            }

            that.removeClass('valid').addClass('invalid');
        });

        $.ajax({
            url: window.callbackPaths['plan_strategy'],
            type: 'POST',
            dataType: 'json',
            data: {
                // save selected strategy per event
                'default':row.find('select[name="strategy-select"]').val() != '_none'
                    ? row.find('select[name="strategy-select"]').val()
                    : null,
                strategies: strategies
            },
            success: function (data) {

            }
        });

    });
});