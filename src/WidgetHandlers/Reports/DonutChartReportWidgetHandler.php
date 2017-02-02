<?php
namespace Search\WidgetHandlers\Reports;

use Cake\Utility\Inflector;
use Search\WidgetHandlers\ReportWidgetHandler;

class DonutChartReportWidgetHandler extends ReportWidgetHandler
{
    protected $_type = 'donutChart';

    public function prepareChartData($data = [])
    {
        $report = $this->_report;

        $chartData = [
            'chart' => $this->_type,
            'options' => [
                'element' => self::GRAPH_PREFIX . $report['slug'],
                'resize' => true,
            ],
        ];

        $options = [
            'data' => []
        ];

        foreach ($data as $item) {
            array_push($options['data'], [
                'label' => $item[$report['info']['label']],
                'value' => $item[$report['info']['value']],
            ]);
        }

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
