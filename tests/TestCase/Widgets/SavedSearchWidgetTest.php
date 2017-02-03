<?php
namespace Search\Test\TestCase\Widgets;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\Fixture\FixtureManager;
use Cake\TestSuite\TestCase;
use Search\Widgets\SavedSearchWidget;

class SavedSearchWidgetTest extends TestCase
{
    protected $widget;

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

        $this->widget = new SavedSearchWidget();

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
        $result = $this->widget->getRenderElement();
        $this->assertEquals($result, 'table');
    }
}
