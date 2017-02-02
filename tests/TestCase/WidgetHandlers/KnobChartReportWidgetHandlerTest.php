<?php
namespace Search\Test\TestCase\WidgetHandlers;

use Cake\TestSuite\TestCase;
use Search\WidgetHandlers\Reports\KnobChartReportWidgetHandler;

class KnobChartReportWidgetHandlerTest extends TestCase
{
    public $widgetHandler;

    public function setUp()
    {
        $this->widgetHandler = new KnobChartReportWidgetHandler();
    }

    public function testGetType()
    {
        $this->assertEquals('knobChart', $this->widgetHandler->getType());
    }

    public function testGetScripts()
    {
        $content = $this->widgetHandler->getScripts([]);
        $this->assertNotEmpty($content);
    }
}
