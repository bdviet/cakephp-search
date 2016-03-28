<?php
namespace Search\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use Search\Controller\Component\SearchableComponent;

/**
 * Search\Controller\Component\SearchableComponent Test Case
 */
class SearchableComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Search\Controller\Component\SearchableComponent
     */
    public $Searchable;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Searchable = new SearchableComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Searchable);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
