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

    public function testGetFieldTypeOperators()
    {
        $expected = [
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
        ];
        $result = $this->SavedSearches->getFieldTypeOperators();
        $this->assertEquals($expected, $result);
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
        $result = $this->SavedSearches->getSearchableFields('dashboards');
        $this->assertTrue(is_array($result));

        // If we have searchable fields, check that they don't have any
        // skipped fields.
        $skippedFields = $this->SavedSearches->getSkippedDisplayFields();
        if (!empty($result) && !empty($skippedFields)) {
            foreach ($skippedFields as $skippedField) {
                $this->assertFalse(in_array($skippedField, $result), "Skipped field [$skippedField] found in searchable fields");
            }
        }
    }
}
