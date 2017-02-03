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
        $this->assertEquals($containerId, $this->widget::GRAPH_PREFIX . 'testLineChartGraph');
    }
}
