<?php
namespace Search\Model\Table;

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Validation\Validator;
use RuntimeException;
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
     * Target table searchable fields.
     *
     * @var array
     */
    protected $_searchableFields = [];

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
     * Filter basic search allowed field types
     *
     * @var array
     */
    protected $_basicSearchFieldTypes = ['string', 'text', 'textarea'];

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
        $this->addBehavior('Muffin/Trash.Trash');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
        # TODO : Temporary disabled
        #$rules->add($rules->existsIn(['user_id'], 'Users'));

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
     * @param  string $tableName table name
     * @param  array  $user user
     * @param  array  $requestData request data
     * @return array
     */
    public function search($tableName, $user, $requestData)
    {
        $data = array_merge($this->_queryDefaults, $requestData);

        if (empty($data['result'])) {
            // get search results
            $data['result'] = $this->_getResults($data, $tableName);
        }

        // pre-save search criteria and results
        $preSaveIds = $this->preSaveSearchCriteriaAndResults(
            $tableName,
            $data,
            $requestData,
            $user['id']
        );

        return [
            'saveSearchCriteriaId' => $preSaveIds['saveSearchCriteriaId'],
            'saveSearchResultsId' => $preSaveIds['saveSearchResultsId'],
            'entities' => $data
        ];
    }

    /**
     * Returns saved searches filtered by users and models.
     *
     * @param  array  $users  users ids
     * @param  array  $models models names
     * @return Cake\ORM\ResultSet
     */
    public function getSavedSearches(array $users = [], array $models = [])
    {
        $conditions = [
            'SavedSearches.name IS NOT' => null
        ];

        if (!empty($users)) {
            $conditions['SavedSearches.user_id IN'] = $users;
        }

        if (!empty($models)) {
            $conditions['SavedSearches.model IN'] = $models;
        }

        $query = $this->find('all', [
            'conditions' => $conditions
        ]);

        return $query->toArray();
    }

    /**
     * Return Table's searchable fields.
     *
     * @param  \Cake\ORM\Table|string $table Table object or name.
     * @return array
     */
    public function getSearchableFields($table)
    {
        if (!empty($this->_searchableFields)) {
            return $this->_searchableFields;
        }

        // get Table instance
        if (is_string($table)) {
            $table = TableRegistry::get($table);
        }

        $event = new Event('Search.Model.Search.searchabeFields', $this, [
            'table' => $table
        ]);
        $this->eventManager()->dispatch($event);

        if (empty($event->result)) {
            throw new RuntimeException('Table [' . $table->registryAlias() . '] has no searchable fields defined.');
        }

        $this->_searchableFields = $event->result;

        return $this->_searchableFields;
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
     * Prepare basic search query's where statement
     *
     * @param  array                  $data  search fields
     * @param  \Cake\ORM\Table|string $table Table object or name
     * @return array
     */
    public function getSearchCriteria(array $data, $table)
    {
        $result = [];
        if (empty($data['query'])) {
            return $result;
        }

        if (is_string($table)) {
            $table = TableRegistry::get($table);
        }

        $displayField = $table->displayField();

        $fields = $this->getSearchableFields($table);
        if (empty($fields)) {
            return $result;
        }

        // if display field is not a virtual field, use that for basic search
        if (in_array($displayField, $table->schema()->columns())) {
            $result[$displayField][] = [
                'type' => $fields[$displayField]['type'],
                'operator' => key($fields[$displayField]['operators']),
                'value' => $data['query']
            ];
        } else {
            foreach ($fields as $field => $properties) {
                if (!in_array($properties['type'], $this->_basicSearchFieldTypes)) {
                    continue;
                }

                $result[$field][] = [
                    'type' => $properties['type'],
                    'operator' => key($fields[$displayField]['operators']),
                    'value' => $data['query']
                ];
            }
        }

        return $result;
    }

    /**
     * Method that fetches the search results.
     *
     * @param  array $data search data
     * @param  string $tableName table name
     * @return \Cake\ORM\ResultSet
     */
    protected function _getResults(array $data, $tableName)
    {
        $table = TableRegistry::get($tableName);

        $query = $table
            ->find('all')
            ->select($this->_getQueryFields($data, $table))
            ->where($this->_prepareWhereStatement($data, $tableName))
            ->order([$data['sort_by_field'] => $data['sort_by_order']]);

        // set limit if not 0
        if (0 < (int)$data['limit']) {
            $query->limit($data['limit']);
        }

        $result = $query->all();

        $event = new Event('Search.Model.Search.afterFind', $this, [
            'entities' => $result,
            'table' => $table
        ]);
        $this->eventManager()->dispatch($event);

        return $result;
    }

    /**
     * Prepare search query's where statement
     *
     * @param  array  $data  request data
     * @param  string $model model name
     * @return array
     */
    protected function _prepareWhereStatement(array $data, $model)
    {
        $result = [];

        if (empty($data['criteria'])) {
            return $result;
        }

        foreach ($data['criteria'] as $fieldName => $criterias) {
            if (empty($criterias)) {
                continue;
            }

            foreach ($criterias as $criteria) {
                $type = $criteria['type'];
                $value = $criteria['value'];
                if ('' === trim($value)) {
                    continue;
                }
                $operator = $criteria['operator'];
                if (isset($this->_searchableFields[$fieldName]['operators'][$operator]['pattern'])) {
                    $value = str_replace(
                        '{{value}}',
                        $value,
                        $this->_searchableFields[$fieldName]['operators'][$operator]['pattern']
                    );
                }
                $sqlOperator = $this->_searchableFields[$fieldName]['operators'][$operator]['operator'];
                list(, $prefix) = pluginSplit($model);
                $key = $prefix . '.' . $fieldName . ' ' . $sqlOperator;

                if (!array_key_exists($key, $result)) {
                    $result[$key] = $value;
                } else {
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
                        case 'email':
                            if (is_array($result[$key])) {
                                array_push($result[$key]['OR'], $value);
                            } else {
                                $result[$key] = ['OR' => [$result[$key], $value]];
                            }
                            break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get fields for Query's select statement.
     *
     * @param  array $data request data
     * @param  \Cake\ORM\Table $table Table instance
     * @return array
     */
    protected function _getQueryFields(array $data, Table $table)
    {
        $result = [];
        if (empty($data['display_columns'])) {
            return $result;
        }

        $result = $data['display_columns'];

        if (!is_array($result)) {
            $result = (array)$result;
        }

        $primaryKey = $table->primaryKey();

        if (!in_array($primaryKey, $result)) {
            array_unshift($result, $primaryKey);
        }

        return $result;
    }

    /**
     * Method that pre-saves search criteria and results and returns saved records ids.
     *
     * @param  string $model  model name
     * @param  array  $results search results
     * @param  array  $data   request data
     * @param  string $userId user id
     * @return array
     */
    public function preSaveSearchCriteriaAndResults($model, array $results, $data, $userId)
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
        $result['saveSearchResultsId'] = $this->_preSaveSearchResults($model, $results, $userId);

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

        // save search criteria
        $this->save($search);

        return $search->id;
    }

    /**
     * Pre-save search results and return record id.
     *
     * @param  string $model  model name
     * @param  array  $results search results
     * @param  string $userId user id
     * @return string
     */
    protected function _preSaveSearchResults($model, array $results, $userId)
    {
        $search = $this->newEntity();
        $search->type = $this->getResultType();
        $search->user_id = $userId;
        $search->model = $model;
        $search->shared = $this->getPrivateSharedStatus();
        $search->content = json_encode($results);

        // save search results
        $this->save($search);

        return $search->id;
    }
}
