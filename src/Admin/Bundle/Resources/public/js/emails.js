

Selectize.define('restore_on_backspace2', function(options) {
    var self = this;

    options.text = options.text || function(option) {
        return option[this.settings.labelField];
    };

    this.onKeyDown = (function() {
        var original = self.onKeyDown;
        return function(e) {
            var index, option;
            index = this.caretPos - 1;

            if (e.keyCode === 8 && this.$control_input.val() === '' && !this.$activeItems.length) {
                if (index >= 0 && index < this.items.length) {
                    option = this.options[this.items[index]];
                    // prevent from deleting google
                    if (this.deleteSelection(e)) {
                        this.setTextboxValue(option[this.settings.valueField]);
                        this.refreshOptions(true);
                    }
                    e.preventDefault();
                    return;
                }
            }
            return original.apply(this, arguments);
        };
    })();
});

Selectize.define('continue_editing', function(options) {
    var self = this;

    options.text = options.text || function(option) {
        return option[this.settings.labelField];
    };

    this.onFocus = (function() {
        var original = self.onFocus;

        return function(e) {
            original.apply(this, arguments);

            var index = this.caretPos - 1;
            if (index >= 0 && index < this.items.length) {
                var option = this.options[this.items[index]];
                var currentValue = options.text.apply(this, [option]);
                if (this.deleteSelection({keyCode: 8})) {
                    // only remove item if it is made up and not from the server
                    if(typeof option.alt == 'undefined')
                        this.removeItem(currentValue);
                    this.setTextboxValue(option[this.settings.valueField]);
                    this.refreshOptions(true);
                }
            }
        };
    })();

    this.onBlur = (function() {
        var original = self.onBlur;

        return function(e) {
            var v = this.$control_input.val();
            original.apply(this, arguments);
            if(v.trim() != '') {
                var option = this.options[v] || { value: v, text: v };
                this.addOption(option);
                this.setValue(option.value);
            }
        };
    })();
});

var callbackTimeout;

function setupSelectize()
{
    var that = $(this),
        row = that.parents('tr');
    that.selectize({
        persist:false,
        delimiter: ' ',
        searchField: ['text', 'value', '1', '0'],
        plugins: ['continue_editing', 'restore_on_backspace2'],
        maxItems: 1,
        options: [that.val()],
        render: {
            option: function(item) {
                return '<div>' +
                '<span class="title">' +
                '<span class="name"><i class="icon source"></i>' + item.text + '</span>' +
                '<span class="by">' + (typeof item[0] != 'undefined' ? item[0] : '') + '</span>' +
                '</span>' +
                '<span class="description">' + (typeof item[1] != 'undefined' ? item[1] : '') + '</span>' +
                '</div>';
            }
        },
        load: function(query, callback) {
            if(that.parents('th').length > 0) callback(window.entities);
            if (query.length < 1) callback();
            if(callbackTimeout)
                clearTimeout(callbackTimeout);
            callbackTimeout = setTimeout(function () {
                $.ajax({
                    url: window.callbackPaths['emails_search'],
                    dataType:'json',
                    data: {
                        alt: row.find('.selectized').not(that).map(function () {return $(this).attr('name');}).toArray().join(','),
                        field: that.attr('name'),
                        q: query
                    },
                    error: function() {
                        callback();
                    },
                    success: function(res) {
                        callback(res.slice(0, 100));
                    }
                });
            }, 500)
        }
    });
}

$(document).ready(function () {

    var body = $('body');
    var ctrlDown = false;
    var ctrlKey = 17, vKey = 86, cKey = 67;

    $(document).keydown(function(e)
    {
        if (e.keyCode == ctrlKey) ctrlDown = true;
    }).keyup(function(e)
    {
        if (e.keyCode == ctrlKey) ctrlDown = false;
    });

    body.find('#emails').on('keydown', function(e)
    {
        var email = $('#send-email');
        if (ctrlDown && (e.keyCode == vKey || e.keyCode == cKey)) {

            // get the clipboard text
            var that = $(this),
                text = $('<textarea></textarea>').css('opacity', '0').css('height', 1).css('width', 1).appendTo(email).focus();
            setTimeout(function () {
                var clipText = text.val();
                text.remove();
                that.focus();

                // split into rows
                var clipRows = clipText.split(/\n/ig);

                // split rows into columns
                for (i=0; i<clipRows.length; i++) {
                    clipRows[i] = clipRows[i].split(/\t|\s\s\s\s+/ig);
                }

                // write out in a table
                for (var i=0; i<clipRows.length; i++) {
                    if(clipRows[i].length == 0 || clipRows[i][0].length == 0 || clipRows[i].indexOf('email') > -1 ||
                        clipRows[i].indexOf('e-mail') > -1 || clipRows[i].indexOf('E-mail') > -1)
                        continue;
                    email.find('a[href="#add-line"]').trigger('click');
                    var newRow = email.find('.variables > tbody > tr').last();
                    for (var j=0; j<clipRows[i].length; j++) {
                        if (clipRows[i][j].length == 0) {
                            newRow.find('> td:eq(' + j + ') input').val('');
                        }
                        else {
                            newRow.find('> td:eq(' + j + ') input').val(clipRows[i][j]);
                        }
                    }
                }
            }, 100);
        }
    });

    body.on('change', '#send-email .variables > tbody > tr > td > label > input, #send-email .variables > thead > tr > th > label > input', function () {
        if($(this).parents('th').length > 0) {
            var i = $(this).parents('th').index();
            for(var k in window.entities) {
                if(window.entities.hasOwnProperty(k) && window.entities[k].value == this.selectize.getValue()) {
                    var cell = $(this).parents('.variables').find('> tbody > tr > td:eq(' + i + ')');
                    cell.find('input').attr('placeholder', window.entities[k]['0'].substr(1));
                    cell.find('> label > input').attr('name', window.entities[k].value);
                    break;
                }
            }
        }
        else {
            if(typeof this.selectize.options[this.selectize.getValue()] != 'undefined' &&
                typeof this.selectize.options[this.selectize.getValue()].alt != 'undefined') {
                var option = this.selectize.options[this.selectize.getValue()];
                for(var j in option.alt) {
                    if(option.alt.hasOwnProperty(j)) {
                        var that = $(this).parents('tr').find('input[name="' + j + '"]');
                        that.val(option.alt[j]);
                        that[0].selectize.addOption({text: option.alt[j], value: option.alt[j]});
                        that[0].selectize.setValue(option.alt[j])
                    }
                }
            }
        }
    });

    body.on('click', '#emails a[href="#send-email"]', function () {
        var emails = $('#emails'),
            template = $(this).parents('tr').find('td:nth-child(1)').text();
        if(template != '') {
            emails.find('.nav li a[href="#send-email"]')
                .parents('ul').find('li')
                .removeClass('active').last()
                .addClass('active');
            if(template != emails.find('select[name="template"]').val()) {
                emails.find('select[name="template"]').val(template).trigger('change');
            }
        }
    });

    body.on('click', '#send-email a[href="#remove-line"]', function (evt) {
        evt.preventDefault();
        if($(this).parents('tr').siblings().length > 0)
            $(this).parents('tr').remove();
        else
            $(this).parents('tr').find('input').val('');
    });

    body.on('click', '#send-email a[href="#remove-field"]', function (evt) {
        var index = $(this).parents('th').index();
        $(this).parents('.variables').find('thead > tr > th:eq(' + index + '), tbody > tr > td:eq(' + index + ')').remove();
    });

    body.on('click', '#send-email a[href="#add-field"]', function (evt) {
        evt.preventDefault();
        var email = $('#send-email');
        email.find('.variables > thead > tr > th:first-child, .variables > tbody > tr > td:first-child').each(function () {
            var newCell = $(this).clone().insertBefore($(this).parents('tr').find('td:last-child, th:last-child'));
            newCell.find('.selectized').removeClass('selectized');
            newCell.find('.selectize-control').remove();
            if(newCell.is('th')) {
                newCell.html('<label class="input"><input type="text" /></label><a href="#remove-field"></a>');
            }
            newCell.find('input').each(function () {
                $(this).val('');
                setupSelectize.apply(this);
            });
        });
    });

    body.on('change', '#send-email select[name="template"]', function () {
        var email = $('#send-email');
        if($(this).val() != '')
            email.find('.preview').replaceWith($('<iframe class="preview" src="' + window.callbackPaths['emails_template'] + '/' + $(this).val() + '" height="400" width="100%" frameborder="0"></iframe>'));
    });

    body.on('click', '#send-email a[href="#add-line"]', function (evt) {
        evt.preventDefault();
        var email = $('#send-email'),
            newRow = email.find('.variables > tbody > tr').first().clone().appendTo(email.find('.variables > tbody'));
        newRow.find('.selectized').removeClass('selectized');
        newRow.find('.selectize-control').remove();
        newRow.find('input').each(function () {
            $(this).val('');
            setupSelectize.apply(this);
        });

    });

    body.on('click', '#send-email a[href="#markdown"], #send-email a[href="#editor1"]', function (evt) {
        evt.preventDefault();
    });

    body.on('shown.bs.modal', '#send-confirm', function () {
        var count = 0;
        $('#send-email').find('.variables > tbody > tr').each(function () {
            if($(this).find('td:nth-child(1) > label > input').val().trim().length > 0)
                count++;
        });
        $('#send-confirm').find('.count').text(count);
    });

    body.on('click', '#send-confirm a[href="#confirm-send"]', function () {
        var email = $('#send-email');
        $.ajax({
            url: window.callbackPaths['emails_send'] + '/' + email.find('select[name="template"]').val(),
            dataType: 'text',
            type: 'POST',
            data: {
                subject: email.find('input[name="subject"]').val().trim(),
                template: email.find('iframe.preview')[0].contentWindow.CKEDITOR.instances.editor1.getData(),
                variables: email.find('.variables > tbody > tr').map(function () {
                    var line = {};
                    $(this).find('> td > label > input').each(function () { line[$(this).attr('name')] = $(this).val().trim(); });
                    return line;
                }).toArray(),
                confirm: true
            },
            success: function (response) {
                // clear list

            }
        });
    });

    body.find('#send-email .variables input').each(setupSelectize);

});