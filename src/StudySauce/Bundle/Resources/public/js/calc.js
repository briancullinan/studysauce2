$(document).ready(function () {

    var body = $('body');

    body.on('click', '#calculator .term-row > *:not(.term-editor)', function () {
        var calc = body.find('#calculator'),
            row = $(this).parents('.term-row');
        if(!row.is('selected')) {
            calc.find('.selected').removeClass('selected');
            row.addClass('selected');
        }
    });

    body.on('click', '#calculator .class-row > *:not(.grade-editor):not(.hours)', function () {
        var row = $(this).parents('.class-row');
        row.toggleClass('selected');
    });

    function validateGrades()
    {
        var calc = body.find('#calculator');
        calc.find('.grade-row').each(function () {
            var that = $(this),
                score = parseInt(that.find('.score input').val().trim());
            if(isNaN(score)) {
                that.find('.grade span, .gpa').html('&bullet;');
            }
            else {
                if($('#grade-scale').find('.nav-tabs li.active').index() == 0)
                {
                    if(score >= 97) {
                        that.find('.grade span').html('A+');
                        that.find('.gpa').html('4.0');
                    }
                    else if(score >= 93) {
                        that.find('.grade span').html('A');
                        that.find('.gpa').html('4.0');
                    }
                    else if(score >= 90) {
                        that.find('.grade span').html('A-');
                        that.find('.gpa').html('3.7');
                    }
                    else if(score >= 87) {
                        that.find('.grade span').html('B+');
                        that.find('.gpa').html('3.3');
                    }
                    else if(score >= 83) {
                        that.find('.grade span').html('B');
                        that.find('.gpa').html('3.0');
                    }
                    else if(score >= 80) {
                        that.find('.grade span').html('B-');
                        that.find('.gpa').html('2.7');
                    }
                    else if(score >= 77) {
                        that.find('.grade span').html('C+');
                        that.find('.gpa').html('2.3');
                    }
                    else if(score >= 73) {
                        that.find('.grade span').html('C');
                        that.find('.gpa').html('2.0');
                    }
                    else if(score >= 70) {
                        that.find('.grade span').html('C-');
                        that.find('.gpa').html('1.7');
                    }
                    else if(score >= 67) {
                        that.find('.grade span').html('D+');
                        that.find('.gpa').html('1.3');
                    }
                    else if(score >= 63) {
                        that.find('.grade span').html('D');
                        that.find('.gpa').html('1.0');
                    }
                    else if(score >= 60) {
                        that.find('.grade span').html('D-');
                        that.find('.gpa').html('0.7');
                    }
                    else {
                        that.find('.grade span').html('F');
                        that.find('.gpa').html('0.0');
                    }
                }
                else
                {
                    if(score >= 90) {
                        that.find('.grade span').html('A');
                        that.find('.gpa').html('4.0');
                    }
                    else if(score >= 80) {
                        that.find('.grade span').html('B');
                        that.find('.gpa').html('3.0');
                    }
                    else if(score >= 70) {
                        that.find('.grade span').html('C');
                        that.find('.gpa').html('2.0');
                    }
                    else if(score >= 60) {
                        that.find('.grade span').html('D');
                        that.find('.gpa').html('1.0');
                    }
                    else {
                        that.find('.grade span').html('F');
                        that.find('.gpa').html('0.0');
                    }
                }
            }
        });
    }

    body.on('keyup', '#calculator .grade-row input, #calculator .class-row input', validateGrades);
    body.on('change', '#calculator .grade-row input, #calculator .class-row input', validateGrades);

});