<?php
namespace Search\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * Chart helper
 */
class ChartHelper extends Helper
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * getChartData method
     * Re-matching report data with
     * correlation to x/y axises
     * @param array $renderData for the widget
     * @param array $chartOptions with report config
     * @return array $result matching X/Y axis with data
     */
    public function getChartData($renderData, $chartOptions = [])
    {
        $result = [];

        if (!empty($renderData) && !empty($chartOptions)) {
            $xAxisField = $chartOptions['data']['info']['x_axis'];
            $yAxisField = $chartOptions['data']['info']['y_axis'];

            foreach ($renderData as $k => $fields) {
                $result[] = [
                    'x_axis' => $fields[$xAxisField],
                    'y_axis' => $fields[$yAxisField]
                ];
            }
        }

        return json_encode($result);
    }
}
