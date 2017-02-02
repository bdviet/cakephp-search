<?php
namespace Search\Test\TestCase\WidgetHandlers;

use Search\WidgetHandlers\Reports\BarChartReportWidgetHandler;
use Cake\TestSuite\TestCase;

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
