<?php
namespace Search\WidgetHandlers\Reports;

use Search\WidgetHandlers\Reports\BaseReportGraphs;

class KnobChartReportWidgetHandler extends BaseReportGraphs
{
    public $_type = 'knobChart';

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
        $report = $this->_report;

        $chartData = [
            'chart' => $this->_type,
            'options' => [
                'element' => self::GRAPH_PREFIX . $report['slug'],
                'resize' => true,
            ],
        ];
        $tmp = [];
        if (isset($report['info']['max']) && isset($report['info']['value'])) {
            foreach ($data as $item) {
                $tmp[] = [
                    'max' => $item[$report['info']['max']],
                    'value' => $item[$report['info']['value']],
                    'label' => $item[$report['info']['label']],
                ];
            }
        }

        $options = [
            'data' => $tmp,
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
                        'AdminLTE./plugins/knob/jquery.knob',
                    ],
                    'block' => 'scriptBotton',
                ],
            ]
        ];
    }
}
