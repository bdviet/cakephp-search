<?php
namespace Search\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Search\Model\Table\SavedSearchesTable;

/**
 * Search\Model\Table\SavedSearchesTable Test Case
 */
class SavedSearchesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Search\Model\Table\SavedSearchesTable
     */
    public $SavedSearches;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.search.dashboards',
        'plugin.search.saved_searches',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('SavedSearches') ? [] : ['className' => 'Search\Model\Table\SavedSearchesTable'];
        $this->SavedSearches = TableRegistry::get('SavedSearches', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SavedSearches);

        parent::tearDown();
    }

    public function testGetCriteriaType()
    {
        $result = $this->SavedSearches->getCriteriaType();
        $this->assertEquals('criteria', $result);
    }

    public function testGetResultType()
    {
        $result = $this->SavedSearches->getResultType();
        $this->assertEquals('result', $result);
    }

    public function testGetDefaultSortByOrder()
    {
        $result = $this->SavedSearches->getDefaultSortByOrder();
        $this->assertEquals($result, 'desc');
    }

    public function testGetDefaultLimit()
    {
        $this->assertEquals($this->SavedSearches->getDefaultLimit(), 10);
    }

    public function testGetPrivateSharedStatus()
    {
        $result = $this->SavedSearches->getPrivateSharedStatus();
        $this->assertEquals('private', $result);
    }

    public function testGetSkippedDisplayFields()
    {
        $expected = ['id'];
        $result = $this->SavedSearches->getSkippedDisplayFields();
        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetSearchableFields()
    {
        $result = $this->SavedSearches->getSearchableFields('Widgets');
        $this->assertEventFired('Search.Model.Search.searchabeFields', $this->EventManager());
    }

    public function testGetListingFields()
    {
        $result = $this->SavedSearches->getListingFields('Dashboards');
        $this->assertNotEmpty($result);
        $this->assertEquals($result, ['name', 'modified', 'created']);
    }

    /**
     * @expectedException \RuntimeException
     * @dataProvider dataProviderGetSearchCriteria
     */
    public function testGetSearchCriteria($config)
    {
        $result = $this->SavedSearches->getSearchCriteria(['query' => $config['query']], $config['table']);
    }

    public function dataProviderGetSearchCriteria()
    {
        return [
            [['query' => 'SELECT id,created FROM dashboards LIMIT 2', 'table' => 'Dashboards']],
        ];
    }

    public function test_prepareWhereStatement()
    {
        $class = new \ReflectionClass('Search\Model\Table\SavedSearchesTable');
        $method = $class->getMethod('_prepareWhereStatement');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->SavedSearches, [
            [],
            'Dashboards'
        ]);

        $this->assertEquals($result, []);
    }

    public function testGetSavedSearchesFindAll()
    {
        $resultset = $this->SavedSearches->getSavedSearches();
        $this->assertInternalType('array', $resultset);
        $this->assertInstanceOf('\Search\Model\Entity\SavedSearch', current($resultset));
    }

    public function testGetSavedSearchesByUser()
    {
        $records = $this->fixtureManager->loaded()['plugin.search.saved_searches']->records;
        $userId = current($records)['user_id'];
        $resultset = $this->SavedSearches->getSavedSearches([$userId]);
        $this->assertInternalType('array', $resultset);
        $this->assertInstanceOf('\Search\Model\Entity\SavedSearch', current($resultset));

        foreach ($resultset as $entity) {
            $this->assertEquals($userId, $entity->user_id);
        }
    }

    public function testGetSavedSearchesByModel()
    {
        $records = $this->fixtureManager->loaded()['plugin.search.saved_searches']->records;
        $model = current($records)['model'];
        $resultset = $this->SavedSearches->getSavedSearches([], [$model]);
        $this->assertInternalType('array', $resultset);
        $this->assertInstanceOf('\Search\Model\Entity\SavedSearch', current($resultset));

        foreach ($resultset as $entity) {
            $this->assertEquals($model, $entity->model);
        }
    }

    public function testGetLimitOptions()
    {
        $result = $this->SavedSearches->getLimitOptions();
        $this->assertNotEmpty($result);
        $this->assertInternalType('array', $result);
    }

    public function testGetSortByOrderOptions()
    {
        $result = $this->SavedSearches->getSortByOrderOptions();
        $this->assertNotEmpty($result);
        $this->assertInternalType('array', $result);
    }

    public function testValidateData()
    {
        $data = [
            'criteria' => [
                'name' => [
                    10 => [
                        'type' => 'string',
                        'operator' => 'contains',
                        'value' => 'foo'
                    ]
                ]
            ],
            'display_columns' => [
                'name', 'modified', 'created'
            ],
            'sort_by_field' => 'name',
            'sort_by_order' => 'asc',
            'limit' => '20'
        ];
        $result = $this->SavedSearches->validateData('Dashboards', $data);
        $this->assertEquals($data, $result);
    }

    public function testValidateDataWrong()
    {
        $data = [
            'criteria' => [
                'foo' => [
                    10 => [
                        'type' => 'string',
                        'operator' => 'contains',
                        'value' => 'foo'
                    ]
                ]
            ],
            'display_columns' => [
                'foo'
            ],
            'sort_by_field' => 'foo',
            'sort_by_order' => [
                'foo'
            ],
            'limit' => [
                '999'
            ]
        ];
        $result = $this->SavedSearches->validateData('Dashboards', $data);

        $this->assertEmpty($result['criteria']);
        $this->assertEmpty($result['display_columns']);

        $expected = TableRegistry::get('Dashboards')->displayField();
        $this->assertEquals($expected, $result['sort_by_field']);

        $expected = $this->SavedSearches->getDefaultSortByOrder();
        $this->assertEquals($expected, $result['sort_by_order']);

        $expected = $this->SavedSearches->getDefaultLimit();
        $this->assertEquals($expected, $result['limit']);
    }
}
