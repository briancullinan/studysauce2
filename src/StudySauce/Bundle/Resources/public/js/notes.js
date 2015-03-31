$(document).ready(function () {

    var body = $('body');

    body.on('click', 'a[href*="#note-id-"]', function (evt) {
        evt.preventDefault();
        var notes = $('#notes');
        notes.find('.note-title input').val($(this).text());
        notes.addClass('edit-note');
        setTimeout(function () {
            notes.find('#editor1').attr('contenteditable', true);
            CKEDITOR.instances['editor1'].setReadOnly(false);
        }, 100);
    });

    body.on('click', 'a[href="#save-note"]', function (evt) {
        evt.preventDefault();
        var notes = $('#notes');
        notes.removeClass('edit-note');
    });

    body.on('show', '#notes', function () {

        if($('#notes-connect').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            }).length > 0) {
        }

        // load editor
        if(!$(this).is('.loaded')) {
            $(this).addClass('loaded');
            CKEDITOR.on('dialogDefinition', function(e) {
                var dialogDefinition = e.data.definition;
                dialogDefinition.onShow = function() {
                    this.move($(window).width() - this.getSize().width,0); // Top center
                }
            });
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

                        debugger;
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

        }
    });


});

