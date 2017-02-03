<?php
namespace Search\Test\TestCase\Widgets;

use Cake\Event\EventList;
use Cake\Event\EventManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\Fixture\FixtureManager;
use Cake\TestSuite\TestCase;
use Search\Widgets\ReportWidgetHandler;

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

        $this->widget = new ReportWidgetHandler();

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
        $result = $this->widget->getReportConfig();
        $this->assertEquals($result, []);
    }


    public function testGetReportConfigWithEventFired()
    {
        $query = $this->Widgets->findById('00000000-0000-0000-0000-000000000002');
        $entity = $query->first();

        $result = $this->widget->getReportConfig(['rootView' => $this->appView, 'entity' => $entity]);

        $this->assertEventFired('Search.Report.getReports', $this->appView->EventManager());
    }
}
