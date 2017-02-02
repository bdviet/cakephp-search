<?php
namespace Search\Test\TestCase\WidgetHandlers;

use Search\WidgetHandlers\Reports\DonutChartReportWidgetHandler;
use Cake\TestSuite\TestCase;

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
