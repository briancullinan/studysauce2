$(document).ready(function () {

    var body = $('body');

    body.on('click', '#calculator .term-row > *:not(.term-editor)', function () {
        var calc = body.find('#calculator'),
            row = $(this).parents('.term-row');
        if(!row.is('.selected')) {
            calc.find('.term-row.selected').removeClass('selected');
            row.addClass('selected');
        }
    });

    body.on('click', '#calculator .class-row > *:not(.grade-editor):not(.hours),#calculator .class-row:not(.selected) > *.hours', function () {
        var row = $(this).parents('.class-row');
        if(row.is('.selected')) {
            row.removeClass('selected');
            row.find('.hours').addClass('read-only');
        }
        else {
            row.addClass('selected');
            row.find('.hours').removeClass('read-only');
        }
    });

    function convertToScale(score)
    {
        var scaled = [null, null];
        $('#grade-scale').find('tbody tr').each(function () {
            if(score <= parseInt($(this).find('td:nth-child(2) input').val()) &&
                    score >= parseInt($(this).find('td:nth-child(3) input').val()))
            {
                scaled = [$(this).find('td:nth-child(1) input').val(), $(this).find('td:nth-child(4) input').val()];
                return false;
            }
        });
        return scaled;
    }

    function validateGrades()
    {
        var calc = body.find('#calculator'),
            overallHours = 0,
            overallGPA = 0;
        calc.find('.term-row').each(function () {
            var term = $(this),
                hours = 0,
                termPercent = 0,
                termGPA = 0;

            term.find('.class-row').each(function () {
                var row = $(this),
                    percent = 0,
                    sum = 0,
                    courseHours = parseInt(row.find('.hours input').val().trim());
                row.find('.grade-row:not(.deleted)').each(function () {
                    var that = $(this),
                        score = parseInt(that.find('.score input').val().trim()),
                        rowPercent = parseInt(that.find('.percent input').val().trim());
                    // valid if assignment is filled in or all fields are blank is the same as remove
                    if(that.find('.assignment input').val().trim() != '' || (
                        that.find('.score input').val().trim() == '' &&
                        that.find('.percent input').val().trim() == '' &&
                        that.find('.assignment input').val().trim() == ''))
                        that.removeClass('invalid').addClass('valid');
                    else
                        that.removeClass('valid').addClass('invalid');

                    if(isNaN(score)) {
                        that.find('.grade span, .gpa').html('&bullet;');
                    }
                    else {
                        if(!isNaN(rowPercent)) {
                            percent += rowPercent;
                            sum += score * rowPercent;
                        }

                        var scaled = convertToScale(score);
                        that.find('.grade span').html(scaled[0]);
                        that.find('.gpa').html(scaled[1]);
                    }
                });
                if(percent == 0) {
                    row.find('> .score, > .gpa, > .percent, > .grade span').html('&bullet;');
                }
                else {
                    var classScore = sum / percent,
                        scaled = convertToScale(classScore);
                    row.find('> .score').html(Math.round(classScore * 10) / 10);
                    row.find('> .grade span').html(scaled[0]);
                    row.find('> .gpa').html(scaled[1]);
                    row.find('> .percent').html(percent + '%');

                    if(!isNaN(courseHours)) {
                        hours += courseHours;
                        termGPA += parseFloat(scaled[1] * courseHours);
                        termPercent += percent * courseHours;
                    }
                }
            });

            if(hours == 0) {
                term.find('> .gpa span, > .percent, > .hours').html('&bullet;');
                calc.find('.projected').html('&bullet;');
            }
            else {
                if(term.index(calc.find('.term-row')) == 0)
                    calc.find('.projected').html(Math.round(termGPA / hours * 100) / 100);
                term.find('> .gpa span').html((Math.round(termGPA / hours * 10) / 10));
                if(term.find('> .gpa span').html().length == 3) {
                    term.find('> .gpa span').html(term.find('> .gpa span').html() + '0');
                }
                if(term.find('> .gpa span').html().length == 1 && !isNaN(parseInt(term.find('> .gpa span').html()))) {
                    term.find('> .gpa span').html(term.find('> .gpa span').html() + '.00');
                }
                term.find('> .percent').html((Math.round(termPercent / hours * 10) / 10) + '%');
                term.find('> .hours').html(hours + ' hrs');
            }

            overallGPA += termGPA;
            overallHours += hours;

            if(calc.find('.grade-row.edit.invalid').length == 0) {
                if(calc.find('.grade-row.edit').length > 0 || calc.find('.grade-row:not(:visible)').length > 0)
                    calc.find('.form-actions').removeClass('invalid').addClass('valid');
            }
            else
                calc.find('.form-actions').removeClass('valid').addClass('invalid');
        });

        if(overallHours == 0)
            calc.find('.cumulative').html('&bullet;');
        else
            calc.find('.cumulative').html(Math.round(overallGPA / overallHours * 100) / 100);

        if(calc.find('.cumulative').html().length == 3) {
            calc.find('.cumulative').html(calc.find('.cumulative').html() + '0');
        }
        if(calc.find('.cumulative').html().length == 1 && !isNaN(parseInt(calc.find('.cumulative').html()))) {
            calc.find('.cumulative').html(calc.find('.cumulative').html() + '.00');
        }
        if(calc.find('.projected').html().length == 3) {
            calc.find('.projected').html(calc.find('.projected').html() + '0');
        }
        if(calc.find('.projected').html().length == 1 && !isNaN(parseInt(calc.find('.projected').html()))) {
            calc.find('.projected').html(calc.find('.projected').html() + '.00');
        }
    }

    body.on('click', '#calculator a[href="#add-schedule"]', function (evt) {
        evt.preventDefault();
        body.one('show', '#schedule', function () {
            $('#schedule').find('a[href="#create-schedule"]').first().trigger('click');
        });
        $('#calculator').find('a[href*="/schedule"]').first().trigger('click');
    });

    body.on('click', '#calculator a[href="#what-if"]', function () {
        var calc = $('#calculator');
        if($(this).parent().index() == 1) {
            // set the fields to their current value
            calc.find('#what-if select:first-child').val(Math.round(parseFloat(calc.find('.projected').html()) * 10) / 10);
        }
    });

    body.on('click', '#calculator a[href*="/schedule"]', function () {
        var term = $(this).parents('.term-row'),
            scheduleId = (/schedule-id-([0-9]*)(\s|$)/ig).exec(term.attr('class'))[1];
        body.one('show', '#schedule', function () {
            var schedule = $('#schedule');
            schedule.find('.term-row').hide();
            schedule.find('.schedule-id-' + scheduleId).show();
            if(schedule.find('.term-row:visible').is(schedule.find('.term-row').last()))
                schedule.find('a[href="#next-schedule"]').addClass('disabled');
            else
                schedule.find('a[href="#next-schedule"]').removeClass('disabled');
            if(schedule.find('.term-row:visible').is(schedule.find('.term-row').first()))
                schedule.find('a[href="#prev-schedule"]').addClass('disabled');
            else
                schedule.find('a[href="#prev-schedule"]').removeClass('disabled');
        });
    });

    body.on('click', '#calculator a[href="#add-grade"]', function (evt) {
        evt.preventDefault();
        var editor = $(this).parents('.grade-editor'),
            row = editor.find('.grade-row').first()
                .clone().show()
                .removeClass('read-only').addClass('edit deleted')
                .insertAfter(editor.find('.grade-row').last());
        row.attr('class', row.attr('class').replace(/grade-id-([0-9]*)(\s|$)/ig, ' grade-id- '));
        row.find('.grade span, .gpa').html('&bullet;');
        row.find('input').val('').attr('placeholder', '');
        row.find('.assignment input').attr('placeholder', 'Assignment')
    });

    body.on('scheduled', function () {
        // update classes
        setTimeout(function () {
            $.ajax({
                url: window.callbackPaths['calculator'],
                type: 'GET',
                dataType: 'text',
                success: updateCalc
            });
        }, 100);
    });

    function updateCalc(data)
    {
        var response = $(data),
            calc = $('#calculator');
        calc.find('.term-row').remove();
        response.find('.term-row').insertBefore(calc.find('.form-actions').last());
        calc.find('.projected').html(response.find('.projected').html());
        calc.find('.cumulative').html(response.find('.cumulative').html());
        if(response.filter('#calculator').is('.empty')) {
            calc.addClass('empty');
            $('#calc-empty').modal({
                backdrop: 'static',
                keyboard: false,
                show: calc.is(':visible')
            });
        }
        else {
            calc.removeClass('empty');
            $('#calc-empty').modal('hide');
        }
    }

    function submitCalc(evt)
    {
        evt.preventDefault();
        var calc = $('#calculator');
        if(calc.find('.form-actions').is('invalid'))
            return;

        calc.find('.form-actions').removeClass('valid').addClass('invalid');
        loadingAnimation(calc.find('[value="#save-grades"]'));

        var data = {courses: [], scale: $('#grade-scale').find('tbody tr').map(function () { return [[
                $(this).find('td:nth-child(1) input').val(),
                $(this).find('td:nth-child(2) input').val(),
                $(this).find('td:nth-child(3) input').val(),
                $(this).find('td:nth-child(4) input').val()
            ]]; }).toArray()};
        calc.find('.class-row').each(function (i) {
            var row = $(this),
                courseId = (/course-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1],
                courseData = {
                    courseId: courseId,
                    creditHours: parseInt(row.find('.hours input').val().trim()),
                    grades: {}
                };
            row.find('.grade-row.edit.valid').each(function (j) {
                var that = $(this),
                    gradeId = (/grade-id-([0-9]*)(\s|$)/ig).exec(that.attr('class'))[1];
                courseData.grades[j] = {
                    gradeId: gradeId,
                    assignment: that.find('.assignment input').val().trim(),
                    score: that.find('.score input').val().trim(),
                    percent: that.find('.percent input').val().trim(),
                    remove: that.find('.score input').val().trim() == '' &&
                            that.find('.percent input').val().trim() == '' &&
                            that.find('.assignment input').val().trim() == ''
                }
            });
            data.courses[i] = courseData;
        });
        $.ajax({
            url: window.callbackPaths['calculator_update'],
            type: 'POST',
            dataType: 'text',
            data: data,
            success: updateCalc
        });
    }


    body.on('submit', '#calculator form', submitCalc);

    body.on('click', '#calculator a[href="#edit-grade"]', function (evt) {
        evt.preventDefault();
        $(this).parents('.grade-row').removeClass('read-only').addClass('edit');
        validateGrades();
    });

    body.on('click', '#calculator a[href="#remove-grade"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('.grade-row'),
            classRow = row.parents('.class-row');
        row.addClass('edit').hide();
        row.find('.score input, .percent input, .assignment input').val('');
        // create 4 empty rows if this is the last one
        if(classRow.find('.grade-row:visible').length == 0)
            classRow.find('a[href="#add-grade"]').trigger('click').trigger('click').trigger('click').trigger('click');
        validateGrades();
    });

    body.on('click', '#grade-scale a[href="#scale-preset"]', function (evt) {
        evt.preventDefault();
        var preset = window.presetScale[$(this).html()],
            dialog = $('#grade-scale');
        dialog.find('li.active').removeClass('active');
        $(this).parent().addClass('active');
        dialog.find('tbody input').val('');
        for(var i = 0; i < preset.length; i++) {
            var gpa = preset[i][3] + '';
            if(gpa.length == 1)
                gpa += '.00';
            if(gpa.length == 3)
                gpa += '0';
            dialog.find('tbody tr').eq(i).find('input').eq(0).val(preset[i][0]);
            dialog.find('tbody tr').eq(i).find('input').eq(1).val(preset[i][1]);
            dialog.find('tbody tr').eq(i).find('input').eq(2).val(preset[i][2]);
            dialog.find('tbody tr').eq(i).find('input').eq(3).val(gpa);
        }
    });

    body.on('click', '#calculator a[href="#what-if"]', function () {
        // update course list in dialog
        var calc = $('#calculator'),
            dialog = $('#what-if'),
            firstRow = dialog.find('.class-row').first(),
            classes = calc.find('.term-row').first().find('.class-row');
        dialog.find('select.class-name').find('option').remove();
        dialog.find('.class-row').not(firstRow).remove();
        classes.each(function (i) {
            var row = $(this),
                courseId = (/course-id-([0-9]+)(\s|$)/ig).exec(row.attr('class'))[1];
            $('<option value="' + courseId + '">' + row.find('.class-name').text() + '</option>')
                .appendTo(dialog.find('select.class-name'));
            firstRow.find('.class-name').html(row.find('.class-name').html());
            firstRow.find('.hours').text(row.find('.hours input').val());
            firstRow.find('select').val(row.find('> .grade span').text());
            if(i < classes.length - 1)
                firstRow.clone().insertAfter(firstRow);
        });
        dialog.find('.overall-gpa').val(Math.round(parseFloat(calc.find('.cumulative').text()) * 10) / 10);
    });

    body.on('click', '#grade-scale a[href="#save-scale"]', validateGrades);
    body.on('keyup', '#calculator .grade-row input, #calculator .class-row input', validateGrades);
    body.on('change', '#calculator .grade-row input, #calculator .class-row input', validateGrades);

    body.on('show', '#calculator', function () {
        if($(this).is('.empty'))
            $('#calc-empty').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        else
            $('#calc-empty').modal('hide');
        validateGrades();
    })
});