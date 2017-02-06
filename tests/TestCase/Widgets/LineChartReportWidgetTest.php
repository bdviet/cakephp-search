<?php
namespace Search\Test\TestCase\Widgets;

use Cake\TestSuite\TestCase;
use Search\Widgets\Reports\LineChartReportWidget;

class LineChartReportWidgetTest extends TestCase
{
    public $widget;

    public function setUp()
    {
        $this->widget = new LineChartReportWidget();
    }

    public function getType()
    {
        $this->assertEquals($this->widget->getType(), 'lineChart');
    }

    public function testGetScripts()
    {
        $content = $this->widget->getScripts([]);
        $this->assertNotEmpty($content);
    }


    public function testGetContainerId()
    {
        $config = [
            'slug' => 'testLineChartGraph',
        ];

        $containerId = $this->widget->setContainerId($config);
        $this->assertEquals($containerId, 'graph_' . 'testLineChartGraph');
    }

    public function testGetChartData()
    {
        $config = [
            'modelName' => 'Reports',
            'slug' => 'bar_assigned_by_year',
            'info' => [
                'id' => '00000000-0000-0000-0000-000000000002',
                'model' => 'Bar',
                'widget_type' => 'report',
                'name' => 'Report Bar',
                'query' => '',
                'columns' => '',
                'renderAs' => 'barChart',
                'y_axis' => '',
                'x_axis' => ''
            ]
        ];

        $this->widget->setConfig($config);
        $this->widget->setContainerId($config);

        $result = $this->widget->getChartData([]);
        $this->assertNotEmpty($result['options']['element']);
        $this->assertNotEmpty($result['options']['lineColors']);

        //as the data passed in the method is empty
        $this->assertEquals([], $this->widget->getData());
    }
}
