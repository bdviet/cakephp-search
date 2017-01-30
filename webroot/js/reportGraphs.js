(function ($) {
    'use strict';

    if (!window.chartsData) {
        return;
    }

    var colorGradients = [
        //blue
        '#05497d',
        '#043b64',
        '#032c4c',
        //green
        '#1e3302',
        '#2c4c03',
        '#3b6404',
        '#497d05',
        '#579606',
        '#66ae07',
        '#74c708',
        //yellow
        '#a7a60e',
        '#bfbd0f',
        '#d6d411',
        //red
        '#a70e0f',
        '#bf0f11',
        '#d61113',
        '#ec1517',
    ];

    chartsData.forEach(function (graph) {
        if (graph.chart === 'barChart') {
            Morris.Bar(graph.options);
        }

        if (graph.chart === 'lineChart') {
            Morris.Line(graph.options);
        }

        if (graph.chart === 'donutChart') {
            Morris.Donut(graph.options);
        }

        if (graph.chart === 'knobChart') {
            var knobs = [];
            if (graph.options.data.length) {
                $('.knob-graph').knob({
                    'width': 85,
                    'height': 85,
                    'linecap': 'round',
                    'thickness': '.2',
                    'readOnly': true,
                    'cursor': false,
                    'draw': function () {
                        var randomColor = Math.floor(Math.random() * colorGradients.length);
                        this.o.fgColor = colorGradients[randomColor];
                    }
                });
            }
        }
    });

})(jQuery);
