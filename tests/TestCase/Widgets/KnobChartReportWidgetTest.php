<?php
namespace Search\Test\TestCase\Widgets;

use Cake\TestSuite\TestCase;
use Search\Widgets\Reports\KnobChartReportWidget;

class KnobChartReportWidgetTest extends TestCase
{
    public $widget;

    public function setUp()
    {
        $this->widget = new KnobChartReportWidget();
    }

    public function testGetType()
    {
        $this->assertEquals('knobChart', $this->widget->getType());
    }

    public function testGetScripts()
    {
        $content = $this->widget->getScripts([]);
        $this->assertNotEmpty($content);
    }
}
