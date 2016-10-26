<?php
namespace Search\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Search\View\Helper\ChartHelper;

/**
 * Search\View\Helper\ChartHelper Test Case
 */
class ChartHelperTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Search\View\Helper\ChartHelper
     */
    public $Chart;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Chart = new ChartHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Chart);

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
