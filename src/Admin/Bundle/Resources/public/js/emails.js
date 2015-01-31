

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
                this.addOption({ value: v, text: v });
                this.setValue([v]);
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
            if (query.length < 1) return callback();
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

    body.find('#send-email .variables input').each(setupSelectize);

});