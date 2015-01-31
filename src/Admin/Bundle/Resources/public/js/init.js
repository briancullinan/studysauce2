

$(document).ready(function () {
    var email = $('#send-email'),
        preview = email.find('#preview')[0];

    var emails = $(window.parent.document).find('#send-email'),
        subject = $('.subject');
    emails.find('.variables').remove();
    $('.variables').insertBefore(emails.find('.highlighted-link'))
        // setupSelectize
        .find('input').each(window.parent.setupSelectize);
    emails.find('input[name="subject"]').val(subject.text());
    subject.remove();
    // TODO: recreate rows when variables change


    CKEDITOR.basePath = window.parent.callbackPaths['_welcome'] + 'bundles/admin/js/ckeditor/';
    CKEDITOR.on( 'instanceCreated', function( event ) {
        var editor = event.editor,
            element = editor.element;

        // Customize editors for headers and tag list.
        // These editors don't need features like smileys, templates, iframes etc.
        if ( element.is( 'h1', 'h2', 'h3' ) || element.getAttribute( 'id' ) == 'taglist' ) {
            // Customize the editor configurations on "configLoaded" event,
            // which is fired after the configuration file loading and
            // execution. This makes it possible to change the
            // configurations before the editor initialization takes place.
            editor.on( 'configLoaded', function() {

                // Remove unnecessary plugins to make the editor simpler.
                editor.config.removePlugins = 'colorbutton,find,flash,font,' +
                'forms,iframe,image,newpage,removeformat,' +
                'smiley,specialchar,stylescombo,templates';

                // Rearrange the layout of the toolbar.
                editor.config.toolbarGroups = [
                    { name: 'editing',		groups: [ 'basicstyles', 'links' ] },
                    { name: 'undo' },
                    { name: 'clipboard',	groups: [ 'selection', 'clipboard' ] },
                    { name: 'about' }
                ];
            });
        }

        editor.on( 'instanceReady', function( ) {
            var rules = {
                indent: false,
                breakBeforeOpen: true,
                breakAfterOpen: false,
                breakBeforeClose: false,
                breakAfterClose: true
            };
            editor.dataProcessor.writer.setRules( 'p',rules);
            editor.dataProcessor.writer.setRules( 'div',rules);
            editor.dataProcessor.writer.setRules( 'hr',rules);
            editor.dataProcessor.writer.setRules( 'br',rules);
        });
    });


    mirror = CodeMirror.fromTextArea($('#markdown')[0], {
        lineNumbers: true,
        lineWrapping: true,
        extraKeys: {
            "Ctrl-Space": "autocomplete",
            "Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }
        },
        fullscreen: true,
        mode: "text/html",
        foldGutter: true,
        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
    });

    if($(window.parent.document.body)
            .find('#send-email .active a[href="#markdown"], #send-email .active a[href="#editor1"]').first().is('[href="#editor1"]')) {
        $('.CodeMirror').hide();
    }
    else {
        $('#editor1').hide();
    }

    $(window.parent.document.body).on('click', '#send-email a[href="#markdown"], #send-email a[href="#editor1"]', function (evt) {
        evt.preventDefault();
        var email = $('#send-email');
        $(this).parents('ul').find('.active').removeClass('active');
        $(this).parents('li').addClass('active');
        if($(this).is('[href="#editor1"]')) {
            CKEDITOR.instances.editor1.setData(mirror.getDoc().getValue());
            $('.CodeMirror').hide();
            $('#editor1').show();
        }
        else {
            mirror.getDoc().setValue(CKEDITOR.instances.editor1.getData());
            $('.CodeMirror').show();
            $('#editor1').hide();
            mirror.refresh();
        }
    });

});
