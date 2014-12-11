
jQuery(document).ready(function() {

    var body = $('body');

    function importFunc() {
        var importTab = $('#import'),
            row = $(this).closest('.import-row');
        row.each(function () {
            var that = jQuery(this),
                isValid = true;

            if(that.find('.first-name input').val().trim() == '' ||
                that.find('.last-name input').val().trim() == '' ||
                that.find('.email input').val().trim() == '' ||
                !(/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}\b/i).test(that.find('.email input').val()))
                isValid = false;

            if(isValid)
                that.removeClass('invalid').addClass('valid');
            else
                that.removeClass('valid').addClass('invalid');
        });
        if(importTab.find('.import-row.edit.valid').length == 0)
            importTab.find('.form-actions').removeClass('valid').addClass('invalid');
        else
            importTab.find('.form-actions').removeClass('invalid').addClass('valid');
    }

    body.on('change', '#import .first-name input, #import .last-name input, #import .email input', importFunc);
    body.on('keyup', '#import .first-name input, #import .last-name input, #import .email input', importFunc);

    function rowImport(append)
    {
        var importTab = $('#import');
        this.forEach(function (x) {
            // parse first last and email
            var parser = (/(.+?)\s*[\t,]\s*(.+?)\s*[\t,]\s*(.+?)\s*(\t|,|$)\s*/ig).exec(x);
            if(x.trim() == '' || parser == null)
                return true;

            var count = importTab.find('.import-row').length,
                addUser = importTab.find('#add-user-row').last(),
                newUser = addUser.clone().attr('id', '').addClass('edit');
            if(append != null)
                newUser.appendTo(append);
            else
                newUser.insertBefore(addUser);
            if(count == 1)
                newUser.addClass('first');
            newUser.find('input[type="checkbox"], input[type="radio"]').each(function () {
                var that = jQuery(this),
                    oldId = that.attr('id');
                that.attr('id', oldId + count);
                if(that.is('[type="radio"]'))
                    that.attr('name', that.attr('name') + count);
                newUser.find('label[for="' + oldId + '"]').attr('for', oldId + count);
            });

            // fill in values automatically
            newUser.find('.first-name input').val(parser[1]);
            newUser.find('.last-name input').val(parser[2]);
            newUser.find('.email input').val(parser[3]);

            importFunc.apply(newUser);
            importTab.addClass('edit-user-only');
        });

        // remove empties
        if(importTab.find('.import-row.edit.valid').not('fieldset .import-row').length > 1)
        {
            importTab.find('.import-row.edit').not('#add-user-row').each(function () {
                var that = jQuery(this);
                if(that.find('.first-name input').val().trim() == '' &&
                    that.find('.last-name input').val().trim() == '' &&
                    that.find('.email input').val().trim() == '')
                {
                    that.remove();
                }
            });
        }
    }

    var previewTimeout = null;
    function previewImport() {
        var importTab = $('#import');
        if(previewTimeout != null)
            clearTimeout(previewTimeout);
        previewTimeout = setTimeout(function () {
            // select the first couple rows or limit to 1000 characters
            var rows;
            var first1000 = /[\s\S]{0,1000}/i;
            var match = first1000.exec(importTab.find('textarea').val());
            var preview = importTab.find('fieldset');
            preview.find('.import-row').remove();
            if (match != null) {
                rows = match[0].split((/\s*\n\s*/ig));
            } else {
                rows = []
            }
            rowImport.apply(rows, preview);
        }, 1000);
    }

    body.on('mousedown', '#import textarea', previewImport);
    body.on('mouseup', '#import textarea', previewImport);
    body.on('change', '#import textarea', previewImport);
    body.on('focus', '#import textarea', previewImport);
    body.on('blur', '#import textarea', previewImport);
    body.on('keydown', '#import textarea', previewImport);
    body.on('keyup', '#import textarea', previewImport);

    body.on('click', '#import a[href="#import-group"]', function (evt) {
        evt.preventDefault();
        var importTab = $('#import'),
            rows = importTab.find('textarea').val().split((/\s*\n\s*/ig));
        importTab.find('fieldset').find('.import-row').remove();
        rowImport.apply(rows);
        importTab.find('textarea').val('');
    });

    body.on('click', '#import a[href="#add-user"]', function (evt) {
        evt.preventDefault();
        var importTab = $('#import'),
            newUser = importTab.find('.import-row').first().clone()
            .removeClass('read-only historic').addClass('edit').insertBefore(importTab.find('.form-actions').first());
        newUser.find('.first-name select, .last-name input, .email input').val('');
        importFunc.apply(newUser);
    });

    body.on('click', '#import a[href="#save-group"]', function (evt) {
        evt.preventDefault();
        var importTab = $('#import'),
            users = [],
            rows = importTab.find('.import-row.edit.valid').not('fieldset .row');
        if(importTab.find('.form-actions').is('.invalid'))
            return;
        importTab.find('.form-actions').removeClass('valid').addClass('invalid');
        rows.each(function () {
            var that = jQuery(this);
            users[users.length] = {
                first: that.find('.first-name input').val(),
                last: that.find('.last-name input').val(),
                email: that.find('.email input').val()
            };
        });
        jQuery.ajax({
            url: window.callbackPaths['import_save'],
            type: 'POST',
            dataType: 'json',
            data: {
                users: users
            },
            success: function (data)
            {
            }
        });
    });
});