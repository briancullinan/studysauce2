$(document).ready(function () {

    var body = $('body');

    body.on('click', '#validation a[href="#run-test"]', function (evt) {
        evt.preventDefault();
        var validation = $('#validation'),
            row = $(this).parents('tr'),
            suite = (/suite-(.*?)(\s|$)/ig).exec(row.attr('class'))[1],
            test = (/test-id-(.*?)(\s|$)/ig).exec(row.attr('class'))[1];
        $.ajax({
            url: window.callbackPaths['validation_test'],
            type: 'POST',
            dataType: 'text',
            data: {
                suite: suite,
                test: test,
                host: validation.find('.host-setting input').val().trim(),
                browser: validation.find('.browser-setting select').val().trim(),
                wait: validation.find('.wait-setting input').val().trim(),
                url: validation.find('.url-setting input').val().trim()
            },
            success: function (response) {
                var content = $(response);
                content.filter('[class*="test-id-"]').each(function () {
                    var test = (/test-id-(.*?)(\s|$)/ig).exec($(this).attr('class'))[1];
                    validation.find('tr.test-id-' + test + ' td:nth-child(2) *').remove();
                    $(this).appendTo(validation.find('tr.test-id-' + test + ' td:nth-child(2)'));
                });
                content.not(content.filter('[class*="test-id-"]')).prependTo(row.find('td:nth-child(2)'));
            }
        });
    });

    body.on('click', '#validation h3', function () {
        $(this).toggleClass('selected');
    });

    body.on('click', '#validation code', function () {
        var text = this;
        var range, selection;
        if (document.body.createTextRange) {
            range = document.body.createTextRange();
            range.moveToElementText(text);
            range.select();
        } else if (window.getSelection) {
            selection = window.getSelection();
            range = document.createRange();
            range.selectNodeContents(text);
            selection.removeAllRanges();
            selection.addRange(range);
        }
    });

    body.on('click', '#validation a[href="#run-all"]', function (evt) {
        evt.preventDefault();
        var validation = $('#validation'),
            suite = (/suite-(.*?)(\s|$)/ig).exec($(this).attr('class'))[1];
        $.ajax({
            url: window.callbackPaths['validation_test'],
            type: 'POST',
            dataType: 'text',
            data: {
                suite: suite,
                test: validation.find('tr:has(td:nth-child(3) input:checked)')
                    .map(function () { return (/test-id-(.*?)(\s|$)/ig).exec($(this).attr('class'))[1]; }).toArray()
                    .join('|'),
                host: validation.find('.host-setting input').val().trim(),
                browser: validation.find('.browser-setting select').val().trim(),
                wait: validation.find('.wait-setting input').val().trim(),
                url: validation.find('.url-setting input').val().trim()
            },
            success: function (response) {
                var content = $(response),
                    first = (/test-id-(.*?)(\s|$)/ig).exec(content.filter('[class*="test-id-"]').first().attr('class'))[1];
                content.filter('[class*="test-id-"]').each(function () {
                    var test = (/test-id-(.*?)(\s|$)/ig).exec($(this).attr('class'))[1];
                    validation.find('tr.test-id-' + test + ' td:nth-child(2)').html('');
                    $(this).appendTo(validation.find('tr.test-id-' + test + ' td:nth-child(2)'));
                });
                content.not(content.filter('[class*="test-id-"]')).prependTo(validation.find('tr.test-id-' + first + ' td:nth-child(2)'));
            }
        });
    });

    function highlight() {
        var validation = $('#validation'),
            re = /depends-on-(.*?)(\s|$)/ig,
            match;
        while(match = re.exec($(this).attr('class'))) {
            validation.find('tr.test-id-' + match[1]).not('.dependency').addClass('dependency').each(highlight);
        }
        var ire = /includes-(.*?)(\s|$)/ig;
        while(match = ire.exec($(this).attr('class'))) {
            validation.find('tr.test-id-' + match[1]).not('.dependency').addClass('included').each(highlight);
        }
    }

    body.on('mouseover', '#validation tbody tr', highlight);

    function removeHighlight() {
        var validation = $('#validation'),
            re = /depends-on-(.*?)(\s|$)/ig,
            match;
        while(match = re.exec($(this).attr('class'))) {
            validation.find('tr.test-id-' + match[1]).filter('.dependency').removeClass('dependency').each(removeHighlight);
        }
        var ire = /includes-(.*?)(\s|$)/ig;
        while(match = ire.exec($(this).attr('class'))) {
            validation.find('tr.test-id-' + match[1]).filter('.included').removeClass('included').each(removeHighlight);
        }
    }

    body.on('mouseout', '#validation tbody tr', removeHighlight);

});