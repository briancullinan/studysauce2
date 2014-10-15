Date.prototype.getWeekNumber = function () {
    var d = new Date(+this);
    d.setHours(0, 0, 0, 0);
    d.setDate(d.getDate() + 4 - (d.getDay() || 7));
    return Math.ceil((((d - new Date(d.getFullYear(), 0, 1)) / 8.64e7) + 1) / 7);
};

Date.prototype.getFirstDayOfWeek = function () {
    var d = new Date(+this);
    d.setHours(0, 0, 0, 0);
    var day = d.getDay(),
        diff = d.getDate() - day + (day == 0 ? 0:0); // adjust when day is sunday
    return new Date(d.setDate(diff));
};

jQuery(document).ready(function () {

    var timeline = jQuery('#timeline'),
        piechart = jQuery('#pie-chart');

    var m = [30, 0, 50, 0],
        w = 475 - m[1] - m[3],
        w2 = 300 - m[1] - m[3],
        h = 300 - m[0] - m[2],
        h2 = 300;

    var color = d3.scale.category10().range(["#FF1100", "#FF9900", "#FFDD00", "#BBEE00", "#33DD00",
        "#009999", "#1133AA", "#6611AA", "#BB0088"]);
    var x,
        y,
        xAxis,
        xAxisLine2,
        xAxisTotals;
    var week = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    var svg = d3.select("#timeline").append("svg")
        .attr("width", w + m[1] + m[3])
        .attr("height", h + m[0] + m[2])
        .append("g")
        .attr("transform", "translate(" + m[1] + "," + m[0] + ")");

    var svg2 = d3.select("#pie-chart").append("svg")
        .attr("width", w2 + m[1] + m[3])
        .attr("height", h2)
        .append("g");

    var history,
        classes,
        resizeTimeout = null;

    jQuery(window).resize(function () {
        if(resizeTimeout != null)
            clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function () {
            if (timeline.width() != timeline.find('svg').attr('width'))
            {
                w = timeline.width() - m[1] - m[3];
                w2 = piechart.width() - m[1] - m[3];
                h = (timeline.width() * 12 / 16) - m[0] - m[2];
                h2 = (piechart.width() * 12 / 16);
                d3.select('#timeline svg')
                    .attr("width", w + m[1] + m[3])
                    .attr("height", h + m[0] + m[2]);
                d3.select('#pie-chart svg')
                    .attr("width", w2 + m[1] + m[3])
                    .attr("height", h2);
                setTimeout(redraw, 100);
            }
        }, 100);
    });
    if(timeline.is(':visible'))
        jQuery(window).trigger('resize');

    jQuery.fn.updateMetrics = function (newHistory) {
        updateHistory(newHistory);
        if (timeline.width() != timeline.find('svg').width())
            jQuery(window).trigger('resize');
        else
            setTimeout(redraw, 100);
    };

    jQuery('#metrics').on('show', function () {
        jQuery(window).trigger('resize');
    });

    if (typeof window.initialHistory != 'undefined') {
        updateHistory(window.initialHistory);
        if (timeline.width() != timeline.find('svg').width())
            jQuery(window).trigger('resize');
    }

    function updateHistory(newHistory) {
        history = newHistory;

        classes = d3.nest()
            .key(function (d) { return d['class']; })
            .entries(history);

        classes.forEach(function (s, j) {
            s.bases = {};
            s.values.forEach(function (d) {
                d.length = +d.length;
                d.time = new Date(+d.time * 1000).getFirstDayOfWeek();
                d.length0 = +d.length0;
                // add the groups length that came before this one
                var g = d.time.getWeekNumber();
                var prev = 0;
                classes.forEach(function (c, i) {
                    if (i < j && typeof c.bases[g] != 'undefined')
                        prev += c.bases[g];
                });
                d.length0 += prev;
                if (typeof s.bases[g] == 'undefined')
                    s.bases[g] = d.length;
                else
                    s.bases[g] += d.length;
            });
            s.maxLength = d3.max(s.values, function (d) { return d.length; });
            s.sumLength = d3.sum(s.values, function (d) { return d.length; });
            s.minTime = d3.min(s.values, function (d) { return d.time; });
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

        color = color.domain(window.classNames);
    }

    function redraw() {
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
            .range([0, w - m[1] - m[3]]);

        y = d3.scale.linear()
            .domain([0, d3.max(classes.map(function (c) {
                return d3.max(c.values.map(function (d) {
                    return d.length + d.length0;
                }));
            }))])
            .range([h, 0]);

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
            .attr("transform", "translate(0," + (h) + ")")
            .call(xAxis);
        if (svg.select('.x.axis2').empty())
            svg.append("g").attr("class", "x axis2");
        svg.select('.x.axis2')
            .attr("transform", "translate(0," + (h + 20) + ")")
            .call(xAxisLine2)
            .select('path').remove();
        if (svg.select('.x.axisT').empty())
            svg.append("g").attr("class", "x axisT");
        svg.select('.x.axisT')
            .attr("transform", "translate(0," + (-30) + ")")
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
                    return y(d.length0 + d.length);
                })
                .attr("width", 30)
                .attr("height", function (d) { return h - y(d.length); })
                .style("fill", color(p.key))
                .style("fill-opacity", 1);

        });

        jQuery("svg .symbol rect")
            .tipsy({
                gravity: jQuery.fn.tipsy.autoBounds(100, 'w'),
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

    function redrawPie() {
        var g = svg2.selectAll(".symbol");

        var pie = d3.layout.pie()
            .value(function (d) { return d.sumLength; });

        var arc = d3.svg.arc();

        g.each(function () {
            var e = d3.select(this);

            /*
             if(e.select('text').empty())
             e.append("text");
             e.select('text')
             .attr('text-anchor', 'middle')
             .attr("x", 0)
             .attr("dy", ".31em")
             .text(p.key);
             */

            if (e.select('path').empty())
                e.append("path");
            e.select('path')
                .style("fill", function (d) { return color(d.key); })
        });

        g.select('path')
            .style("fill", function (d) { return color(d.key); })
            .data(function () { return pie(classes); })
//                .transition()
//                .duration(100)
            .each(arcTween);
//                .tween("arc", arcTween);


        function arcTween(d) {
            var path = d3.select(this),
            //    text = d3.select(this.parentNode).select('text'),
            //    x0 = x(d.data.key),
                extraHeight = Math.min(jQuery(window).width() / 600, 1) * 50,
                y0 = Math.min(h2, w2);
            //this.parentNode.appendChild(text[0][0]);

            var r = y0 / 2,
                a = Math.cos(Math.PI / 2),
                xx = ((a) * w2 + (1 - a) * w2 / 2),
                yy = ((a) * h2 + (1 - a) * h2 / 2),
                f = {
                    innerRadius: r / 2, //Math.min(jQuery(window).width() / 480, 1) * 50, //r - 60 / (2 - a),
                    outerRadius: r,
                    startAngle: a * (Math.PI / 2 - y0 / r) + (1 - a) * d.startAngle,
                    endAngle: a * (Math.PI / 2) + (1 - a) * d.endAngle
                };

            path.attr("transform", "translate(" + xx + "," + yy + ")");
            path.attr("d", arc(f));
            //text.attr("transform", "translate(" + arc.centroid(f) + ")translate(" + xx + "," + yy + ")");
            //text.attr("transform", "translate(" + arc.centroid(f) + ")translate(" + xx + "," + yy + ")rotate(" + ((f.startAngle + f.endAngle) / 2 + 3 * Math.PI / 2) * 180 / Math.PI + ")rotate(" + (100) + ")");
            //text.attr("transform", "translate(" + xx + "," + yy + ")rotate(" + ((f.startAngle + f.endAngle) / 2 + 2 * Math.PI / 2) * 180 / Math.PI + ")");
        }

        jQuery("svg .symbol path")
            .tipsy({
                gravity: jQuery.fn.tipsy.autoBounds(100, 'w'),
                html: true,
                title: function () {
                    var total = d3.sum(classes, function (c) { return c.sumLength; }),
                        d = this.__data__.data; //, c = color(d.key);
                    return d.key + '<br />' + (Math.round(d.sumLength * 10 / 3600) / 10) + ' hrs' + '<br />' + (Math.round(d.sumLength / total * 1000) / 10) + '%';
                }
            })

    }
});

