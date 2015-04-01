$(document).ready(function () {

    var body = $('body');

    body.on('click', '#calculator .term-row > *:not(.term-editor)', function () {
        var calc = body.find('#calculator'),
            row = $(this).parents('.term-row');
        if(!row.is('.selected')) {
            calc.find('.term-row.selected').removeClass('selected');
            calc.find('.term-row .term-name').addClass('read-only');
            row.addClass('selected');
            if(row.index('.term-row') > 0)
                row.find('.term-name').removeClass('read-only');
        }
    });

    body.on('click', '#calculator .class-row > .hours', function () {
        $(this).removeClass('read-only');
        $(this).find('input').focus();
        $(this).parents('.class-row').next().find('[value="#save-grades"]').css('visibility', 'visible');
    });

    body.on('click', '#calculator .class-row > *:not(.grade-editor):not(.hours):not(.grade):not(.class-name),' +
                     '#calculator .class-row:not(.selected) > .hours.read-only,' +
                     '#calculator .class-row > .grade.read-only,' +
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
        var scaled = [null, null],
            rounded = Math.round(score);
        if(isNaN(rounded)) {
            // try to find letter match
            $('#grade-scale').find('tbody tr').each(function () {
                if(score == $(this).find('td:nth-child(1) input').val())
                {
                    scaled = [$(this).find('td:nth-child(1) input').val(), $(this).find('td:nth-child(4) input').val()];
                    return false;
                }
            });
        }
        else {
            $('#grade-scale').find('tbody tr').each(function () {
                if(rounded <= parseInt($(this).find('td:nth-child(2) input').val()) &&
                    rounded >= parseInt($(this).find('td:nth-child(3) input').val()))
                {
                    scaled = [$(this).find('td:nth-child(1) input').val(), $(this).find('td:nth-child(4) input').val()];
                    return false;
                }
            });
        }
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
        else {
            if(that.find('.assignment input').val().trim() == '') {

            }
            that.removeClass('valid').addClass('invalid');
        }

    }

    function addDecimalPoints(value)
    {
        var roundedScore = (Math.round(value * 100) / 100) + '';
        if(roundedScore.indexOf('.') == -1) {
            roundedScore += '.00';
        }
        if(roundedScore.indexOf('.') == roundedScore.length - 2) {
            roundedScore += '0';
        }
        return roundedScore;
    }

    function calculateClassGrade()
    {
        var row = $(this),
            percent = 0,
            sum = 0;
        row.next().find('.grade-row:not(.deleted)').each(function () {
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

        // if the grade is set by the assignments the percent would add up
        if (percent > 0) {
            var classScore = sum / percent,
                scaled = convertToScale(classScore);
            row.find('> .score').html(addDecimalPoints(classScore));
        }
        // if the grade it only set by the dropdown use that instead, assume 100%
        else if (row.find('> .grade select').val() != '') {
            scaled = convertToScale(row.find('> .grade select').val());
            percent = 100;
        }

        // set the other values for the class row based on the grade or score
        if(percent == 0) {
            row.find('> .score, > .gpa, > .percent').html('&bullet;');
            row.find('> .grade select').val('');
        }
        else {
            if(percent > 100) {
                row.addClass('over-percent-error');
            }
            else {
                row.removeClass('over-percent-error');
            }
            row.find('> .grade select').val(scaled[0]);
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
                    scaled = convertToScale(row.find('> .grade select').val());
                percent = parseInt(percent.substr(0, percent.length - 1));

                if(!isNaN(courseHours) && !isNaN(percent) && scaled[1] != null) {
                    hours += courseHours;
                    termGPA += parseFloat(scaled[1] * courseHours);
                    termPercent += percent * courseHours;
                }
            });

            if(hours == 0) {
                term.find('> .gpa, > .percent, > .hours').html('&bullet;');
                term.addClass('missing-hours');
                if(term.index(calc.find('.term-row')) == 0)
                    calc.find('.projected').html('&bullet;');
            }
            else {
                term.removeClass('missing-hours');
                if(term.index(calc.find('.term-row')) == 0)
                    calc.find('.projected').html(addDecimalPoints(termGPA / hours));
                term.find('> .gpa').html(addDecimalPoints(termGPA / hours));
                term.find('> .hours').html(hours);
            }

            if(term.find('.over-percent-error').length > 0) {
                term.addClass('over-percent-error');
            }
            else {
                term.removeClass('over-percent-error');
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
            calc.find('.cumulative').html(addDecimalPoints(overallGPA / overallHours));
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
        newTerm.find('> .gpa, > .hours').html('&bullet;');
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
            newClass = editor.find('.class-row').first()
                .add(editor.find('.class-row').first().next()).clone()
                .insertBefore(editor.find('> .highlighted-link').last())
                .filter('.class-row'),
            grades = newClass.next().find('.grade-row');
        newClass.removeClass('selected').show().attr('class', newClass.attr('class').replace(/course-id-([0-9]*)(\s|$)/ig, ' course-id- '));
        newClass.find('.hours, > .grade').removeClass('read-only').find('input').val('');
        newClass.find('.class-name').removeClass('read-only');
        newClass.find('.class-name input').val('');
        newClass.find('.class-name i').attr('class', 'class' + (editor.find('.class-row').length - 1));
        newClass.find('> .score, > .gpa, > .percent').html('&bullet;');
        newClass.find('> .grade select').val('');
        // insert new grades
        for(var i = 0; i < 4; i++) {
            addGrade.apply(newClass.next().find('a[href="#add-grade"]'));
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

    function addGrade()
    {
        var editor = $(this).parents('.grade-editor'),
            row = editor.find('.grade-row').first()
                .clone().show()
                .removeClass('read-only deleted').addClass('edit')
                .insertAfter(editor.find('.grade-row').last());
        row.attr('class', row.attr('class').replace(/grade-id-([0-9]*)(\s|$)/ig, ' grade-id- '));
        row.find('.grade span, .gpa').html('&bullet;');
        row.find('input').val('');
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
            if(oldTerms.eq(j).is('.selected')) {
                termRow.addClass('selected');
                if(j > 0)
                    termRow.find('.term-name').removeClass('read-only');
            }
            else {
                termRow.removeClass('selected');
                termRow.find('.term-name').addClass('read-only');
            }
            termRow.find('.class-row').each(function (k) {
                $(this).find('[value="#save-grades"]').css('visibility', '');
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
        if(calc.find('.term-row.selected').length == 0) {
            calc.find('.term-row').first().addClass('selected');
            calc.find('.term-row').first().find('.class-row').addClass('selected');
        }
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
        validateGrades();
    }

    function submitCalc(evt)
    {
        evt.preventDefault();
        var calc = $('#calculator');
        if(calc.find('.form-actions').is('.invalid')) {
            // TODO: open term with invalid entry
            calc.addClass('invalid-only');
            var rowEdit = calc.find('.grade-row.invalid').first(),
                termRow = rowEdit.parents('.term-row'),
                classRow = rowEdit.parents('.class-row');
            if(!termRow.is('.selected')) {
                calc.find('.term-row.selected').removeClass('selected');
                calc.find('.term-row .term-name').addClass('read-only');
                termRow.addClass('selected');
                if(termRow.index('.term-row') > 0)
                    termRow.find('.term-name').removeClass('read-only');
            }
            if(!classRow.is('.selected')) {
                classRow.addClass('selected');
                classRow.find('.hours').removeClass('read-only');
            }
            rowEdit.find('.assignment input').focus();
            return;
        }

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
                        grade: row.find('> .grade select').val().trim(),
                        className: row.find('.class-name input').val().trim(),
                        creditHours: parseInt(row.find('.hours input').val().trim()),
                        grades: {}
                    };
                row.next().find('.grade-row.edit.valid').each(function (j) {
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
            success: function (data) {
                calc.find('.squiggle').stop().remove();
                updateCalc(data);
            },
            error: function () {
                calc.find('.squiggle').stop().remove();
            }
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
            classRow = row.parents('.grade-editor');
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

    body.on('change', '#calculator .term-name select', function () {
        //resort term rows
        var calc = $('#calculator');
        calc.find('.term-row').sort(function (a, b) {
            var timeA = $(a).find('.term-name select').val().split('/'),
                timeB = $(b).find('.term-name select').val().split('/');
            if(parseInt(timeA[1]) * 12 + parseInt(timeA[0]) > parseInt(timeB[1]) * 12 + parseInt(timeB[0])) {
                return -1;
            }
            if(parseInt(timeA[1]) * 12 + parseInt(timeA[0]) < parseInt(timeB[1]) * 12 + parseInt(timeB[0])) {
                return 1;
            }
            return 0;
        }).detach().insertBefore(calc.find('form > .highlighted-link'));
    });

    body.on('click', '#grade-scale a[href="#scale-preset"]', function (evt) {
        evt.preventDefault();
        var preset = window.presetScale[$(this).html()],
            dialog = $('#grade-scale');
        dialog.find('li.active').removeClass('active');
        $(this).parent().addClass('active');
        dialog.find('tbody input').val('');
        for(var i = 0; i < preset.length; i++) {
            if(dialog.find('tbody tr').eq(i).length == 0) {
                dialog.find('tbody tr').clone().insertAfter(dialog.find('tbody tr').last());
            }
            dialog.find('tbody tr').eq(i).find('input').eq(0).val(preset[i][0]);
            dialog.find('tbody tr').eq(i).find('input').eq(1).val(preset[i][1]);
            dialog.find('tbody tr').eq(i).find('input').eq(2).val(preset[i][2]);
            dialog.find('tbody tr').eq(i).find('input').eq(3).val(addDecimalPoints(preset[i][3]));
        }
        dialog.find('tbody tr').eq(i-1).nextAll('tr').remove();
    });

    body.on('click', '#calculator a[href="#what-if"]', function () {
        var calc = $('#calculator'),
            dialog = $('#what-if'),
            firstRow = dialog.find('.class-row').first(),
            classes = calc.find('.term-row').first().find('.class-row'),
            firstGrade = classes.first().find('> .grade span').text();

        dialog.find('select.class-name option').remove();
        $('<option value="">-Select-</option>')
            .appendTo(dialog.find('select.class-name'));
        dialog.find('.class-row').not(firstRow).remove();

        // update course list in dialog
        classes.each(function (i) {
            var row = $(this),
                courseId = (/course-id-([0-9]*)(\s|$)/ig).exec(row.attr('class'))[1];
            $('<option value="' + courseId + '">' + row.find('.class-name input').val() + '</option>')
                .appendTo(dialog.find('select.class-name'));
            firstRow.find('.class-name').html('<span class="' + row.find('.class-name i').attr('class') + '"></span>' + row.find('.class-name input').val());
            firstRow.find('.hours').text(row.find('.hours input').val());
            if(i < classes.length - 1)
                firstRow = firstRow.clone().insertAfter(dialog.find('.class-row').last());
        });

        // update grade scale in dialog to match selected grade scale
        dialog.find('#class-grade select:not(.class-name) option, #term-gpa select option').remove();
        $('<option value="">-Select-</option>')
            .appendTo(dialog.find('#class-grade select:not(.class-name), #term-gpa select'));
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

        classes.each(function (j) {
            var val = $(this).find('.gpa').html();
            if(isNaN(parseFloat(val))) {
                val = '';
            }
            dialog.find('.class-row').eq(j).find('select').val(val);
        });

        // process the current results
        var totalHours = 0,
            pastGPA = 0;
        calc.find('.term-row').not(calc.find('.term-row').first()).each(function () {
            var hours = parseInt($(this).find('> .hours').html());
            if(!isNaN(hours)) {
                totalHours += hours;
                pastGPA += parseFloat($(this).find('> .gpa').text()) * hours;
            }
        });
        if(!isNaN(pastGPA / totalHours))
            dialog.find('#overall-gpa .current-grade').html(Math.round(pastGPA / totalHours * 100) / 100);
        else
            dialog.find('#overall-gpa .current-grade').html('&nbsp;');
        dialog.find('#class-grade select:not(.class-name)').val('');
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
            classId = grade.find('.class-name').val(),
            classVal = grade.find('select:not(.class-name)').val(),
            row = calc.find('.class-row.course-id-' + classId),
            currentGrade = row.find('> .grade select').val(),
            currentScore = parseFloat(row.find('> .score').text()),
            completed = row.find('> .percent').text(),
            result = grade.find('.result'),
            wants = parseInt(classVal);
        completed = parseInt(completed.substr(0, completed.length - 1));

        // these are all the error conditions
        if(classId != '' && !isNaN(currentScore)) {
            grade.find('.current-grade').html(currentGrade);
        }
        else {
            grade.find('.current-grade').html('&nbsp;');
        }

        if(classVal == '') {
            result.html('&nbsp;');
        }
        if(100-completed == 0) {
            $('#what-if').addClass('class-completed');
            result.html('&nbsp;');
        }
        else {
            $('#what-if').removeClass('class-completed');
        }
        if(classId == '' || classVal == '' || isNaN(currentScore)) {
            return;
        }

        var score = Math.round((wants * 100 - currentScore * completed) / (100 - completed));

        // finally show result
        if(isNaN(score)) {
            $('#what-if').addClass('grade-incomplete');
            result.text('UNK');
        }
        else {
            $('#what-if').removeClass('grade-incomplete');
            if(score > 100) {
                result.text('> 100%');
            }
            else if(score < 0) {
                result.text('< 0%');
            }
            else {
                result.text(score + '%');
            }
        }
    });

    body.on('change', '#what-if #term-gpa select', function () {
        var term = $(this).parents('#term-gpa'),
            result = term.find('.result'),
            gpa = 0,
            total = 0;
        term.find('.class-row').each(function () {
            var hours = parseInt($(this).find('.hours').text());
            gpa += $(this).find('select').val() * hours;
            total += hours;
        });
        var termGPA = Math.round(gpa / total * 100) / 100;
        result.text(termGPA);
        if(isNaN(termGPA)) {
            $('#what-if').addClass('term-incomplete');
            result.text('UNK');
        }
        else {
            $('#what-if').removeClass('term-incomplete');
            result.text(addDecimalPoints(termGPA));
        }
    });

    body.on('change', '#what-if #overall-gpa select', function () {
        /*
         Overall GPA = (Term GPA * Hours + Past GPA * Past Hours) / Total Hours

         Backwards:
         (Overall GPA * Total Hours - Past GPA * Past Hours) / Hours = Term GPA

         */
        var calc = $('#calculator'),
            overall = $(this).parents('#overall-gpa'),
            result = overall.find('.result'),
            current = calc.find('.term-row').first(),
            currentHours = parseInt(current.find('> .hours').text()),
            totalHours = 0,
            pastGPA = 0,
            overallVal = overall.find('select').val();
        if(overallVal == '') {
            result.html('&nbsp;');
            return;
        }
        calc.find('.term-row').not(current).each(function () {
            var hours = parseInt($(this).find('> .hours').html());
            if(!isNaN(hours)) {
                totalHours += hours;
                pastGPA += parseFloat($(this).find('> .gpa').text()) * hours;
            }
        });
        totalHours += currentHours;
        var termGPA = (overallVal * totalHours - pastGPA) / currentHours;
        if(isNaN(termGPA)) {
            $('#what-if').addClass('overall-incomplete');
            result.text('UNK');
        }
        else {
            $('#what-if').removeClass('overall-incomplete');
            if(termGPA > 4) {
                result.text('> 4.00');
            }
            else if(termGPA < 0) {
                result.text('< 0.00');
            }
            else {
                result.text(addDecimalPoints(termGPA));
            }
        }
    });

    body.on('click', '#grade-scale a[href="#save-scale"]', function (evt) {
        evt.preventDefault();
        $.ajax({
            url: window.callbackPaths['calculator_update'],
            type: 'POST',
            dataType: 'text',
            data: {
                scale: $('#grade-scale').find('tbody tr').map(function () { return [[
                    $(this).find('td:nth-child(1) input').val(),
                    $(this).find('td:nth-child(2) input').val(),
                    $(this).find('td:nth-child(3) input').val(),
                    $(this).find('td:nth-child(4) input').val()
                ]]; }).toArray()
            },
            success: function (data) {
                $('#grade-scale').modal('hide');
                updateCalc(data);
            },
            error: function () {
            }
        });
    });
    body.on('keyup', '#calculator .grade-row input, #calculator .class-row input', validateGrades);
    body.on('change', '#calculator .grade-row input, #calculator .class-row input, #calculator .class-row select', validateGrades);

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