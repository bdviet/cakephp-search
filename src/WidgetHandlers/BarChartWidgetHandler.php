<?php
namespace Search\WidgetHandlers;

use Cake\Utility\Inflector;
use Search\WidgetHandlers\BaseReportWidget;

class BarChartWidgetHandler extends BaseReportWidget
{
    protected $_type = 'barChart';

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
