$(document).ready(function () {

    var body = $('body'),
        noteId = '';

    body.on('click', '#notes .term-row', function (evt) {
        if($(evt.target).is('.term-editor, .term-editor *'))
            return;
        var calc = body.find('#notes'),
            row = $(this).closest('.term-row');
        if(!row.is('.selected')) {
            calc.find('.term-row.selected').removeClass('selected');
            row.addClass('selected');
        }
    });

    body.on('click', '#notes .class-row', function () {
        var row = $(this).closest('.class-row');
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
            notebook = (/notebook-id-([a-z0-9\-]*)(\s|$)/ig).exec(note.attr('class'))[1],
            courseId = (/course-id-([a-z0-9\-]*)(\s|$)/ig).exec(note.attr('class'))[1];
        notes.find('.note-title .title input').val($(this).find('h4 a').text());
        if(notebook != '')
            notes.find('select[name="notebook"]').val(notebook);
        else
            notes.find('select[name="notebook"]').val(courseId);
        notes.find('.input.tags input')[0].selectize.setValue(JSON.parse(note.attr('data-tags')));
        noteId = (/note-id-([a-z0-9\-]*)(\s|$)/ig).exec($(this).attr('class'))[1];
        notes.addClass('edit-note');
        notes.find('.highlighted-link').removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['notes'].replace('/tab', '/body/' + noteId),
            type: 'GET',
            dataType: 'text',
            data: {},
            success: function (data) {
                notes.find('.highlighted-link').removeClass('invalid').addClass('valid');
                setTimeout(function () {
                    if(typeof CKEDITOR.instances.editor1 != 'undefined') {
                        CKEDITOR.instances.editor1.setData($(data).filter('en-note').html());
                        CKEDITOR.instances.editor1.fire('focus');
                    }
                }, 20);
            }
        });
        setTimeout(function () {
            if(typeof CKEDITOR.instances.editor1 != 'undefined') {
                CKEDITOR.instances.editor1.setData(note.find('.summary en-note').html());
                CKEDITOR.instances.editor1.fire('focus');
            }
        }, 20);
    });

    body.on('click', 'en-todo', function () {
        if($(this).is('[checked]')) {
            $(this).removeAttr('checked');
        }
        else {
            $(this).attr('checked', 'true');
        }
    });

    function deleteNote(evt)
    {
        evt.preventDefault();
        var notes = $('#notes');
        notes.find('.highlighted-link').removeClass('valid').addClass('invalid');
        loadingAnimation(notes.find('a[href="#delete-note"]'));
        $.ajax({
            url: window.callbackPaths['notes'].replace('/tab', '/remove/' + noteId),
            type: 'POST',
            dataType: 'text',
            data: {},
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
    }

    body.on('click', '#notes a[href="#delete-note"]', deleteNote);

    body.on('click', '#notes a[href="#add-note"]', function (evt) {
        evt.preventDefault();
        $('#notes-connect').modal('hide');
        var notes = $('#notes');
        notes.find('.note-title input').val('');
        notes.find('select[name="notebook"]').val('');
        notes.find('.input.tags input')[0].selectize.setValue([]);
        noteId = '';
        notes.addClass('edit-note');
        setTimeout(function () {
            if(typeof CKEDITOR.instances.editor1 != 'undefined')
                CKEDITOR.instances['editor1'].setData('');
                CKEDITOR.instances.editor1.fire('focus');
        }, 20);
    });

    function updateNotes(data)
    {
        var notes = $('#notes'),
            notebookId = notes.find('select[name="notebook"]').val();
        notes.find('.squiggle').remove();
        notes.removeClass('edit-note').find('.highlighted-link').removeClass('invalid').addClass('valid');
        var response = $(data);
        notes.find('.term-row').remove();
        response.find('.term-row').insertAfter('.new-study-note');
        notes.find('select[name="notebook"]').replaceWith(response.find('select[name="notebook"]'));
        var row = notes.find('.class-row.notebook-id-' + notebookId + ', .class-row.course-id-' + notebookId);
        if (!row.parents('.term-row').is('.selected')) {
            row.parents('.term-row').find('> :first-child').trigger('click');
        }
        if (!row.is('.selected')) {
            row.find('> :first-child').trigger('click');
        }
        row.scrollintoview(DASHBOARD_MARGINS);
        $('#editor1').blur();
        CKEDITOR.instances.editor1.fire('blur');
        if(notes.is('.not-connected') && !notes.is('.connect-shown') && notes.is(':visible')) {
            notes.addClass('connect-shown');
            $('#notes-connect').modal({show:true});
        }
    }

    function saveNote()
    {
        var notes = $('#notes'),
            notebookId = notes.find('select[name="notebook"]').val();
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
                notebookId: notebookId,
                body: CKEDITOR.instances['editor1'].getData()
            },
            success: updateNotes,
            error: function () {
                notes.find('.squiggle').remove();
            }
        });
    }

    body.on('click', '#notes a[href="#save-note"]', function (evt) {
        evt.preventDefault();
        var notes = $('#notes');
        if(notes.is(':visible') && CKEDITOR.instances['editor1'].getData().trim() == '') {
            var dialog = $('#notes-discard').modal({show:true});
            dialog.one('click.discard', 'a[href="#close"]', saveNote);
            // if the note is cleared and it has an id, we should delete it
            if(noteId != '') {
                dialog.one('click.discard', 'a[href="#discard-note"]', deleteNote);
            }
        }
        else {
            saveNote();
        }
    });

    body.on('hide.bs.modal', '#notes-discard', function () {
        var that = $(this);
        // make sure we remove it after it has a chance to fire
        setTimeout(function () { that.off('.discard'); }, 15);
    });

    body.on('scheduled', function () {
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
    });

    body.on('hide', '#notes', function () {
        $(this).find('[contenteditable="true"]').each(function () {
            var id = $(this).attr('id');
            if(typeof CKEDITOR.instances[id] != 'undefined')
                CKEDITOR.instances[id].fire('blur');
            $('#cke_' + id).hide();
        });
        if($(this).is('.edit-note')) {
            $(this).find('a[href="#save-note"]').trigger('click');
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
                notes.find('.term-row').remove();
                response.find('.term-row').insertAfter('.new-study-note');
                notes.find('select[name="notebook"]').replaceWith(response.find('select[name="notebook"]'));
                notes.find('select[name="notebook"]').val(notes.find('option:contains(' + dialog.find('input').val().trim() + ')').attr('value'));
                var row = notes.find('[value="' + dialog.find('input').val().trim() + '"]').parents('.class-row');
                if(!row.parents('.term-row').is('.selected')) {
                    row.parents('.term-row').find('> :first-child').trigger('click');
                }
                if(!row.is('.selected')) {
                    row.find('> :first-child').trigger('click');
                }
                row.scrollintoview(DASHBOARD_MARGINS);
                dialog.find('input').val('');
                dialog.modal('hide');
            },
            error: function () {
                dialog.find('.squiggle').remove();
            }
        });
    });

    body.on('click', '#notes a[href="#delete-notebook"]', function () {
        var notebook = (/notebook-id-([a-z0-9\-]*)(\s|$)/ig).exec($(this).parents('.class-row').attr('class'))[1];
        var dialog = $('#delete-notebook');
        var current = (/notebook-id-([a-z0-9\-]*)(\s|$)/ig).exec(dialog.attr('class'));
        if(current != null)
            dialog.removeClass(current[0]);
        dialog.addClass('notebook-id-' + notebook);
    });

    body.on('click', '#delete-notebook a[href="#confirm-delete-notebook"]', function (evt) {
        evt.preventDefault();
        var notes = $('#notes'),
            dialog = $('#delete-notebook');
        var notebookId = (/notebook-id-([a-z0-9\-]*)(\s|$)/ig).exec(dialog.attr('class'))[1];
        loadingAnimation($(this));
        $.ajax({
            url: window.callbackPaths['notes_notebook'],
            type: 'POST',
            dataType: 'text',
            data: {
                remove: notebookId
            },
            success: function (data) {
                dialog.find('.squiggle').remove();
                var response = $(data);
                notes.find('.term-row').remove();
                response.find('.term-row').insertAfter('.new-study-note');
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

    body.on('keyup change', '#add-notebook input', function () {
        var dialog = $('#add-notebook');
        if($(this).val().trim() != '')
            dialog.find('.highlighted-link').removeClass('invalid').addClass('valid');
        else
            dialog.find('.highlighted-link').removeClass('valid').addClass('invalid');
    });

    body.on('keyup change', '#notes input[name="search"]', function () {
        var notes = $('#notes');
        if($(this).val().trim() == '') {
            notes.find('.note-row').show();
        }
    });

    body.on('submit', '#notes form', function (evt) {
        evt.preventDefault();
        var notes = $('#notes');
        loadingAnimation(notes.find('[value="search"]'));
        $.ajax({
            url: window.callbackPaths['notes_search'],
            type: 'GET',
            dataType: 'json',
            data: {
                search: notes.find('input[name="search"]').val().trim()
            },
            success: function (data) {
                notes.find('.squiggle').remove();
                // only show notes and sections with results in them
                notes.find('.note-row').each(function () {
                    var noteId = (/note-id-([a-z0-9\-]*)(\s|$)/ig).exec($(this).attr('class'))[1];
                    if(data.indexOf(noteId) > -1) {
                        $(this).show();
                    }
                    else {
                        $(this).hide();
                    }
                });
            },
            error: function () {
                notes.find('.squiggle').remove();
            }
        })
    });

    body.on('show', '#notes', function () {

        var notes = $('#notes');

        // load editor
        if(!$(this).is('.setup')) {

            $(this).addClass('setup');

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

        }

        // load editor
        if(!$(this).is('.loaded')) {
            $(this).addClass('loaded');
            setTimeout(initializeCKE, 100);
        }
        else {
            if($(this).is('.edit-note')) {
                $(this).find('[contenteditable="true"]').each(function () {
                    var id = $(this).attr('id');
                    CKEDITOR.instances[id].fire('focus');
                });
            }
        }
    });

    function initializeCKE()
    {
        var notes = $('#notes').find('[contenteditable]:not(.loaded)');
        notes.each(function () {
            var that = $(this),
                id = that.attr('id');
            if(typeof CKEDITOR.instances[id] == 'undefined' ||
                typeof CKEDITOR.instances[id].setReadOnly == 'undefined') {
                CKEDITOR.inline(id);
            }
            that.addClass('loaded');
            setTimeout(function () {
                if(typeof CKEDITOR.instances[id] != 'undefined') {
                    var editor = CKEDITOR.instances[id];
                    editor.on('blur',function( evt ){
                        if(that.parents('.panel-pane').is('.edit-note') && that.is(':visible')) {
                            editor.fire('focus');
                            evt.cancel();
                        }
                    });
                    editor.on('focus',function(){
                        if(typeof editor.editable() != 'undefined')
                            editor.setReadOnly(false);
                        $(window).trigger('resize');
                    });
                    $(window).trigger('resize');
                }
            }, 20);
            $(window).trigger('resize');
        });
    }

    $(window).resize(function () {
        $('[contenteditable="true"]:visible').each(function () {
            var that = $(this),
                id = that.attr('id');
            setTimeout(function () {
                $('#cke_' + id + ':visible').width(that.outerWidth());
                if(typeof CKEDITOR.instances[id] != 'undefined')
                    CKEDITOR.instances[id].fire('resize');
            }, 200);
        });
    });

});

