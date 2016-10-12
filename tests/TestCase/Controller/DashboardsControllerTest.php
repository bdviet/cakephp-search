<?php
namespace Search\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use Search\Controller\DashboardsController;

/**
 * Search\Controller\DashboardsController Test Case
 */
class DashboardsControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.search.dashboards'
    ];

    public function testSearchNonSearchableModel()
    {
        $this->post('/search/dashboards/search');

        $this->assertResponseError();
    }
}
