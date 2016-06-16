<?php
namespace Search\Model\Table;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;
use Search\Model\Entity\SavedSearch;

/**
 * SavedSearches Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 */
class SavedSearchesTable extends Table
{
    /**
     * Criteria type value
     */
    const TYPE_CRITERIA = 'criteria';

    /**
     * Result type value
     */
    const TYPE_RESULT = 'result';

    /**
     * Private shared status value
     */
    const SHARED_STATUS_PRIVATE = 'private';

    /**
     * Delete older than value
     */
    const DELETE_OLDER_THAN = '-3 hours';

    /**
     * List of display fields to be skipped.
     *
     * @var array
     */
    protected $_skipDisplayFields = ['id'];

    /**
     * Search query default properties
     *
     * @var array
     */
    protected $_queryDefaults = [
        'sort_by_field' => 'created',
        'sort_by_order' => 'desc',
        'limit' => 10
    ];

    /**
     * Operators to SQL operator
     *
     * @var array
     */
    protected $_sqlOperators = [
        'uuid' => ['operator' => 'IN'],
        'boolean' => [
            'is' => ['operator' => 'IS'],
            'is_not' => ['operator' => 'IS NOT']
        ],
        'list' => [
            'is' => ['operator' => 'IN'],
            'is_not' => ['operator' => 'NOT IN']
        ],
        'string' => [
            'contains' => ['operator' => 'LIKE', 'pattern' => '%{{value}}%'],
            'not_contains' => ['operator' => 'NOT LIKE', 'pattern' => '%{{value}}%'],
            'starts_with' => ['operator' => 'LIKE', 'pattern' => '{{value}}%'],
            'ends_with' => ['operator' => 'LIKE', 'pattern' => '%{{value}}']
        ],
        'text' => [
            'contains' => ['operator' => 'LIKE', 'pattern' => '%{{value}}%'],
            'not_contains' => ['operator' => 'NOT LIKE', 'pattern' => '%{{value}}%'],
            'starts_with' => ['operator' => 'LIKE', 'pattern' => '{{value}}%'],
            'ends_with' => ['operator' => 'LIKE', 'pattern' => '%{{value}}']
        ],
        'textarea' => [
            'contains' => ['operator' => 'LIKE', 'pattern' => '%{{value}}%'],
            'not_contains' => ['operator' => 'NOT LIKE', 'pattern' => '%{{value}}%'],
            'starts_with' => ['operator' => 'LIKE', 'pattern' => '{{value}}%'],
            'ends_with' => ['operator' => 'LIKE', 'pattern' => '%{{value}}']
        ],
        'integer' => [
            'is' => ['operator' => 'IN'],
            'is_not' => ['operator' => 'NOT IN'],
            'greater' => ['operator' => '>'],
            'less' => ['operator' => '<']
        ],
        'datetime' => [
            'is' => ['operator' => 'IN'],
            'is_not' => ['operator' => 'NOT IN'],
            'greater' => ['operator' => '>'],
            'less' => ['operator' => '<']
        ],
        'date' => [
            'is' => ['operator' => 'IN'],
            'is_not' => ['operator' => 'NOT IN'],
            'greater' => ['operator' => '>'],
            'less' => ['operator' => '<']
        ],
        'time' => [
            'is' => ['operator' => 'IN'],
            'is_not' => ['operator' => 'NOT IN'],
            'greater' => ['operator' => '>'],
            'less' => ['operator' => '<']
        ]
    ];

    /**
     * Filter basic search allowed field types
     *
     * @var array
     */
    protected $_basicSearchFieldTypes = [
        'uuid',
        'list',
        'string',
        'text',
        'textarea',
        'integer',
        'datetime',
        'date',
        'time'
    ];

    /**
     * Basic search default fields
     *
     * @var array
     */
    protected $_basicSearchDefaultFields = [
        'modified',
        'created'
    ];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('saved_searches');
        $this->displayField('name');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'Search.Users'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->uuid('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name', 'update')
            ->allowEmpty('name', 'create');

        $validator
            ->requirePresence('type', 'create')
            ->notEmpty('type');

        $validator
            ->requirePresence('model', 'create')
            ->notEmpty('model');

        $validator
            ->requirePresence('shared', 'create')
            ->notEmpty('shared');

        $validator
            ->requirePresence('content', 'create')
            ->notEmpty('content');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }

    /**
     * Returns criteria type.
     *
     * @return string
     */
    public function getCriteriaType()
    {
        return static::TYPE_CRITERIA;
    }

    /**
     * Returns result type.
     *
     * @return string
     */
    public function getResultType()
    {
        return static::TYPE_RESULT;
    }

    /**
     * Returns private shared status.
     *
     * @return string
     */
    public function getPrivateSharedStatus()
    {
        return static::SHARED_STATUS_PRIVATE;
    }

    /**
     * Returns a list of display fields to be skipped.
     *
     * @return array
     */
    public function getSkippedDisplayFields()
    {
        return $this->_skipDisplayFields;
    }

    /**
     * Search method
     *
     * @param  string $model model name
     * @param  bool   $advanced advanced search flag
     * @return void
     */
    public function search($model, $user, $data, $advanced = false, $preSave = false)
    {
        $criteria = [];
        if (isset($data['criteria'])) {
            $criteria = $data['criteria'];
        }
        $where = $this->prepareWhereStatement($criteria, $model, $advanced);
        $table = TableRegistry::get($model);

        $query = $data;
        /*
        do not include criteria when pre-saving search results
         */
        unset($query['criteria']);

        /*
        use query defaults if not set
         */
        $query = array_merge($this->_queryDefaults, $query);

        $query['result'] = $table
            ->find('all')
            ->where($where)
            ->order([$query['sort_by_field'] => $query['sort_by_order']])
        ;

        /*
        set limit if not 0
         */
        if (0 < (int)$query['limit']) {
            $query['result']->limit($query['limit']);
        }

        /*
        if in advanced mode, pre-save search criteria and results
         */
        if ($preSave && !empty($criteria)) {
            $preSaveIds = $this->preSaveSearchCriteriaAndResults(
                $model,
                $query,
                $data,
                $user['id']
            );
            $result['saveSearchCriteriaId'] = $preSaveIds['saveSearchCriteriaId'];
            $result['saveSearchResultsId'] = $preSaveIds['saveSearchResultsId'];
        }
        $result['entities'] = $query;

        return $result;
    }

    /**
     * Return Table's searchable fields.
     *
     * @param  \Cake\ORM\Table|string $table Table object or name.
     * @return array
     */
    public function getSearchableFields($table)
    {
        $result = [];
        /*
        get Table instance
         */
        if (is_string($table)) {
            $table = TableRegistry::get($table);
        }

        if (method_exists($table, 'getSearchableFields') && is_callable([$table, 'getSearchableFields'])) {
            $result = $table->getSearchableFields();
        } else {
            $db = ConnectionManager::get('default');
            $collection = $db->schemaCollection();
            // by default, all fields are searchable
            $result = $collection->describe($table->table())->columns();
        }
        /*
        skip display fields
         */
        $result = array_diff($result, $this->_skipDisplayFields);

        return $result;
    }

    /**
     * Method responsible for retrieving specified fields properties.
     *
     * @param  mixed  $table  name or instance of the Table
     * @param  array  $fields fields
     * @return string         field input
     */
    public function getSearchableFieldProperties($table, array $fields)
    {
        $result = [];
        if (!empty($fields)) {
            /*
            get Table instance
             */
            if (is_string($table)) {
                $table = TableRegistry::get($table);
            }
            $db = ConnectionManager::get('default');
            $collection = $db->schemaCollection();

            foreach ($fields as $field) {
                $result[$field] = $collection->describe($table->table())->column($field);
            }
        }

        return $result;
    }

    /**
     * Generates and returns searchable fields labels.
     *
     * @param  array  $fields searchable fields
     * @return array
     */
    public function getSearchableFieldLabels(array $fields)
    {
        foreach ($fields as $fieldName => &$fieldProperties) {
            $fieldProperties['label'] = Inflector::humanize($fieldName);
        }

        return $fields;
    }

    /**
     * Return Table's listing fields.
     *
     * @param  \Cake\ORM\Table|string $table Table object or name.
     * @return array
     */
    public function getListingFields($table)
    {
        $result = [];
        /*
        get Table instance
         */
        if (is_string($table)) {
            $table = TableRegistry::get($table);
        }

        if (method_exists($table, 'getListingFields') && is_callable([$table, 'getListingFields'])) {
            $result = $table->getListingFields();
        } else {
            $result[] = $table->primaryKey();
            $result[] = $table->displayField();
            foreach ($this->_basicSearchDefaultFields as $field) {
                if ($table->hasField($field)) {
                    $result[] = $field;
                }
            }
        }
        /*
        skip display fields
         */
        $result = array_diff($result, $this->_skipDisplayFields);

        return $result;
    }

    /**
     * Prepare search query's where statement
     *
     * @param  array  $data     search fields
     * @param  string $model    model name
     * @param  bool   $advanced advanced search flag
     * @return array
     */
    public function prepareWhereStatement(array $data, $model, $advanced = false)
    {
        $result = [];

        if (!$advanced) {
            $result = $this->_basicWhereStatement($data, $model);
        } else {
            $result = $this->_advancedWhereStatement($data, $model);
        }

        return $result;
    }

    /**
     * Prepare basic search query's where statement
     *
     * @param  array  $data  search fields
     * @param  string $model model name
     * @return array
     */
    protected function _basicWhereStatement(array $data, $model)
    {
        $result = [];
        if (!empty($data['query'])) {
            $fields = $this->getSearchableFields($model);
            $fields = $this->getSearchableFieldProperties($model, $fields);
            foreach ($fields as $field => $properties) {
                if (in_array($properties['type'], $this->_basicSearchFieldTypes)) {
                    $result['OR'][$field . ' LIKE'] = '%' . $data['query'] . '%';
                }
            }
        }

        return $result;
    }

    /**
     * Prepare advanced search query's where statement
     *
     * @param  array  $data  search fields
     * @param  string $model model name
     * @return array
     */
    protected function _advancedWhereStatement(array $data, $model)
    {
        $result = [];
        foreach ($data as $fieldName => $criterias) {
            if (!empty($criterias)) {
                foreach ($criterias as $criteria) {
                    $type = $criteria['type'];
                    $value = $criteria['value'];
                    if ('' === trim($value)) {
                        continue;
                    }
                    $operator = $criteria['operator'];
                    if (isset($this->_sqlOperators[$type][$operator]['pattern'])) {
                        $value = str_replace(
                            '{{value}}',
                            $value,
                            $this->_sqlOperators[$type][$operator]['pattern']
                        );
                    }
                    $sqlOperator = $this->_sqlOperators[$type][$operator]['operator'];
                    $key = $fieldName . ' ' . $sqlOperator;

                    if (array_key_exists($key, $result)) {
                        switch ($type) {
                            case 'uuid':
                            case 'list':
                                if (is_array($result[$key])) {
                                    array_push($result[$key], $value);
                                } else {
                                    $result[$key] = [$result[$key], $value];
                                }
                                break;

                            case 'integer':
                            case 'datetime':
                            case 'date':
                            case 'time':
                                switch ($operator) {
                                    case 'greater':
                                    case 'less':
                                        if (is_array($result[$key])) {
                                            array_push($result[$key]['AND'], $value);
                                        } else {
                                            $result[$key] = ['AND' => [$result[$key], $value]];
                                        }
                                        break;

                                    default:
                                        if (is_array($result[$key])) {
                                            array_push($result[$key], $value);
                                        } else {
                                            $result[$key] = [$result[$key], $value];
                                        }
                                        break;
                                }
                                break;

                            case 'string':
                            case 'text':
                            case 'textarea':
                                if (is_array($result[$key])) {
                                    array_push($result[$key]['OR'], $value);
                                } else {
                                    $result[$key] = ['OR' => [$result[$key], $value]];
                                }
                                break;
                        }
                    } else {
                        $result[$key] = $value;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Method that pre-saves search criteria and results and returns saved records ids.
     *
     * @param  string $model  model name
     * @param  array  $query  results query
     * @param  array  $data   request data
     * @param  string $userId user id
     * @return array
     */
    public function preSaveSearchCriteriaAndResults($model, array $query, $data, $userId)
    {
        $result = [];
        /*
        delete old pre-saved searches
         */
        $this->_deleteOldPreSavedSearches();

        /*
        pre-save search criteria
         */
        $result['saveSearchCriteriaId'] = $this->_preSaveSearchCriteria($model, $data, $userId);
        /*
        pre-save search results
         */
        $result['saveSearchResultsId'] = $this->_preSaveSearchResults($model, $query, $userId);

        return $result;
    }

    /**
     * Method that deletes old pre-save search records.
     *
     * @return void
     */
    protected function _deleteOldPreSavedSearches()
    {
        $this->deleteAll([
            'modified <' => new \DateTime(static::DELETE_OLDER_THAN),
            'name IS' => null
        ]);
    }

    /**
     * Pre-save search criteria and return record id.
     *
     * @param  string $model  model name
     * @param  array  $data   request data
     * @param  string $userId user id
     * @return string
     */
    protected function _preSaveSearchCriteria($model, $data, $userId)
    {
        $search = $this->newEntity();
        $search->type = $this->getCriteriaType();
        $search->user_id = $userId;
        $search->model = $model;
        $search->shared = $this->getPrivateSharedStatus();
        $search->content = json_encode($data);
        /*
        save search criteria
         */
        $this->save($search);

        return $search->id;
    }

    /**
     * Pre-save search results and return record id.
     *
     * @param  string $model  model name
     * @param  array  $query  results query
     * @param  string $userId user id
     * @return string
     */
    protected function _preSaveSearchResults($model, array $query, $userId)
    {
        $search = $this->newEntity();
        $search->type = $this->getResultType();
        $search->user_id = $userId;
        $search->model = $model;
        $search->shared = $this->getPrivateSharedStatus();
        $search->content = json_encode($query);
        /*
        save search results
         */
        $this->save($search);

        return $search->id;
    }
}
