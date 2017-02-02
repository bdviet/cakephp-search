<?php
namespace Search\Test\TestCase\WidgetHandlers;

use Cake\TestSuite\TestCase;
use Search\WidgetHandlers\Reports\LineChartReportWidgetHandler;

class LineChartReportWidgetHandlerTest extends TestCase
{
    public $widgetHandler;

    public function setUp()
    {
        $this->widgetHandler = new LineChartReportWidgetHandler();
    }

    public function getType()
    {
        $this->assertEquals($this->widgetHandler->getType(), 'lineChart');
    }

    public function testGetScripts()
    {
        $content = $this->widgetHandler->getScripts([]);
        $this->assertNotEmpty($content);
    }
}
