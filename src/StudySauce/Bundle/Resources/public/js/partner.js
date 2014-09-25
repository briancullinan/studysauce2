$(document).ready(function () {
    $('#partner input[type="checkbox"]').each(function () {
        $(this).data('origState', $(this).prop('checked'));
    });
    $('#partner').on('change', 'input[type="checkbox"]', function (evt) {
        evt.preventDefault();
        if ($(this).prop('checked') != $(this).data('origState'))
            $(this).prop('checked', $(this).data('origState'));
    });
});