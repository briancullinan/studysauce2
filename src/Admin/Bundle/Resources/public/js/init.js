

$document.ready(function () {
    var email = $('#send-email'),
        preview = email.find('.preview')[0];

    var emails = $(window.parent.document).find('#send-email'),
        subject = $('.subject');
    emails.find('.variables').remove();
    $('.variables').insertBefore(emails.find('.highlighted-link'));
    emails.find('input[name="subject"]').val(subject.text());
    subject.remove();

    $('#editor1').find('> div:nth-of-type(2)').attr('contenteditable', true);
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

    $('.CodeMirror').hide();

    $(window.parent.document.body).on('click', '#send-email a[href="#toggle-source"]', function (evt) {
        var email = $('#send-email');
        if($('.CodeMirror').is(':visible')) {
            $('#editor1').html(mirror.getDoc().getValue());
            $('.CodeMirror,#editor1').toggle();
        }
        else {
            mirror.getDoc().setValue($('#editor1').html());
            $('.CodeMirror,#editor1').toggle();
            mirror.refresh();
        }
    });


    /*
    window.app = {

        // Web app variables
        supportsLocalStorage: ("localStorage" in window && window.localStorage !== null),

        init: function() {
            editor.init();
        },

        // Save a key/value pair in localStorage (either Markdown text or enabled features)
        save: function(key, value) {
            if (!this.supportsLocalStorage) return false;

            // Even if localStorage is supported, using it can still throw an exception if disabled or the quota is exceeded
            try {
                localStorage.setItem(key, value);
            } catch (e) {}
        },

        // Restore the editor's state from localStorage (saved Markdown and enabled features)
        restoreState: function(c) {
            var restoredItems = {};

            if (this.supportsLocalStorage) {
                // Even if localStorage is supported, using it can still throw an exception if disabled
                try {
                    //restoredItems.markdown = localStorage.getItem("markdown");
                    restoredItems.isAutoScrolling = localStorage.getItem("isAutoScrolling");
                    restoredItems.isFullscreen = 'y';
                    restoredItems.activePanel = localStorage.getItem("activePanel");
                } catch (e) {}
            }

            c(restoredItems);
        },

        // Update the preview panel with new HTML
        updateMarkdownPreview: function(html) {
            editor.markdownPreview.html(html);
            editor.updateWordCount(editor.markdownPreview.text());
        }

    };

    app.init();
*/

});
