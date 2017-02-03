<?php
namespace Search\Test\TestCase\Widgets;

use Cake\TestSuite\TestCase;
use Search\Widgets\Reports\BarChartReportWidget;

class BarChartReportWidgetTest extends TestCase
{
    public $widget;

    public function setUp()
    {
        $this->widget = new BarChartReportWidget();
    }

    public function testGetType()
    {
        $this->assertEquals('barChart', $this->widget->getType());
    }

    public function testGetScripts()
    {
        $content = $this->widget->getScripts([]);
        $this->assertNotEmpty($content);
    }
}
