<?php
namespace Search\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Search\Model\Table\DashboardsTable;

/**
 * Search\Model\Table\DashboardsTable Test Case
 */
class DashboardsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Search\Model\Table\DashboardsTable
     */
    public $Dashboards;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.search.dashboards',
        'plugin.search.roles',
        'plugin.search.saved_searches',
        'plugin.search.users',
        'plugin.search.dashboards_saved_searches'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Dashboards') ? [] : ['className' => 'Search\Model\Table\DashboardsTable'];
        $this->Dashboards = TableRegistry::get('Dashboards', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Dashboards);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}