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
        $this->assertEquals('criteria', $this->SavedSearches->getCriteriaType());
    }

    public function testGetResultType()
    {
        $this->assertEquals('result', $this->SavedSearches->getResultType());
    }

    public function testGetPrivateSharedStatus()
    {
        $this->assertEquals('private', $this->SavedSearches->getPrivateSharedStatus());
    }

    public function testGetSkippedDisplayFields()
    {
        $this->assertEquals(['id'], $this->SavedSearches->getSkippedDisplayFields());
    }

    public function testGetFieldTypeOperators()
    {
        $this->assertEquals(
            [
                'uuid' => ['is' => 'Is'],
                'boolean' => ['is' => 'Is', 'is_not' => 'Is not'],
                'list' => ['is' => 'Is', 'is_not' => 'Is not'],
                'string' => ['contains' => 'Contains', 'not_contains' => 'Does not contain', 'starts_with' => 'Starts with', 'ends_with' => 'Ends with'],
                'text' => ['contains' => 'Contains', 'not_contains' => 'Does not contain', 'starts_with' => 'Starts with', 'ends_with' => 'Ends with'],
                'textarea' => ['contains' => 'Contains', 'not_contains' => 'Does not contain', 'starts_with' => 'Starts with', 'ends_with' => 'Ends with'],
                'email' => ['contains' => 'Contains', 'not_contains' => 'Does not contain', 'starts_with' => 'Starts with', 'ends_with' => 'Ends with'],
                'phone' => ['contains' => 'Contains', 'not_contains' => 'Does not contain', 'starts_with' => 'Starts with', 'ends_with' => 'Ends with'],
                'url' => ['contains' => 'Contains', 'not_contains' => 'Does not contain', 'starts_with' => 'Starts with', 'ends_with' => 'Ends with'],
                'integer' => ['is' => 'Is', 'is_not' => 'Is not', 'greater' => 'Greater', 'less' => 'Less'],
                'datetime' => ['is' => 'Is', 'is_not' => 'Is not', 'greater' => 'Greater', 'less' => 'Less'],
                'date' => ['is' => 'Is', 'is_not' => 'Is not', 'greater' => 'Greater', 'less' => 'Less'],
                'time' => ['is' => 'Is', 'is_not' => 'Is not', 'greater' => 'Greater', 'less' => 'Less']
            ],
            $this->SavedSearches->getFieldTypeOperators()
        );
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

    public function testGetSearchableFields()
    {
        $fields = $this->fixtureManager->loaded()['plugin.search.dashboards']->schema()->columns();
        $skippedFields = $this->SavedSearches->getSkippedDisplayFields();
        $this->assertEquals(array_diff($fields, $skippedFields), $this->SavedSearches->getSearchableFields('dashboards'));
    }
}
