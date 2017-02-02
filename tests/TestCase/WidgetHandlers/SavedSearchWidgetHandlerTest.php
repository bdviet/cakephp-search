<?php
namespace Search\Test\TestCase\WidgetHandlers;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\Fixture\FixtureManager;
use Cake\TestSuite\TestCase;
use Search\WidgetHandlers\SavedSearchWidgetHandler;

class SavedSearchWidgetHandlerTest extends TestCase
{
    protected $widgetHandler;

    public $Widgets;

    public $autoFixtures = true;
    public $dropTables = false;

    public $fixtures = [
        'plugin.search.widgets',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->fixtureManager = new FixtureManager();
        $this->fixtureManager->fixturize($this);

        $this->widgetHandler = new SavedSearchWidgetHandler();

        $config = TableRegistry::exists('Widgets') ? [] : ['className' => 'Search\Model\Table\WidgetsTable'];
        $this->Widgets = TableRegistry::get('Widgets', $config);

        $this->fixtureManager->load($this);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testGetRenderElement()
    {
        $result = $this->widgetHandler->getRenderElement();
        $this->assertEquals($result, 'saved_search');
    }
}
