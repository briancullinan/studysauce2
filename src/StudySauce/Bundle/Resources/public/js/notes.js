$(document).ready(function () {

    var body = $('body'),
        noteId = '';

    body.on('click', '#notes .term-row > *:not(.term-editor)', function () {
        var calc = body.find('#notes'),
            row = $(this).parents('.term-row');
        if(!row.is('.selected')) {
            calc.find('.term-row.selected').removeClass('selected');
            row.addClass('selected');
        }
    });

    body.on('click', '#notes .class-row > *:not(.notes)', function () {
        var row = $(this).parents('.class-row');
        if(row.is('.selected')) {
            row.removeClass('selected');
        }
        else {
            row.addClass('selected');
        }
    });

    body.on('click', '#notes a[href="#view-note"]', function (evt) {
        evt.preventDefault();
        var notes = $('#notes'),
            note = $(this).parents('.note-row'),
            classes = $(this).parents('.notes').prev('.class-row').attr('class'),
            notebook = (/notebook-id-([a-z0-9\-]*)(\s|$)/ig).exec(classes)[1],
            courseId = (/course-id-([a-z0-9\-]*)(\s|$)/ig).exec(classes)[1];
        notes.find('.note-title .title input').val($(this).text());
        if(notebook != '')
            notes.find('select[name="notebook"]').val(notebook);
        else
            notes.find('select[name="notebook"]').val(courseId);
        notes.find('.input.tags input')[0].selectize.setValue(JSON.parse(note.attr('data-tags')));
        noteId = (/note-id-([a-z0-9\-]*)(\s|$)/ig).exec($(this).parents('.note-row').attr('class'))[1];
        notes.addClass('edit-note');
        CKEDITOR.instances['editor1'].setData(note.find('.summary en-note').html());
        $('#editor1').focus();
    });

    body.on('click', '#notes a[href*="#add-note"]', function (evt) {
        evt.preventDefault();
        var notes = $('#notes');
        notes.find('.note-title input').val('');
        notes.find('select[name="notebook"]').val('');
        notes.addClass('edit-note');
        noteId = '';
        CKEDITOR.instances['editor1'].setData('');
        $('#editor1').focus();
    });

    body.on('click', 'a[href="#save-note"]', function (evt) {
        evt.preventDefault();
        var notes = $('#notes');
        notes.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation(notes.find('a[href="#save-note"]'));
        $.ajax({
            url: window.callbackPaths['notes_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                noteId: noteId,
                tags: notes.find('.input.tags input')[0].selectize.getValue(),
                title: notes.find('.note-title .title input').val().trim(),
                notebookId: notes.find('select[name="notebook"]').val(),
                body: CKEDITOR.instances['editor1'].getData()
            },
            success: function (data) {
                notes.find('.squiggle').remove();
                notes.removeClass('edit-note');
                CKEDITOR.instances.editor1.fire('blur');
            },
            error: function () {
                notes.find('.squiggle').remove();
            }
        });
    });

    body.on('hide', '#notes', function () {
        CKEDITOR.instances.editor1.fire('blur');
    });

    body.on('show', '#notes', function () {

        var notes = $('#notes');

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
            CKEDITOR.on( 'instanceReady', function( event ) {
                var editor = event.editor,
                    element = editor.element;
                editor.on('blur',function( e ){
                    editor.fire('focus');
                });
                editor.on('focus',function( e ){
                    if($('#cke_editor1').width() != $('#editor1').outerWidth()) {
                        $('#cke_editor1').width($('#editor1').outerWidth());
                        editor.fire('blur');
                        editor.fire('focus');
                    }
                });
                $(window).resize(function () {
                    $('#cke_editor1').width($('#editor1').outerWidth());
                    editor.fire('resize');
                });
                editor.setReadOnly(false);
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

            });

            // initialize tags selectize
            notes.find('.input.tags input').selectize({
                persist:false,
                plugins: [/*'continue_editing', */ 'restore_on_backspace', 'remove_button'],
                valueField: 'value',
                labelField: 'text',
                searchField: ['text'],
                create: true,
                options: window.initialTags,
                render: {
                    option: function(item) {
                        return '<div><span class="title">' + item.text + '</span></div>';
                    }
                }
            });
        }
        else {
            if(notes.is('.edit-note')) {
                CKEDITOR.instances.editor1.fire('focus');
            }
        }
    });


});

