<?php
namespace Search\WidgetHandlers\Reports;

use Cake\Utility\Inflector;
use Search\WidgetHandlers\ReportWidgetHandler;

class BarChartReportWidgetHandler extends ReportWidgetHandler
{
    protected $_type = 'barChart';

    /**
     * prepareChartData method
     *
     * Assembles graphs data from the reports config and data.
     *
     * @param array $data containing report configs and data.
     * @return array $chartData with defined chart information.
     */
    public function prepareChartData($data = [])
    {
        $labels = [];
        $report = $this->_report;

        $chartData = [
            'chart' => $this->_type,
            'options' => [
                'element' => self::GRAPH_PREFIX . $report['slug'],
                'resize' => true,
            ],
        ];

        $columns = explode(',', $report['info']['columns']);

        foreach ($columns as $column) {
            array_push($labels, Inflector::humanize($column));
        }

        $options = [
            'data' => $data,
            'barColors' => ['#00a65a', '#f56954'],
            'labels' => $labels,
            'xkey' => explode(',', $report['info']['x_axis']),
            'ykeys' => explode(',', $report['info']['y_axis'])
        ];

        $chartData['options'] = array_merge($chartData['options'], $options);

        return $chartData;
    }

    /**
     * prepareChartOptions method
     *
     * Specifies JS/CSS libs for the content loading
     *
     * @param array $data passed from the widgetHandler.
     * @return array $content with the libs.
     */
    public function prepareChartOptions($data = [])
    {
        $content = [];

        $content = [
            'post' => [
                'css' => [
                    'type' => 'css',
                    'content' => [
                        'AdminLTE./plugins/morris/morris',
                    ],
                    'block' => 'css',
                ],
                'javascript' => [
                    'type' => 'script',
                    'content' => [
                        'https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js',
                        'AdminLTE./plugins/morris/morris.min',
                        'Search.reportGraphs',
                    ],
                    'block' => 'scriptBotton',
                ],
            ]
        ];

        return $content;
    }
}
