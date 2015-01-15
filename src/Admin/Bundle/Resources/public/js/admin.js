$(document).ready(function () {

    var body = $('body'),
        orderBy = 'lastLogin DESC',
        searchTimeout = null;

    function sortSelect(a, b) {
        if(a == 'Student' || a == 'Status' || a == 'Adviser' || a == 'School' || a == 'Date' || a == 'Completion')
            return -1;
        if(b == 'Student' || b == 'Status' || b == 'Adviser' || b == 'School' || b == 'Date' || a == 'Completion')
            return 1;
        if(a == 'Ascending (A-Z)' && b == 'Descending (Z-A)')
            return -1;
        if(b == 'Ascending (A-Z)' && a == 'Descending (Z-A)')
            return 1;
        if(a == 'Ascending (A-Z)' || a == 'Descending (Z-A)')
            return -1;
        if(b == 'Ascending (A-Z)' || b == 'Descending (Z-A)')
            return 1;

        if (a.toUpperCase() > b.toUpperCase())
            return 1;
        if (a.toUpperCase() < b.toUpperCase())
            return -1;
        // a must be equal to b
        return 0;
    }

    function loadContent (data) {
        var admin = jQuery('#admin'),
            content = $(data);
        admin.find('.scroller tr').remove();
        content.find('.scroller tr').appendTo(admin.find('.scroller tbody'));
        admin.find('th').each(function (i) {
            $(this).find('label > *:not(select)').remove();
            content.find('th').eq(i).find('label > *:not(select)').prependTo($(this).find('label'));
        });
        admin.find('#page-total').text(content.find('#page-total').text());
    }

    function getData()
    {
        var admin = jQuery('#admin');
        var result = {
            order: orderBy,
            search: admin.find('input[name="search"]').val().trim(),
            page: admin.find('input[name="page"]').val().trim(),
            role: admin.find('select[name="role"]').val().trim(),
            group: admin.find('select[name="group"]').val().trim(),
            last: admin.find('select[name="last"]').val().trim(),
            completed: admin.find('select[name="completed"]').val().trim(),
            paid: admin.find('select[name="hasPaid"]').val().trim(),
            goals: admin.find('select[name="hasGoals"]').val().trim(),
            deadlines: admin.find('select[name="hasDeadlines"]').val().trim(),
            schedules: admin.find('select[name="hasSchedules"]').val().trim(),
            partners: admin.find('select[name="hasPartners"]').val().trim(),
        };

        for(var i = 1; i <= 17; i++) {
            result['lesson' + i] = admin.find('select[name="lesson' + i + '"]').val().trim();
        }

        return result;
    }

    function loadResults() {
        $.ajax({
            url: window.callbackPaths['command_callback'],
            type: 'GET',
            dataType: 'text',
            data: getData(),
            success: loadContent
        });
    }

    body.on('keyup', '#admin input[name="search"], #admin input[name="page"]', function () {
        if(searchTimeout != null)
            clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadResults, 1000);
    });

    body.on('click', '#admin a[href="#edit-user"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('tr');
        row.removeClass('read-only').addClass('edit');
    });

    body.on('click', '#admin a[href="#confirm-remove-user"]', function (evt) {
        evt.preventDefault();
        var admin = $('#admin'),
            row = $(this).parents('tr'),
            userId = (/user-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        $('#remove-user-name').text(row.find('td').eq('3').find('a').first().text());
        $('#confirm-remove-user').data('userId', userId);
    });

    body.on('click', '#admin a[href="#confirm-cancel-user"]', function (evt) {
        evt.preventDefault();
        var admin = $('#admin'),
            row = $(this).parents('tr'),
            userId = (/user-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        $('#cancel-user-name').text(row.find('td').eq('3').find('a').first().text());
        $('#confirm-cancel-user').data('userId', userId);
    });

    body.on('click', '#admin a[href="#confirm-password-reset"]', function (evt) {
        evt.preventDefault();
        var admin = $('#admin'),
            row = $(this).parents('tr'),
            userId = (/user-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        $('#reset-user-name').text(row.find('td').eq('3').find('a').first().text());
        $('#confirm-password-reset').data('userId', userId);
    });

    body.on('click', 'a[href="#remove-user"]', function () {
        var data = getData();
        data.userId = $('#confirm-remove-user').data('userId');
        $.ajax({
            url: window.callbackPaths['remove_user'],
            type: 'POST',
            dataType: 'text',
            data: data,
            success: loadContent
        });
    });

    body.on('click', 'a[href="#cancel-user"]', function () {
        var data = getData();
        data.userId = $('#confirm-cancel-user').data('userId');
        $.ajax({
            url: window.callbackPaths['cancel_user'],
            type: 'POST',
            dataType: 'text',
            data: data,
            success: loadContent
        });
    });

    body.on('click', 'a[href="#reset-password"]', function () {
        var data = getData();
        data.userId = $('#confirm-password-reset').data('userId');
        $.ajax({
            url: window.callbackPaths['reset_user'],
            type: 'POST',
            dataType: 'text',
            data: data,
            success: loadContent
        });
    });

    body.on('click', '#admin a[href="#all"], #admin a[href="#courses"], #admin a[href="#tools"]', function (evt) {
        evt.preventDefault();
        var admin = $('#admin');
        admin.removeClass('all courses tools');
        admin.addClass(this.hash.substr(1));
    });

    body.on('click', '#admin .paginate a', function (evt) {
        evt.preventDefault();
        var admin = $('#admin'),
            page = this.search.match(/([0-9]*|last|prev|next|first)$/i)[0],
            current = parseInt(admin.find('input[name="page"]').val());
        if(page == 'first')
            page = 1;
        if(page == 'next')
            page = current + 1;
        if(page == 'prev')
            page = current - 1;
        if(page == 'last')
            page = parseInt(admin.find('#page-total').text());
        admin.find('input[name="page"]').val(page);
        if(searchTimeout != null)
            clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadResults, 100);
    });

    body.on('click', '#admin .scroller tr.read-only', function () {
        $(this).find('input[name="selected"]')
            .prop('checked', !$(this).find('input[name="selected"]').prop('checked'));
    });

    body.on('change', '#admin select, #admin input[name="search"], #admin input[name="page"]', function () {
        var admin = jQuery('#admin');

        admin.find('select').each(function () {
            var that = $(this);

            if(that.val() == '_ascending' || that.val() == '_descending')
            {
                orderBy = that.attr('name') + (that.val() == '_ascending' ? ' ASC' : ' DESC');
                that.val(that.data('last') || that.find('option').first().attr('value'));
            }
            else if(that.val().trim() != '')
            {
                that.parents('th').removeClass('unfiltered').addClass('filtered');
                that.data('last', that.val());
            }
            else
            {
                that.parents('th').removeClass('filtered').addClass('unfiltered');
                that.data('last', that.val());
            }

        });

        if(searchTimeout != null)
            clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadResults, $(this).is('input') ? 1000 : 100);
    });


});