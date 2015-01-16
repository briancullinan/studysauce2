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
        admin.find('table.results > tbody > tr').remove();
        content.find('table.results > tbody > tr').appendTo(admin.find('table.results > tbody'));
        admin.find('table.results > thead > tr > th').each(function (i) {
            $(this).find('label > *:not(select):not(input)').remove();
            content.find('.pane-content th').eq(i).find('label > *:not(select):not(input)').prependTo($(this).find('label'));
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
            partners: admin.find('select[name="hasPartners"]').val().trim()
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

    body.on('click', '#admin a[href="#edit-user"], #group-manager a[href="#edit-group"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('tr');
        row.removeClass('read-only').addClass('edit');
    });

    body.on('click', '#admin a[href="#cancel-edit"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('tr');
        row.removeClass('edit').addClass('read-only');
    });

    body.on('click', '#group-manager a[href="#cancel-edit"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('tr'),
            groupId = (/group-id-([0-9]*)(\s|$)/ig).exec(row.attr('class'))[1];
        row.removeClass('edit').addClass('read-only');
        if(groupId == '') {
            row.remove();
        }
    });

    body.on('click', '#group-manager a[href="#add-group"]', function (evt) {
        evt.preventDefault();
        var manager = $('#group-manager'),
            newRow = manager.find('tbody tr').first().clone().attr('class', 'group-id- edit');
        newRow.find('input[type="checkbox"]').prop('checked', false);
        newRow.find('input[type="text"], textarea').val('');
        newRow.find('td:nth-child(4)').text('0');
        newRow.prependTo(manager.find('tbody'));
    });

    body.on('click', '#group-manager a[href="#save-group"]', function (evt) {
        evt.preventDefault();
        var data = getData(),
            row = $(this).parents('tr');
        if(row.is('.invalid'))
            return;
        row.removeClass('edit').addClass('read-only');
        data.groupName = row.find('input[name="groupName"]').val().trim();
        data.description = row.find('textarea[name="description"]').val().trim();
        data.roles = row.find('input[name="roles"]:checked').map(function () {return $(this).val();}).toArray().join(',');
        data.groupId = (/group-id-([0-9]*)(\s|$)/ig).exec(row.attr('class'))[1];
        $.ajax({
            url: window.callbackPaths['save_group'],
            type: 'POST',
            dataType: 'text',
            data: data,
            success: function (response) {
                var admin = $('#admin'),
                    content = $(response),
                    current = admin.find('th:nth-child(3) select').val();
                // update group select in heading
                admin.find('th:nth-child(3) select').replaceWith(content.find('.pane-content th:nth-child(3) select').val(current));
                if(data.groupName == '_remove') {
                    row.remove();
                }
                if(data.groupId == '') {

                }
                loadContent(response);
            }
        });
    });

    body.on('click', '#add-user a[href="#add-user"]', function (evt) {
        evt.preventDefault();
        var admin = $('#admin'),
            dialog = $('#add-user'),
            data = {
                first: dialog.find('.first-name input').val().trim(),
                last: dialog.find('.last-name input').val().trim(),
                email: dialog.find('.email input').val().trim(),
                pass: dialog.find('.password input').val()
            };
        if(dialog.is('.invalid'))
            return;
        dialog.removeClass('valid').addClass('invalid');
        $.ajax({
            url: window.callbackPaths['add_user'],
            type: 'POST',
            dataType: 'text',
            data: data,
            success: function (response) {
                // reset dialog fields for next entry
                dialog.find('.first-name input').val('');
                dialog.find('.last-name input').val('');
                dialog.find('.email input').val('');
                dialog.find('.password input').val('');

                // show that filtering is showing users with this last name
                admin.find('th').each(function (i) {
                    if(i == 3) {
                        $(this).find('select').val(data.last.substr(0, 1).toUpperCase() + '%');
                        $(this).removeClass('unfiltered').addClass('filtered');
                    }
                    else {
                        $(this).removeClass('filtered').addClass('unfiltered');
                    }
                });

                loadContent(response);
            }
        });
    });

    body.on('show', '#admin', function () {
        var admin = $('#admin'),
            pickers = admin.find('th:nth-child(1) .input + div, th:nth-child(6) .input + div');
        $.datepicker._defaults.onAfterUpdate = null;

        var datepicker__updateDatepicker = $.datepicker._updateDatepicker;
        $.datepicker._updateDatepicker = function( inst ) {
            datepicker__updateDatepicker.call( this, inst );

            var onAfterUpdate = this._get(inst, 'onAfterUpdate');
            if (onAfterUpdate)
                onAfterUpdate.apply((inst.input ? inst.input[0] : null),
                    [(inst.input ? inst.input.val() : ''), inst]);
        };

        var cur = -1, prv = -1;
        pickers
            .datepicker({
                //numberOfMonths: 3,
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,

                beforeShowDay: function ( date ) {
                    return [true, ( (date.getTime() >= Math.min(prv, cur) && date.getTime() <= Math.max(prv, cur)) ? 'date-range-selected' : '')];
                },

                onSelect: function ( dateText, inst ) {
                    prv = cur;
                    cur = (new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay)).getTime();
                    if ( prv == -1 || prv == cur ) {
                        prv = cur;
                        $(this).prev().find('input').val( dateText );
                    } else {
                        var d1 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.min(prv,cur)), {} ),
                            d2 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.max(prv,cur)), {} );
                        $(this).prev().find('input').val( d1+' - '+d2 );
                    }
                },

                onChangeMonthYear: function ( year, month, inst ) {
                    //prv = cur = -1;
                },

                onAfterUpdate: function ( inst ) {
                    var that = $(this);
                    $('<a href="#everything">All</a>')
                        .insertBefore($(this).find('.ui-datepicker-header'))
                        .on('click', function (evt) {
                            evt.preventDefault();
                            cur = that.datepicker('option', 'maxDate');
                            prv = that.datepicker('option', 'minDate');
                            that.prev().find('input').val('').trigger('change');
                            that.hide();
                        });
                    $('<a href="#yesterday">Since Yesterday</a>')
                        .insertBefore($(this).find('.ui-datepicker-header'))
                        .on('click', function (evt) {
                            evt.preventDefault();
                            var yesterday = new Date();
                            yesterday.setHours(0, 0, 0, 0);
                            yesterday.setTime(yesterday.getTime() - 86400000);
                            prv = yesterday.getTime();
                            cur = (new Date()).getTime();
                            var d1 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.min(prv,cur)), {}),
                                d2 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.max(prv,cur)), {} );
                            that.prev().find('input').val( d1+' - '+d2 ).trigger('change');
                            that.hide();
                        });
                    $('<a href="#week">This Week</a>')
                        .insertBefore($(this).find('.ui-datepicker-header'))
                        .on('click', function (evt) {
                            evt.preventDefault();
                            var lastSunday = new Date();
                            lastSunday.setHours(0, 0, 0, 0);
                            lastSunday.setDate(lastSunday.getDate() - lastSunday.getDay());
                            prv = lastSunday.getTime();
                            cur = (new Date()).getTime();
                            var d1 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.min(prv,cur)), {}),
                                d2 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.max(prv,cur)), {} );
                            that.prev().find('input').val( d1+' - '+d2 ).trigger('change');
                            that.hide();
                        });
                    $('<a href="#month">This Month</a>')
                        .insertBefore($(this).find('.ui-datepicker-header'))
                        .on('click', function (evt) {
                            evt.preventDefault();
                            var theFirst = new Date();
                            theFirst.setHours(0, 0, 0, 0);
                            theFirst.setDate(1);
                            prv = theFirst.getTime();
                            cur = (new Date()).getTime();
                            var d1 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.min(prv,cur)), {}),
                                d2 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.max(prv,cur)), {} );
                            that.prev().find('input').val( d1+' - '+d2 ).trigger('change');
                            that.hide();
                        });
                    $('<a href="#year">This Year</a>')
                        .insertBefore($(this).find('.ui-datepicker-header'))
                        .on('click', function (evt) {
                            evt.preventDefault();
                            var foty = new Date();
                            foty.setHours(0, 0, 0, 0);
                            foty.setMonth(0, 1);
                            prv = foty.getTime();
                            cur = (new Date()).getTime();
                            var d1 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.min(prv,cur)), {}),
                                d2 = $.datepicker.formatDate( 'mm/dd/y', new Date(Math.max(prv,cur)), {} );
                            that.prev().find('input').val( d1+' - '+d2 ).trigger('change');
                            that.hide();
                        });

                    $('<button type="button" class="ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all" data-handler="hide" data-event="click">Done</button>')
                        .appendTo($(this).find('.ui-datepicker-buttonpane'))
                        .on('click', function () {
                            that.prev().find('input').trigger('change');
                            that.hide();
                        });
                }
            })
            .each(function (i) {
                $(this).position({
                    my: 'left top',
                    at: 'left bottom',
                    of: admin.find('th:nth-child(1) input, th:nth-child(6) input').eq(i)
                });
            })
            .hide();

        body.on('focus', 'th:nth-child(1) input, th:nth-child(6) input', function (e) {
            var v = this.value,
                d;

            try {
                if ( v.indexOf(' - ') > -1 ) {
                    d = v.split(' - ');

                    prv = $.datepicker.parseDate( 'mm/dd/y', d[0] ).getTime();
                    cur = $.datepicker.parseDate( 'mm/dd/y', d[1] ).getTime();

                } else if ( v.length > 0 ) {
                    prv = cur = $.datepicker.parseDate( 'mm/dd/y', v ).getTime();
                }
            } catch ( e ) {
                cur = prv = -1;
            }

            if ( cur > -1 )
                $(this).parent().next().datepicker('setDate', new Date(cur));

            $(this).parent().next().datepicker('refresh').show();
        });

    });

    body.on('click', '#admin a[href="#save-user"]', function (evt) {
        evt.preventDefault();
        var data = getData(),
            row = $(this).parents('tr');
        if(row.is('.invalid'))
            return;
        row.removeClass('edit').addClass('read-only');
        data.groups = row.find('input[name="groups"]:checked').map(function () {return $(this).val();}).toArray().join(',');
        data.roles = row.find('input[name="roles"]:checked').map(function () {return $(this).val();}).toArray().join(',');
        data.firstName = row.find('input[name="first-name"]').val().trim();
        data.lastName = row.find('input[name="last-name"]').val().trim();
        data.email = row.find('input[name="email"]').val().trim();
        data.userId = (/user-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        $.ajax({
            url: window.callbackPaths['save_user'],
            type: 'POST',
            dataType: 'text',
            data: data,
            success: loadContent
        });
    });

    body.on('click', '#admin a[href="#confirm-remove-user"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('tr'),
            userId = (/user-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        $('#remove-user-name').text(row.find('td').eq('3').find('input').first().val());
        $('#confirm-remove-user').data('userId', userId);
    });

    body.on('click', '#admin a[href="#confirm-cancel-user"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('tr'),
            userId = (/user-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        $('#cancel-user-name').text(row.find('td').eq('3').find('input').first().val());
        $('#confirm-cancel-user').data('userId', userId);
    });

    body.on('click', '#admin a[href="#confirm-password-reset"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('tr'),
            userId = (/user-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
        $('#reset-user-name').text(row.find('td').eq('3').find('input').first().val());
        $('#confirm-password-reset').data('userId', userId);
    });

    body.on('click', '#confirm-remove-user a[href="#remove-user"]', function () {
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

    body.on('click', '#confirm-cancel-user a[href="#cancel-user"]', function () {
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

    body.on('click', '#confirm-password-reset a[href="#reset-password"]', function () {
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

    body.on('click', '#admin table.results > tbody > tr.read-only', function () {
        $(this).find('input[name="selected"]')
            .prop('checked', !$(this).find('input[name="selected"]').prop('checked'));
    });

    body.on('change', '#admin table.results > thead > tr > th > label > select, #admin > thead > tr > th > label > input', function () {
        var admin = jQuery('#admin'),
            that = $(this);

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

        if(searchTimeout != null)
            clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadResults, 100);
    });

    body.on('change', '#admin input[name="search"], #admin input[name="page"]', function () {
        if(searchTimeout != null)
            clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadResults, 100);
    });


});