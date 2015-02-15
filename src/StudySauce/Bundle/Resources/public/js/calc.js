$(document).ready(function () {

    var body = $('body');

    body.on('click', '#calculator .term-row > *:not(.term-editor)', function () {
        var calc = body.find('#calculator'),
            row = $(this).parents('.term-row');
        if(!row.is('.selected')) {
            calc.find('.selected').removeClass('selected');
            row.addClass('selected');
        }
    });

    body.on('click', '#calculator .class-row > *:not(.grade-editor):not(.hours), #calculator .class-row:not(.edit) > *.hours', function () {
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
                row.find('.grade-row').each(function () {
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

                        var scaled = window.convertToScale($('#grade-scale').find('.nav-tabs li.active').index() == 0, score);
                        that.find('.grade span').html(scaled[0]);
                        that.find('.gpa').html(scaled[1]);
                    }
                });
                if(percent == 0) {
                    row.find('> .score, > .grade span, > .gpa, > .percent').html('&bullet;');
                }
                else {
                    var classScore = sum / percent,
                        scaled = window.convertToScale($('#grade-scale').find('.nav-tabs li.active').index() == 0, classScore);
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
                term.find('> .gpa, > .percent, > .hours').html('&bullet;');
                calc.find('.projected').html('&bullet;');
            }
            else {
                if(term.index(calc.find('.term-row')) == 0)
                    calc.find('.projected').html(Math.round(termGPA / hours * 100) / 100);
                term.find('> .gpa').html((Math.round(termGPA / hours * 10) / 10) + ' (projected)');
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

    }

    body.on('click', '#calculator a[href="#add-grade"]', function (evt) {
        evt.preventDefault();
        var editor = $(this).parents('.grade-editor'),
            row = editor.find('.grade-row').first()
                .clone().show()
                .removeClass('read-only').addClass('edit')
                .insertAfter(editor.find('.grade-row').last());
        row.attr('class', row.attr('class').replace(/grade-id-([0-9]*)(\s|$)/ig, ' grade-id- '));
        row.find('.grade span, .gpa').html('&bullet;');
        row.find('input').val('').attr('placeholder', '');
        row.find('.assignment input').attr('placeholder', 'Assignment')
    });

    function submitCalc()
    {

    }


    body.on('submit', '#calculator form', function (evt) {
        evt.preventDefault();
        var calc = $('#calculator'),
            data = {courses: [], scale: $('#grade-scale').find('.nav-tabs li.active').index() == 0};
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
            success: function () {
                // TODO: update courses

            }
        });
    });

    body.on('click', '#calculator a[href="#edit-grade"]', function (evt) {
        evt.preventDefault();
        $(this).parents('.grade-row').removeClass('read-only').addClass('edit');
        validateGrades();
    });

    body.on('click', '#calculator a[href="#remove-grade"]', function (evt) {
        evt.preventDefault();
        var row = $(this).parents('.grade-row'),
            classRow = row.parents('.class-row');
        row.hide();
        row.find('.score input, .percent input, .assignment input').val('');
        // create 4 empty rows if this is the last one
        if(classRow.find('.grade-row:visible').length == 0)
            classRow.find('a[href="#add-grade"]').trigger('click').trigger('click').trigger('click').trigger('click');
        classRow.find('.form-actions button').css('visibility', 'visible');
        validateGrades();
    });

    body.on('keyup', '#calculator .grade-row input, #calculator .class-row input', validateGrades);
    body.on('change', '#calculator .grade-row input, #calculator .class-row input', validateGrades);

    //body.on('show', '#calculator', validateGrades)
});