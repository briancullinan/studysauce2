
$(document).ready(function () {
    var body = $('body');

    var margins = [25, 0, 45, 0];

    var color = d3.scale.category10().range(["#FF1100", "#FF9900", "#FFDD00", "#BBEE00", "#33DD00",
        "#009999", "#1133AA", "#6611AA", "#BB0088"]);
    var x,
        y,
        xAxis,
        xAxisLine2,
        xAxisTotals,
        classes,
        resizeTimeout = null,
        arc = d3.svg.arc();

    function resizeComponents()
    {
        //body.find('#metrics h3, #metrics h4').textfill({
        //    widthOnly: true,
        //    maxFontPixels: 24
        //});

        if(resizeTimeout != null)
            clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function () {
            var timeline = $('#timeline:visible, .timeline:visible'),
                piechart = $('#pie-chart:visible, .pie-chart:visible');
            if (timeline.width() != timeline.find('svg').attr('width'))
            {
                d3.selectAll(timeline.find('svg').toArray())
                    .attr("width", timeline.width())
                    .attr("height", timeline.width() * 12 / 16);
                d3.selectAll(piechart.find('svg').toArray())
                    .attr("width", piechart.width())
                    .attr("height", piechart.width() * 12 / 16);
                redraw();
            }
        }, 100);
    }

    body.on('show', '#metrics,#home', function () {
        var metrics = $('#metrics');

        // show empty dialog
        if(metrics.is('.demo'))
            $('#metrics-empty').modal({
                backdrop: 'static',
                keyboard: false,
                modalOverflow: true
            });

        if(($(this).is('#metrics') && !$(this).is('.loaded')) ||
            ($(this).is('#home') && !$(this).find('.metrics-widget').is('.loaded')))
        {
            initializeGraphs.apply(this);
            $('#metrics:visible,#home .metrics-widget:visible').addClass('loaded');
        }
        resizeComponents();
    });
    body.find('#metrics:visible,#home:visible').trigger('show');
    $(window).resize(resizeComponents);

    function updateHistory(newHistory) {
        var svg = d3.selectAll($('#timeline:visible, .timeline:visible').find('svg > g.bars').toArray());
        var svg2 = d3.selectAll($('#pie-chart:visible, .pie-chart:visible').find('svg > g.slices').toArray());

        classes = d3.nest()
            .key(function (d) { return d['courseId']; })
            .entries(newHistory);

        classes.forEach(function (s, j) {
            s.bases = {};
            s.values.forEach(function (d) {
                d.length = +d.length;
                if(typeof d.time == 'number')
                    d.time = new Date(+d.time * 1000).getFirstDayOfWeek();
                d.length0 = +d.length0;
                // add the groups length that came before this one

                var g = d.time.getWeekNumber(),
                    prev = 0;
                classes.forEach(function (c, i) {
                    if (i < j && typeof c.bases[g] != 'undefined')
                        prev += c.bases[g];
                });
                d.lengthS = d.length0 + prev;
                if (typeof s.bases[g] == 'undefined')
                    s.bases[g] = d.length;
                else
                    s.bases[g] += d.length;
            });
            s.maxLength = d3.max(s.values, function (d) { return d.length; });
            s.sumLength = d3.sum(s.values, function (d) { return d.length; });
            s.minTime = d3.min(s.values, function (d) { return d.time; });
            if(typeof s.values[0] != 'undefined')
                s.class = s.values[0]['class'];
        });

        var g = svg.selectAll("g.symbol")
            .data(classes)
            .attr("class", "symbol");
        g.enter().append("g")
            .attr("class", "symbol");
        g.exit().remove();

        var g2 = svg2.selectAll("g.symbol")
            .data(classes)
            .attr("class", "symbol");
        g2.enter().append("g")
            .attr("class", "symbol");
        g2.exit().remove();

        color = color.domain(window.classIds);

        redraw();
    }

    function initializeGraphs()
    {
        var metrics = $('#metrics'),
            timeline = $(this).find('#timeline:visible, .timeline:visible'),
            piechart = $(this).find('#pie-chart:visible, .pie-chart:visible');

        if(timeline.find('svg').length == 0)
            d3.selectAll(timeline.toArray()).append("svg")
                .attr("width", timeline.width())
                .attr("height", timeline.width() * 12 / 16)
                .append("g").attr('class', 'bars')
                .attr("transform", "translate(" + margins[1] + "," + margins[0] + ")");

        if(piechart.find('svg').length == 0)
            d3.selectAll(piechart.toArray()).append("svg")
                .attr("width", piechart.width())
                .attr("height", piechart.width() * 12 / 16)
                .append("g").attr('class', 'slices');

        if (typeof window.initialHistory != 'undefined') {
            updateHistory(window.initialHistory);
            if (timeline.width() != timeline.find('svg').width())
                $(window).trigger('resize');
        }
    }

    function updateMetrics()
    {
        setTimeout(function () {
            $.ajax({
                url: window.callbackPaths['metrics'],
                type: 'GET',
                dataType: 'text',
                success: function (data) {
                    var content = $(data),
                        metrics = $('#metrics');
                    ssMergeScripts(content.filter('script:not([src])'));
                    if(content.find('#metrics').is('.demo'))
                        metrics.addClass('demo');
                    else
                        metrics.removeClass('demo');
                    // update metrics
                    metrics.find('.checkin-row').remove();
                    jQuery('#checkins-list').append(content.find('.checkin-row'));

                    // update metrics key
                    metrics.find('ol li').remove();
                    metrics.find('ol').append(content.find('ol li'));

                    if(window.initialHistory.length > 0)
                        $('.metrics-widget').removeClass('empty');
                    else
                        $('.metrics-widget').addClass('empty');

                    updateHistory(window.initialHistory);
                }
            });
        }, 100);
    }

    // update metrics if checkin or checkout occurs
    body.on('scheduled', updateMetrics);
    body.on('checkin', updateMetrics);

    function arcTween(d) {
        var piechart = $('#pie-chart:visible, .pie-chart:visible'),
            h2 = piechart.width() * 12 / 16,
            w2 = piechart.width(),
            path = d3.select(this),
            y0 = h2;

        var r = y0 / 2,
            a = Math.cos(Math.PI / 2),
            xx = ((a) * w2 + (1 - a) * w2 / 2),
            yy = ((a) * h2 + (1 - a) * h2 / 2),
            f = {
                innerRadius: r / 2, //Math.min($(window).width() / 480, 1) * 50, //r - 60 / (2 - a),
                outerRadius: r,
                startAngle: a * (Math.PI / 2 - y0 / r) + (1 - a) * d.startAngle,
                endAngle: a * (Math.PI / 2) + (1 - a) * d.endAngle
            };

        path.attr("transform", "translate(" + xx + "," + yy + ")");
        path.attr("d", arc(f));
    }

    function redrawPie() {
        var piechart = $('#pie-chart:visible, .pie-chart:visible').find('svg'),
            svg2 = d3.selectAll(piechart.toArray()),
            g = svg2.selectAll(".symbol");

        var pie = d3.layout.pie()
            .value(function (d) { return d.sumLength; });

        g.each(function () {
            var e = d3.select(this);

            if (e.select('path').empty())
                e.append("path");
            e.select('path')
                .style("fill", function (d) { return color(d.key); })
        });

        g.select('path')
            .style("fill", function (d) { return color(d.key); })
            .data(function () { return pie(classes); })
            .each(arcTween);

        piechart.find('.symbol path').tipsy({
                gravity: $.fn.tipsy.autoBounds(100, 'w'),
                html: true,
                title: function () {
                    var total = d3.sum(classes, function (c) { return c.sumLength; }),
                        d = this.__data__.data; //, c = color(d.key);
                    return d['class'] + '<br />' + (Math.round(d.sumLength * 10 / 3600) / 10) + ' hrs' + '<br />' + (Math.round(d.sumLength / total * 1000) / 10) + '%';
                }
            })

    }

    function redraw() {
        var timeline = $('#timeline:visible, .timeline:visible').find('svg'),
            h = (timeline.width() * 12 / 16) - margins[0] - margins[2],
            w = timeline.width() - margins[1] - margins[3],
            svg = d3.selectAll(timeline.toArray());

        if (classes.length == 0)
            return;

        var now = new Date().getFirstDayOfWeek();
        var weeks = now.getWeekNumber() - d3.min(classes, function (c) { return c.minTime; }).getWeekNumber();
        if (weeks < 5)
            weeks = 5;
        if (weeks > 5)
            weeks = 5;

        var endTime = new Date(Math.floor(now.getTime())).getWeekNumber()+0.5,
            startTime = endTime - weeks;

        x = d3.scale.linear()
            .domain([startTime, endTime])
            .range([0, w])
            .nice();

        y = d3.scale.linear()
            .domain([0, d3.max(classes.map(function (c) {
                return d3.max(c.values.map(function (d) {
                    return d.length + d.lengthS;
                }));
            }))])
            .range([h, 0])
            .nice();

        xAxis = d3.svg.axis()
            .orient("bottom")
            .scale(x)
            .ticks(5)
            .tickFormat(function (w) {
                var firstOfTheYear = new Date('1/1/' + new Date().getFullYear()).getTime(),
                    d = new Date(firstOfTheYear + w * 604800000).getFirstDayOfWeek();
                return (d.getMonth() + 1) + '/' + d.getDate() + ' - ';
            });
        xAxisLine2 = d3.svg.axis()
            .orient("bottom")
            .scale(x)
            .ticks(5)
            .tickFormat(function (w) {
                var firstOfTheYear = new Date('1/1/' + new Date().getFullYear()).getTime(),
                    d = new Date(firstOfTheYear + w * 604800000).getFirstDayOfWeek();
                var d2 = new Date(d.getTime() + 604800000 - 1);
                return (d2.getMonth() + 1) + '/' + d2.getDate();
            });
        xAxisTotals = d3.svg.axis()
            .orient("bottom")
            .scale(x)
            .ticks(5)
            .tickFormat(function (w) {
                var result = 0;
                classes.forEach(function (c) {
                    if (typeof c.bases[w] != 'undefined')
                        result += c.bases[w];
                });
                var rounded = Math.round(result * 10 / 3600) / 10;
                return result == 0 ? '' : rounded;
            });


        // x-axis
        if (svg.select('.x.axis').empty())
            svg.append("g").attr("class", "x axis");
        svg.select('.x.axis')
            .attr("transform", "translate(0," + (h + margins[0]) + ")")
            .call(xAxis);
        if (svg.select('.x.axis2').empty())
            svg.append("g").attr("class", "x axis2");
        svg.select('.x.axis2')
            .attr("transform", "translate(0," + (h + margins[0] + 20) + ")")
            .call(xAxisLine2)
            .select('path').remove();
        if (svg.select('.x.axisT').empty())
            svg.append("g").attr("class", "x axisT");
        svg.select('.x.axisT')
            .attr("transform", "translate(" + margins[1] + ",0)")
            .call(xAxisTotals)
            .select('path').remove();

        var g = svg.selectAll(".symbol");

        g.each(function (p) {
            var e = d3.select(this);


            // TODO: replace with enter and exit
            e.selectAll('rect').remove();
            e.selectAll("rect")
                .data(function (d) { return d.values; })
                .enter().append("rect")
                .attr("x", function (d) {
                    return x(d.time.getWeekNumber());
                })
                .attr("y", function (d) {
                    return y(d.lengthS + d.length);
                })
                .attr("width", 30)
                .attr("height", function (d) { return h - y(d.length); })
                .style("fill", color(p.key))
                .style("fill-opacity", 1);

        });

        timeline.find('.symbol rect').tipsy({
                gravity: $.fn.tipsy.autoBounds(100, 'w'),
                html: true,
                title: function () {
                    var d = this.__data__; //, c = color(d['class']);
                    var g = d.time.getWeekNumber();
                    return d['class'] + '<br />' + (Math.round(this.parentNode.__data__.bases[g] * 10 / 3600) / 10) + ' hrs';
                }
            });

        svg.select('.x.axisT')
            .selectAll('text').each(function (w) {
                var weekTotal = 0;
                classes.forEach(function (c) {
                    if(typeof c.bases[w] != 'undefined')
                        weekTotal += c.bases[w];
                });
                d3.select(this).attr('transform', 'translate(15,' + y(weekTotal) + ')');
            });

        redrawPie();
    }

});

