<?php
namespace Search\WidgetHandlers\Reports;

use Cake\Utility\Inflector;
use Search\WidgetHandlers\Reports\BaseReportGraphs;

class DonutChartReportWidgetHandler extends BaseReportGraphs
{
    public $_type = 'donutChart';

    /**
     * getChartData method
     *
     * Specifies chart data/config of the DonutChart.
     *
     * @param array $data containing configs.
     * @return array $chartData for graph rendering.
     */
    public function getChartData(array $data = [])
    {
        $report = $this->_config;

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

    /**
     * getScripts method
     *
     * Assembles JS/CSS libs for the graph rendering.
     *
     * @param array $data containing widgetHandler info.
     * @return array $content with the scripts.
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
