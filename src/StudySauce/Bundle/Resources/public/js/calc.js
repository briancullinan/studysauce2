$(document).ready(function () {

    var body = $('body');

    body.on('click', '#calculator .term-row > *:not(.term-editor)', function () {
        var calc = body.find('#calculator'),
            row = $(this).parents('.term-row');
        if(!row.is('.selected')) {
            calc.find('.term-row.selected').removeClass('selected');
            calc.find('.term-row .term-name').addClass('read-only');
            row.addClass('selected');
            row.find('.term-name').removeClass('read-only');
        }
    });

    body.on('click', '#calculator .class-row.selected > .read-only.hours', function () {
        $(this).removeClass('read-only');
        $(this).find('input').focus();
    });

    body.on('click', '#calculator .class-row > *:not(.grade-editor):not(.hours):not(.class-name),' +
                     '#calculator .class-row:not(.selected) > *.hours,' +
                     '#calculator .term-row:not(.schedule-id-) .class-row > *.class-name', function () {
        var row = $(this).parents('.class-row');
        if(row.is('.selected')) {
            row.removeClass('selected');
            if(row.find('> .hours input').val().trim() != '' &&
                parseInt(row.find('> .hours input').val().trim()) != 0 &&
                !isNaN(parseInt(row.find('> .hours input').val().trim())))
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
        score = Math.round(score);
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

    function validateGrade()
    {
        var that = $(this);
        // valid if assignment is filled in or all fields are blank is the same as remove
        if(that.find('.assignment input').val().trim() != '' || (
            that.find('.score input').val().trim() == '' &&
            that.find('.percent input').val().trim() == '' &&
            that.find('.assignment input').val().trim() == ''))
            that.removeClass('invalid').addClass('valid');
        else
            that.removeClass('valid').addClass('invalid');

    }

    function calculateClassGrade()
    {
        var row = $(this),
            percent = 0,
            sum = 0;
        row.find('.grade-row:not(.deleted)').each(function () {
            var that = $(this),
                score = parseInt(that.find('.score input').val().trim()),
                rowPercent = parseInt(that.find('.percent input').val().trim());

            validateGrade.apply(this);

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
            row.find('> .score').html(Math.round(classScore * 100) / 100);
            row.find('> .grade span').html(scaled[0]);
            row.find('> .gpa').html(scaled[1]);
            row.find('> .percent').html(percent + '%');
        }
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
                    courseHours = parseInt(row.find('.hours input').val().trim());

                calculateClassGrade.apply(this);

                var percent = row.find('> .percent').html(),
                    score = parseFloat(row.find('> .score').html()),
                    scaled = convertToScale(score);
                percent = parseInt(percent.substr(0, percent.length - 1));

                if(!isNaN(courseHours) && !isNaN(percent) && !isNaN(score)) {
                    hours += courseHours;
                    termGPA += parseFloat(scaled[1] * courseHours);
                    termPercent += percent * courseHours;
                }
            });

            if(hours == 0) {
                term.find('> .gpa span, > .percent, > .hours').html('&bullet;');
                if(term.index(calc.find('.term-row')) == 0)
                    calc.find('.projected').html('&bullet;');
            }
            else {
                if(term.index(calc.find('.term-row')) == 0)
                    calc.find('.projected').html(Math.round(termGPA / hours * 100) / 100);
                term.find('> .gpa span').html((Math.round(termGPA / hours * 100) / 100));
                if(term.find('> .gpa span').html().length == 3) {
                    term.find('> .gpa span').html(term.find('> .gpa span').html() + '0');
                }
                if(term.find('> .gpa span').html().length == 1 && !isNaN(parseInt(term.find('> .gpa span').html()))) {
                    term.find('> .gpa span').html(term.find('> .gpa span').html() + '.00');
                }
                term.find('> .percent').html(Math.round(termPercent / hours) + '%');
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
        // copy first schedule
        var calc = $('#calculator'),
            newTerm = calc.find('.term-row').first().clone().insertAfter(calc.find('.term-row').last()),
            classes = newTerm.find('.class-row').hide();
        newTerm.attr('class', newTerm.attr('class').replace(/schedule-id-([0-9]*)(\s|$)/ig, ' schedule-id- '));
        // add new classes to new term
        for(var i = 0; i < 6; i++) {
            addCourse.apply(newTerm.find('a[href="#add-class"]'));
        }
        calc.find('.term-row.selected').removeClass('selected');
        calc.find('.term-row .term-name').addClass('read-only');
        newTerm.find('> .gpa').html('<span>&bullet;</span>');
        newTerm.find('> .percent, > .hours').html('&bullet;');
        newTerm.find('.term-name').removeClass('read-only');
        newTerm.addClass('selected');
        // remove existing classes
        classes.remove();
        validateGrades();
    });

    function addCourse()
    {
        // copy first class row
        var editor = $(this).parents('.term-editor'),
            newClass = editor.find('.class-row').first().clone().insertBefore(editor.find('> .highlighted-link').last()),
            grades = newClass.find('.grade-row');
        newClass.removeClass('selected').show();
        newClass.attr('class', newClass.attr('class').replace(/course-id-([0-9]*)(\s|$)/ig, ' course-id- '));
        newClass.find('.hours').removeClass('read-only').find('input').val('');
        newClass.find('.class-name').removeClass('read-only');
        newClass.find('.class-name input').val('');
        newClass.find('.class-name span').attr('class', 'class' + (editor.find('.class-row.course-id-').length - 1));
        newClass.find('> .score, > .grade span, > .gpa, > .percent').html('&bullet;');
        // insert new grades
        for(var i = 0; i < 4; i++) {
            addGrade.apply(newClass.find('a[href="#add-grade"]'));
        }
        // remove existing grades
        grades.remove();
        validateGrades();
    }

    body.on('click', '#calculator a[href="#add-class"]', function (evt) {
        evt.preventDefault();
        addCourse.apply(this);
        $(this).parents('.term-row').find('.class-row').last().find('.class-name input').focus();
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

        /*
        body.one('show', '#schedule', function () {
            var schedule = $('#schedule');
            schedule.find('.term-row').hide();
            schedule.find('.schedule-id-' + scheduleId).show();
            updateTermControls();
        });
        */
    });

    function addGrade()
    {
        var editor = $(this).parents('.grade-editor'),
            row = editor.find('.grade-row').first()
                .clone().show()
                .removeClass('read-only deleted').addClass('edit')
                .insertAfter(editor.find('.grade-row').last());
        row.attr('class', row.attr('class').replace(/grade-id-([0-9]*)(\s|$)/ig, ' grade-id- '));
        row.find('.grade span, .gpa').html('&bullet;');
        row.find('input').val('').attr('placeholder', '');
        row.find('.assignment input').attr('placeholder', 'Assignment')
    }

    body.on('click', '#calculator a[href="#add-grade"]', function (evt) {
        evt.preventDefault();
        addGrade.apply(this);
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
            calc = $('#calculator'),
            oldTerms = calc.find('.term-row').detach();
        response.find('.term-row').insertBefore(calc.find('.form-actions').last());
        calc.find('.projected').html(response.find('.projected').html());
        calc.find('.cumulative').html(response.find('.cumulative').html());
        // make hours read only
        calc.find('.term-row').each(function (j) {
            var termRow = $(this);
            if(oldTerms.eq(j).is('.selected'))
                termRow.addClass('selected');
            else
                termRow.removeClass('selected');
            termRow.find('.class-row').each(function (k) {
                if(oldTerms.eq(j).find('.class-row').eq(k).is('.selected'))
                    $(this).addClass('selected');
                else
                    $(this).removeClass('selected');
                if($(this).find('> .hours input').val().trim() != '' &&
                    parseInt($(this).find('> .hours input').val().trim()) != 0 &&
                    !isNaN(parseInt($(this).find('> .hours input').val().trim())))
                    $(this).find('> .hours').addClass('read-only');
            });
        });
        oldTerms.remove();
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

        var data = {terms: [], scale: $('#grade-scale').find('tbody tr').map(function () { return [[
                $(this).find('td:nth-child(1) input').val(),
                $(this).find('td:nth-child(2) input').val(),
                $(this).find('td:nth-child(3) input').val(),
                $(this).find('td:nth-child(4) input').val()
            ]]; }).toArray()};
        calc.find('.term-row').each(function (k) {
            var term = $(this),
                courses = [];
            term.find('.class-row').each(function (i) {
                var row = $(this),
                    courseId = (/course-id-([0-9]*)(\s|$)/ig).exec(row.attr('class'))[1],
                    courseData = {
                        courseId: courseId,
                        className: row.find('.class-name input').val().trim(),
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
                courses[i] = courseData;
            });
            data.terms[k] = {
                courses: courses,
                term: term.find('.term-name select').val(),
                scheduleId: (/schedule-id-([0-9]*)(\s|$)/ig).exec(term.attr('class'))[1]
            }
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
        if(classRow.find('.grade-row:visible').length == 0) {
            for(var i = 0; i < 4; i++) {
                addGrade.apply(classRow.find('a[href="#add-grade"]'));
            }
        }
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
        var calc = $('#calculator'),
            dialog = $('#what-if'),
            firstRow = dialog.find('.class-row').first(),
            classes = calc.find('.term-row').first().find('.class-row'),
            firstGrade = classes.first().find('> .grade span').text();

        dialog.find('select.class-name').find('option').remove();
        dialog.find('.class-row').not(firstRow).remove();

        // update course list in dialog
        classes.each(function (i) {
            var row = $(this),
                courseId = (/course-id-([0-9]*)(\s|$)/ig).exec(row.attr('class'))[1];
            $('<option value="' + courseId + '">' + row.find('.class-name input').val() + '</option>')
                .appendTo(dialog.find('select.class-name'));
            firstRow.find('.class-name').html('<span class="' + row.find('.class-name span').attr('class') + '"></span>' + row.find('.class-name input').val());
            firstRow.find('.hours').text(row.find('.hours input').val() + ' hrs');
            firstRow.find('select').val(row.find('> .grade span').text());
            if(i < classes.length - 1)
                firstRow = firstRow.clone().insertAfter(dialog.find('.class-row').last());
        });

        // update grade scale in dialog to match selected grade scale
        dialog.find('#class-grade select:not(.class-name) option, #term-gpa select option').remove();
        $('#grade-scale').find('tbody tr').each(function () {
            if($(this).find('td:nth-child(1) input').val().trim() == '')
                return true;

            // class grade uses the score
            $('<option value="' + $(this).find('td:nth-child(3) input').val() + '">' +
            $(this).find('td:nth-child(1) input').val() +
            '</option>').appendTo(dialog.find('#class-grade select:not(.class-name)'));
            if(firstGrade == $(this).find('td:nth-child(1) input').val())
                dialog.find('#class-grade select:not(.class-name)').val($(this).find('td:nth-child(3) input').val());

            // term gpa uses the gpa result
            $('<option value="' + $(this).find('td:nth-child(4) input').val() + '">' +
            $(this).find('td:nth-child(1) input').val() +
            '</option>').appendTo(dialog.find('#term-gpa select'));
        });

        // set some defaults to their current scores
        dialog.find('#term-gpa .class-row').each(function (i) {
            $(this).find('select').val(classes.eq(i).find('> .gpa').text());
        });

        dialog.find('#class-grade select:not(.class-name)').val();
        dialog.find('.overall-gpa').val(Math.round(parseFloat(calc.find('.cumulative').text()) * 10) / 10);

        // process the current results
        dialog.find('#class-grade select:not(.class-name)').trigger('change');
        dialog.find('#term-gpa select').first().trigger('change');
        dialog.find('#overall-gpa select').first().trigger('change');
    });

    body.on('change', '#what-if #class-grade select', function () {
        /*
        Grade = (Assignment * Percent + Assignment * Remaining Percent) / Total Percent

        Backwards:
        (Grade * Total Percent - Assignment * Percent) / Remaining Percent = Assignment

         */
        var that = $(this),
            calc = $('#calculator'),
            grade = that.parents('#class-grade'),
            row = calc.find('.class-row.course-id-' + grade.find('.class-name').val()),
            result = grade.find('.result'),
            completed = row.find('> .percent').text(),
            wants = parseInt(grade.find('select:not(.class-name)').val());
        completed = parseInt(completed.substr(0, completed.length - 1));
        var score = (wants * 100 - parseInt(row.find('> .score').text()) * completed) / (100 - completed);
        if(100-completed == 0) {
            if(score > 0)
                score = 100;
            else if(score < 0)
                score = 0;
        }
        score = Math.round(score);
        result.text(score + '%');
    });

    body.on('change', '#what-if #term-gpa select', function () {
        var term = $(this).parents('#term-gpa'),
            gpa = 0,
            total = 0;
        term.find('.class-row').each(function () {
            var hours = $(this).find('.hours').text();
            hours = parseInt(hours.substr(0, hours.length - 4));
            gpa += $(this).find('select').val() * hours;
            total += hours;
        });
        term.find('.result').text(Math.round(gpa / total * 100) / 100);
    });

    body.on('change', '#overall-gpa select', function () {
        /*
         Overall GPA = (Term GPA * Hours + Past GPA * Past Hours) / Total Hours

         Backwards:
         (Overall GPA * Total Hours - Past GPA * Past Hours) / Hours = Term GPA

         */
        var calc = $('#calculator'),
            overall = $(this).parents('#overall-gpa'),
            result = overall.find('.result'),
            current = calc.find('.term-row').first(),
            currentHours = current.find('> .hours').text(),
            totalHours = 0,
            pastGPA = 0;
        currentHours = parseInt(currentHours.substr(0, currentHours.length - 4));
        calc.find('.term-row').not(current).each(function () {
            var hours = $(this).find('> .hours').html();
            hours = parseInt(hours.substr(0, hours.length - 4));
            if(!isNaN(hours)) {
                totalHours += hours;
                pastGPA = parseFloat($(this).find('> .gpa span').text()) * hours;
            }
        });
        totalHours += currentHours;
        var termGPA = (overall.find('select').val() * totalHours - pastGPA) / currentHours;
        result.text(Math.round(termGPA * 100) / 100);
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