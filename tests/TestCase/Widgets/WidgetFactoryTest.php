<?php
namespace Search\Test\TestCase\Widgets;

use Cake\TestSuite\TestCase;
use Search\Widgets\WidgetFactory;

class WidgetFactoryTest extends TestCase
{
    public function setUp()
    {
        $this->appView = new \Cake\View\View();
    }

    /**
     * @dataProvider dataProviderWidgets
     */
    public function testCreate($type, $widgetConfig, $expectedClass)
    {
        $entity = (object)[
            'widget_type' => $widgetConfig['widget_type'],
        ];

        $widget = WidgetFactory::create($type, ['entity' => $entity]);
        $this->assertInstanceOf($expectedClass, $widget);
    }

    public function dataProviderWidgets()
    {
        return [
            ['saved_search', ['widget_type' => 'saved_search'], 'Search\Widgets\SavedSearchWidget'],
            ['report', ['widget_type' => 'report'], 'Search\Widgets\ReportWidget'],
        ];
    }
}
