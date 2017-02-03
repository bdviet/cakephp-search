<?php
namespace Search\Test\TestCase\Widgets;

use Cake\TestSuite\TestCase;
use Search\Widgets\Reports\DonutChartReportWidget;

class DonutChartReportWidgetTest extends TestCase
{
    public $widget;

    public function setUp()
    {
        $this->widget = new DonutChartReportWidget();
    }

    public function testGetType()
    {
        $this->assertEquals('donutChart', $this->widget->getType());
    }

    public function testGetScripts()
    {
        $content = $this->widget->getScripts([]);
        $this->assertNotEmpty($content);
    }

    public function testSetContainerId()
    {
        $config = [
            'slug' => 'barChart',
        ];

        $containerId = $this->widget->setContainerId($config);
        $this->assertEquals($containerId, 'graph_' . $config['slug']);
    }
}
