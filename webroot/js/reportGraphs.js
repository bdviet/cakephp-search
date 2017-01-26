(function ($) {
    'use strict';

    if (!window.chartsData) {
        return;
    }

    chartsData.forEach(function (graph) {
        if (graph.chart === 'barChart') {
            Morris.Bar(graph.options);
        }
    });

})(jQuery);
