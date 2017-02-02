<?php
namespace Search\Test\TestCase\WidgetHandlers;

use Cake\TestSuite\TestCase;
use Search\WidgetHandlers\Reports\DonutChartReportWidgetHandler;

class DonutChartReportWidgetHandlerTest extends TestCase
{
    public $widgetHandler;

    public function setUp()
    {
        $this->widgetHandler = new DonutChartReportWidgetHandler();
    }

    public function testGetType()
    {
        $this->assertEquals('donutChart', $this->widgetHandler->getType());
    }

    public function testGetScripts()
    {
        $content = $this->widgetHandler->getScripts([]);
        $this->assertNotEmpty($content);
    }
}
