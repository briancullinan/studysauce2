$(document).ready(function () {

    var body = $('body');

    body.on('click', '#validation a[href="#run-test"]', function (evt) {
        evt.preventDefault();
        var validation = $('#validation'),
            row = $(this).parents('tr'),
            suite = (/suite-(.*?)(\s|$)/ig).exec(row.attr('class'))[1];
        $.ajax({
            url: window.callbackPaths['validation_test'],
            type: 'POST',
            dataType: 'json',
            data: {
                suite: suite,
                test: row.find('td:first-child').text(),
                host: validation.find('.host-setting input').val().trim(),
                browser: validation.find('.browser-setting select').val().trim(),
                wait: validation.find('.wait-setting input').val().trim(),
                url: validation.find('.url-setting input').val().trim()
            },
            success: function (response) {
                if (typeof response == 'object') {
                    if (typeof response.errors != 'undefined') {
                        row.find('td:nth-child(2)').text(response.errors);
                    }
                    if (typeof response.result != 'undefined') {
                        row.find('td:nth-child(2)').html(response.result);
                    }

                }
            }
        });
    });

    body.on('click', '#validation a[href="#run-all"]', function (evt) {
        evt.preventDefault();
        var validation = $('#validation'),
            suite = (/suite-(.*?)(\s|$)/ig).exec($(this).attr('class'))[1];
        $.ajax({
            url: window.callbackPaths['validation_test'],
            type: 'POST',
            dataType: 'json',
            data: {
                suite: suite,
                host: validation.find('.host-setting input').val().trim(),
                browser: validation.find('.browser-setting select').val().trim(),
                wait: validation.find('.wait-setting input').val().trim(),
                url: validation.find('.url-setting input').val().trim()
            },
            success: function (response) {
                if (typeof response == 'object') {
                    if (typeof response.errors != 'undefined') {

                    }
                    if (typeof response.result != 'undefined') {

                    }
                }
            }
        });
    });

});