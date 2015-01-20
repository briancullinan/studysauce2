$(document).ready(function () {

    var body = $('body');

    body.on('click', '#validation a[href="#run-test"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('tr'),
            suite = (/suite-(.*?)(\s|$)/ig).exec(row.attr('class'))[1];
        $.ajax({
            url: window.callbackPaths['validation_test'],
            type: 'POST',
            dataType: 'json',
            data: {
                suite: suite,
                test: row.find('td:first-child').text()
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
    })

});