<?php
namespace Search\WidgetHandlers\Reports;

use Cake\Utility\Inflector;
use Search\WidgetHandlers\BaseGraphWidgetHandler;

class LineChartReportWidgetHandler extends BaseGraphWidgetHandler
{
    protected $_type = 'lineChart';

    /**
     * prepareChartData method
     *
     * Assembles the chart data for the LineChart widget
     *
     * @param array $data with report config and data.
     * @return array $chartData.
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
     * Specifies required JS/CSS libs for given chart
     *
     * @param array $data passed in the method.
     * @return array $content with JS/CSS libs.
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
