$(document).ready(function () {

    var schedule = $('#schedule');

    schedule.on('click', '.class-row a[href="#edit-class"]', function (evt) {
        evt.preventDefault();
        var that = $(this),
            row = that.parents('.class-row');
        row.removeClass('read-only').addClass('edit');
    });

    /*schedule.find('.school-name input').autocomplete({
        appendTo: schedule.find('.school-name'),
        source: function( request, response ) {
            $.ajax({
                url: window.callbackPaths['institutions'],
                dataType: 'json',
                data: {
                    q: request.term
                },
                success: function( data ) {
                    response( data );
                }
            });
        },
        minLength: 1,
        select: function( event, ui ) {
            schedule.find('.school-name input').val(ui.item);
        },
        open: function() {
            $( this ).removeClass( 'ui-corner-all' ).addClass( 'ui-corner-top' );
        },
        close: function() {
            $( this ).removeClass( 'ui-corner-top' ).addClass( 'ui-corner-all' );
        },
        _renderItem: function (ul, item) {
            return $("<li></li>")
                .data("item.autocomplete", item)
                .append("<a>" + item.label + "</a>")
                .appendTo(ul);
        }
    });*/
    schedule.find('.school-name input').selectize({
        valueField: 'institution',
        labelField: 'institution',
        searchField: ['institution', 'link', 'state'],
        maxItems: 1,
        create: false,
        render: {
            option: function(item, escape) {
                return '<div>' +
                '<span class="title">' +
                '<span class="name"><i class="icon source"></i>' + item.institution + '</span>' +
                '<span class="by">' + item.state + '</span>' +
                '</span>' +
                '<span class="description">' + item.link + '</span>' +
                '</div>';
            }
        },
        /*score: function(search) {
            var score = this.getScoreFunction(search);
            return function(item) {
                return score(item) * (1 + Math.min(item.watchers / 100, 1));
            };
        },*/
        load: function(query, callback) {
            if (query.length < 2) return callback();
            $.ajax({
                url: window.callbackPaths['institutions'],
                dataType:'json',
                data: {
                    q: query
                },
                error: function() {
                    callback();
                },
                success: function(res) {
                    callback(res.slice(0, 100));
                }
            });
        }
    });
});