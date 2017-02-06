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
    public function testCreate($widgetConfig, $expectedClass)
    {
        $entity = (object)[
            'widget_type' => $widgetConfig['widget_type'],
        ];

        $widget = WidgetFactory::create($widgetConfig['widget_type'], ['entity' => $entity]);

        if ($widgetConfig['widget_type'] == 'foobar') {
            $this->assertEquals($widget, null);
        } else {
            $this->assertInstanceOf($expectedClass, $widget);
        }
    }

    /**
     * @dataProvider dataProviderWidgetTypes
     */
    public function testGetType($widgetConfig, $expectedClass)
    {
        $entity = (object)[
            'widget_type' => $widgetConfig['widget_type'],
        ];

        $widget = WidgetFactory::create($widgetConfig['widget_type'], ['entity' => $entity]);

        $this->assertEquals($widgetConfig['widget_type'], $widget->getType());
    }

    public function dataProviderWidgets()
    {
        return [
            [['widget_type' => 'saved_search'], 'Search\Widgets\SavedSearchWidget'],
            [['widget_type' => 'report'], 'Search\Widgets\ReportWidget'],
            [['widget_type' => 'foobar'], ''],
        ];
    }

    public function dataProviderWidgetTypes()
    {
        return [
            [['widget_type' => 'saved_search'], 'Search\Widgets\SavedSearchWidget'],
        ];
    }
}
