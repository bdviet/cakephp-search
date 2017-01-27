(function ($) {
    'use strict';

    if (!window.chartsData) {
        return;
    }

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
    });

})(jQuery);
