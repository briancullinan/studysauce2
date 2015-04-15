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

    body.on('click', '#notes .note-row', function (evt) {
        evt.preventDefault();
        var notes = $('#notes'),
            note = $(this),
            classes = $(this).parents('.notes').prev('.class-row').attr('class'),
            notebook = (/notebook-id-([a-z0-9\-]*)(\s|$)/ig).exec(classes)[1],
            courseId = (/course-id-([a-z0-9\-]*)(\s|$)/ig).exec(classes)[1];
        notes.find('.note-title .title input').val($(this).find('h4 a').text());
        if(notebook != '')
            notes.find('select[name="notebook"]').val(notebook);
        else
            notes.find('select[name="notebook"]').val(courseId);
        notes.find('.input.tags input')[0].selectize.setValue(JSON.parse(note.attr('data-tags')));
        noteId = (/note-id-([a-z0-9\-]*)(\s|$)/ig).exec($(this).attr('class'))[1];
        notes.addClass('edit-note');
        CKEDITOR.instances.editor1.setData(note.find('.summary en-note').html());
        $.ajax({
            url: window.callbackPaths['notes_note'],
            type: 'GET',
            dataType: 'text',
            data: {
                noteId: noteId
            },
            success: function (data) {
                CKEDITOR.instances.editor1.setData($(data).filter('en-note').html());
            }
        });
        setTimeout(function () {
            CKEDITOR.instances.editor1.fire('focus');
        }, 20);
    });

    body.on('click', '#notes a[href="#delete-note"]', function (evt) {
        evt.preventDefault();
        var notes = $('#notes');
        notes.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation($(this));
        $.ajax({
            url: window.callbackPaths['notes_remove'],
            type: 'POST',
            dataType: 'text',
            data: {
                noteId: noteId,
                remove: true
            },
            success: function (data) {
                notes.find('.squiggle').remove();
                notes.removeClass('edit-note').find('.highlighted-link').removeClass('invalid').addClass('valid');
                var response = $(data);
                notes.find('.term-row').remove();
                response.find('.term-row').insertAfter('.new-study-note');
                $('#editor1').blur();
                CKEDITOR.instances.editor1.fire('blur');
            },
            error: function () {
                notes.find('.squiggle').remove();
            }
        });

    });

    body.on('click', '#notes a[href="#add-note"]', function (evt) {
        evt.preventDefault();
        var notes = $('#notes');
        notes.find('.note-title input').val('');
        notes.find('select[name="notebook"]').val('');
        notes.find('.input.tags input')[0].selectize.setValue([]);
        noteId = '';
        notes.addClass('edit-note');
        CKEDITOR.instances['editor1'].setData('');
        setTimeout(function () {
            CKEDITOR.instances.editor1.fire('focus');
        }, 20);
    });

    body.on('click', 'a[href="#save-note"]', function (evt) {
        evt.preventDefault();
        var notes = $('#notes');
        notes.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation($(this));
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
                notes.removeClass('edit-note').find('.highlighted-link').removeClass('invalid').addClass('valid');
                var response = $(data);
                notes.find('.term-row').remove();
                response.find('.term-row').insertAfter('.new-study-note');
                notes.find('select[name="notebook"]').replaceWith(response.find('select[name="notebook"]'));
                $('#editor1').blur();
                CKEDITOR.instances.editor1.fire('blur');
            },
            error: function () {
                notes.find('.squiggle').remove();
            }
        });
    });

    function updateNotes()
    {
        var notes = $('#notes');
        $.ajax({
            url: window.callbackPaths['notes'],
            type: 'POST',
            dataType: 'text',
            data: {},
            success: function (data) {
                var response = $(data);
                notes.find('.term-row').remove();
                response.find('.term-row').insertAfter('.new-study-note');
                notes.find('select[name="notebook"]').replaceWith(response.find('select[name="notebook"]'));
            }
        });
    }

    body.on('scheduled', updateNotes);

    body.on('hide', '#notes', function () {
        CKEDITOR.instances.editor1.fire('blur');
        $('#cke_editor1').hide();
        var notes = $('#notes');
        if(notes.is('.edit-note')) {
            notes.find('a[href="#save-note"]').trigger('click');
        }
    });

    body.on('submit', '#add-notebook form', function (evt) {
        evt.preventDefault();
        var notes = $('#notes'),
            dialog = $('#add-notebook');
        if(dialog.find('.highlighted-link').is('.invalid')) {
            dialog.find('input').focus();
            return;
        }
        loadingAnimation(dialog.find('[value="#save-notebook"]'));
        $.ajax({
            url: window.callbackPaths['notes_notebook'],
            type: 'POST',
            dataType: 'text',
            data: {
                name: dialog.find('input').val().trim()
            },
            success: function (data) {
                dialog.find('.squiggle').remove();
                var response = $(data);
                notes.find('select[name="notebook"]').replaceWith(response.find('select[name="notebook"]'));
                notes.find('select[name="notebook"]').val(notes.find('option:contains(' + dialog.find('input').val().trim() + ')').attr('value'));
                dialog.find('input').val('');
                dialog.modal('hide');
            },
            error: function () {
                dialog.find('.squiggle').remove();
            }
        });
    });

    body.on('change', '#notes select[name="notebook"]', function (evt) {
        if($(this).val() == 'Add notebook') {
            evt.preventDefault();
            $('#add-notebook').modal({show:true});
            return false;
        }
    });

    body.on('change', '#add-notebook input', function () {
        var dialog = $('#add-notebook');
        if($(this).val().trim() != '')
            dialog.find('.highlighted-link').removeClass('invalid').addClass('valid');
        else
            dialog.find('.highlighted-link').removeClass('valid').addClass('invalid');
    });
    body.on('keyup', '#add-notebook input', function () {
        var dialog = $('#add-notebook');
        if($(this).val().trim() != '')
            dialog.find('.highlighted-link').removeClass('invalid').addClass('valid');
        else
            dialog.find('.highlighted-link').removeClass('valid').addClass('invalid');
    });

    body.on('show', '#notes', function () {

        var notes = $('#notes'),
            loading = false;

        if($('#notes-connect').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            }).length > 0) {
        }

        // load editor
        if(!$(this).is('.loaded')) {

            setInterval(function () {
                if(notes.find('.note-row.loading').length == 0 || loading) {
                    return;
                }
                var noteIds = notes.find('.note-row.loading').map(function () {
                    return (/note-id-([a-z0-9\-]*)(\s|$)/ig).exec($(this).attr('class'))[1];
                }).toArray().splice(0, 10);
                loading = true;
                $.ajax({
                    url: window.callbackPaths['notes_summary'],
                    type: 'GET',
                    dateType: 'json',
                    data: {
                        noteIds: noteIds
                    },
                    success: function (data) {
                        for(var i in data) {
                            if(data.hasOwnProperty(i)) {
                                notes.find('.note-row.note-id-' + i).removeClass('loading').addClass('loaded').find('.summary').html(data[i]);
                            }
                        }
                        loading = false;
                    },
                    error: function () {
                        loading = false;
                    }
                })
            }, 1000);

            $(this).addClass('loaded');

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

            CKEDITOR.on('dialogDefinition', function(e) {
                var dialogDefinition = e.data.definition;
                dialogDefinition.onShow = function() {
                    this.move($(window).width() - this.getSize().width,0); // Top center
                }
            });

            setTimeout(initializeCKE, 100);
        }
        else {
            if(notes.is('.edit-note')) {
                CKEDITOR.instances.editor1.fire('focus');
            }
        }
    });

    function initializeCKE()
    {
        var notes = $('#notes');
        if(typeof CKEDITOR.instances.editor1 == 'undefined' ||
            typeof CKEDITOR.instances.editor1.setReadOnly == 'undefined' ||
            typeof CKEDITOR.instances.editor1.editable() == 'undefined') {
            setTimeout(initializeCKE, 100);
            return;
        }
        var editor = CKEDITOR.instances.editor1;
        editor.on('blur',function( e ){
            if(notes.is('.edit-note') && notes.is(':visible'))
                editor.fire('focus');
        });
        editor.on('focus',function( e ){
            CKEDITOR.instances.editor1.setReadOnly(false);
            if($('#cke_editor1').width() != $('#editor1').outerWidth()) {
                $('#cke_editor1').width($('#editor1').outerWidth());
                if(notes.is('.edit-note')) {
                    editor.fire('blur');
                    editor.fire('focus');
                }
            }
        });
        editor.setReadOnly(false);
    }

    $(window).resize(function () {
        $('#cke_editor1').width($('#editor1').outerWidth());
        if(typeof CKEDITOR.instances.editor1 != 'undefined')
            CKEDITOR.instances.editor1.fire('resize');
    });

});

