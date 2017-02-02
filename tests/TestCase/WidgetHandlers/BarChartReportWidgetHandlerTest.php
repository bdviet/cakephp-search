<?php
namespace Search\Test\TestCase\WidgetHandlers;

use Cake\TestSuite\TestCase;
use Search\WidgetHandlers\Reports\BarChartReportWidgetHandler;

class BarChartReportWidgetHandlerTest extends TestCase
{
    public $widgetHandler;

    public function setUp()
    {
        $this->widgetHandler = new BarChartReportWidgetHandler();
    }

    public function testGetType()
    {
        $this->assertEquals('barChart', $this->widgetHandler->getType());
    }

    public function testGetScripts()
    {
        $content = $this->widgetHandler->getScripts([]);
        $this->assertNotEmpty($content);
    }
}
