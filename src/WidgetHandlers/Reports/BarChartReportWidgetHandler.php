<?php
namespace Search\WidgetHandlers\Reports;

use Cake\Utility\Inflector;
use Search\WidgetHandlers\Reports\BaseReportGraphs;

class BarChartReportWidgetHandler extends BaseReportGraphs
{
    public $_type = 'barChart';

    /**
     * getChartData method
     *
     * Assembles graphs data from the reports config and data.
     *
     * @param array $data containing report configs and data.
     * @return array $chartData with defined chart information.
     */
    public function getChartData(array $data = [])
    {
        $labels = [];
        $report = $this->_config;

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
    public function getScripts(array $data = [])
    {
        return [
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
                    ],
                    'block' => 'scriptBotton',
                ],
            ]
        ];
    }
}
