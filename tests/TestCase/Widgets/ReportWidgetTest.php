<?php
namespace Search\Test\TestCase\Widgets;

use Cake\Event\EventList;
use Cake\Event\EventManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\Fixture\FixtureManager;
use Cake\TestSuite\TestCase;
use Search\Widgets\ReportWidget;

class ReportWidgetTest extends TestCase
{
    protected $widget;

    public $Widgets;

    public $fixtureManager;
    public $eventManager;
    public $appView;

    public $autoFixtures = true;
    public $dropTables = false;

    public $fixtures = [
        'plugin.search.widgets',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->appView = new \Cake\View\View();

        $this->appView->EventManager()->trackEvents(true);
        $this->appView->EventManager()->setEventList(new EventList());

        $this->fixtureManager = new FixtureManager();
        $this->fixtureManager->fixturize($this);

        $this->widget = new ReportWidget();

        $config = TableRegistry::exists('Widgets') ? [] : ['className' => 'Search\Model\Table\WidgetsTable'];
        $this->Widgets = TableRegistry::get('Widgets', $config);


        $this->fixtureManager->load($this);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->eventManager, $this->fixtureManager);
    }

    public function testGetRenderElement()
    {
        $result = $this->widget->getRenderElement();
        $this->assertEquals($result, 'graph');
    }

    public function testGetReportConfigWithoutRootView()
    {
        $result = $this->widget->getReport();
        $this->assertEquals($result, []);
    }

    public function testGetReportInstanceWithoutArgs()
    {
        $instance = $this->widget->getReportInstance();
        $this->assertEquals($instance, null);
    }

    /**
     * @dataProvider getInstancesList
     */
    public function testGetReportInstance($config, $expectedClass)
    {
        $instance = $this->widget->getReportInstance($config);
        $this->assertInstanceOf($expectedClass, $instance);

        $this->widget->_instance = $instance;

        $this->widget->setContainerId($config['config']);

        $this->assertEquals($config['config']['info']['renderAs'], $this->widget->getType());
        $this->assertEquals('graph_' . $config['config']['slug'], $this->widget->getContainerId());
        $this->assertEquals([], $this->widget->getOptions());
        $this->assertEquals([], $this->widget->getData());

        $this->widget->setConfig($config['config']);
        $this->assertEquals($this->widget->getConfig(), $config['config']);

        $dummyData = ['foo' => 'bar'];

        $this->widget->setData($dummyData);
        $this->assertEquals($dummyData, $this->widget->getData());
    }

    public function getInstancesList()
    {
        $configs = [
           [['config' => ['slug' => 'barChartTest', 'info' => ['renderAs' => 'barChart']]], '\Search\Widgets\Reports\BarChartReportWidget'],
           [['config' => ['slug' => 'lineChartTest', 'info' => ['renderAs' => 'lineChart']]], '\Search\Widgets\Reports\LineChartReportWidget'],
           [['config' => ['slug' => 'donutChartTest', 'info' => ['renderAs' => 'donutChart']]], '\Search\Widgets\Reports\DonutChartReportWidget'],
           [['config' => ['slug' => 'knobChartTest', 'info' => ['renderAs' => 'knobChart']]], '\Search\Widgets\Reports\KnobChartReportWidget'],
        ];

        return $configs;
    }

    /**
     * @dataProvider getInstancesList
     */
    public function testGetReportConfigWithEvent($config, $expectedClass)
    {
        $entity = (object)[
            'widget_id' => '123123',
        ];

        $instance = $this->widget->getReportInstance($config);
        $this->assertInstanceOf($expectedClass, $instance);

        $this->widget->_instance = $instance;

        $result = $this->widget->getReport(['rootView' => $this->appView, 'entity' => $entity]);

        $events = $this->appView->EventManager()->getEventList();
        $events[0]->result = ['foo' => 'bar'];
        $this->assertEventFired('Search.Report.getReports', $this->appView->EventManager());
    }

    public function testGetReportWithMock()
    {
        $dummyReports = [
            'Reports' => [
                'foo_graph_by_assigned_to' => [
                    'id' => '00000000-0000-0000-0000-000000000001',
                    'model' => 'Foo',
                    'widget_type' => 'report',
                    'name' => 'Report Foo',
                    'query' => '',
                    'columns' => '',
                    'renderAs' => 'barChart',
                    'y_axis' => 'a',
                    'x_axis' => 'b',
                ],
                'bar_assigned_by_year' => [
                    'id' => '00000000-0000-0000-0000-000000000002',
                    'model' => 'Bar',
                    'widget_type' => 'report',
                    'name' => 'Report Bar',
                    'query' => '',
                    'columns' => '',
                    'renderAs' => 'lineChart',
                    'y_axis' => '',
                    'x_axis' => '',
                ]
            ]
        ];

        $expectedReport = [
            'modelName' => 'Reports',
            'slug' => 'bar_assigned_by_year',
            'info' => [
                'id' => '00000000-0000-0000-0000-000000000002',
                'model' => 'Bar',
                'widget_type' => 'report',
                'name' => 'Report Bar',
                'query' => '',
                'columns' => '',
                'renderAs' => 'lineChart',
                'y_axis' => '',
                'x_axis' => ''
            ]
        ];

        $entity = (object)[
            'widget_id' => '00000000-0000-0000-0000-000000000002',
        ];

        $widget = $this->getMockBuilder('Search\Widgets\ReportWidget')->getMock();

        $widget->expects($this->any())
            ->method('getReports')
            ->with(['rootView' => $this->appView])
            ->will($this->returnValue($dummyReports));

        $reports = $widget->getReports(['rootView' => $this->appView]);

        $report = $this->widget->getReport(['entity' => $entity, 'reports' => $reports]);

        $this->assertEquals($report, $expectedReport);
    }

    public function testGetResults()
    {
        $result = $this->widget->getResults([]);
        $this->assertEquals($result, []);
    }
}
