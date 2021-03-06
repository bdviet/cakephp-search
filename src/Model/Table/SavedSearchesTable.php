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
use InvalidArgumentException;
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
     * Default sql limit
     */
    const DEFAULT_LIMIT = 10;

    /**
     * Default sql order by direction
     */
    const DEFAULT_SORT_BY_ORDER = 'desc';

    /**
     * Default sql aggregator
     */
    const DEFAULT_AGGREGATOR = 'AND';

    /**
     * Search limit options.
     *
     * @var array
     */
    protected $_limitOptions = [
        0 => 'Unlimited',
        1 => 1,
        3 => 3,
        5 => 5,
        10 => 10,
        20 => 20,
        50 => 50,
        100 => 100
    ];

    /**
     * Search sort by order options.
     *
     * @var array
     */
    protected $_sortByOrderOptions = [
        'asc' => 'Ascending',
        'desc' => 'Descending'
    ];

    /**
     * Search aggregator options.
     *
     * @var array
     */
    protected $_aggregatorOptions = [
        'AND' => 'Match all filters',
        'OR' => 'Match any filter'
    ];

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
     * Fields used in basic search.
     *
     * @var array
     */
    protected $_basicSearchFields = [];

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
     * Getter method for default sql limit.
     *
     * @return string
     */
    public function getDefaultLimit()
    {
        return static::DEFAULT_LIMIT;
    }

    /**
     * Getter method for sql limit options.
     *
     * @return string
     */
    public function getLimitOptions()
    {
        return $this->_limitOptions;
    }

    /**
     * Getter method for default sql sort by order.
     *
     * @return string
     */
    public function getDefaultSortByOrder()
    {
        return static::DEFAULT_SORT_BY_ORDER;
    }

    /**
     * Getter method for sql sort by order options.
     *
     * @return string
     */
    public function getSortByOrderOptions()
    {
        return $this->_sortByOrderOptions;
    }

    /**
     * Getter method for default sql aggragator.
     *
     * @return string
     */
    public function getDefaultAggregator()
    {
        return static::DEFAULT_AGGREGATOR;
    }

    /**
     * Getter method for sql aggregator options.
     *
     * @return string
     */
    public function getAggregatorOptions()
    {
        return $this->_aggregatorOptions;
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
        $data = $requestData;

        $data = $this->validateData($tableName, $data);

        if (empty($data['result'])) {
            // get search results
            $data['result'] = $this->_getResults($data, $tableName);
        }

        // pre-save search criteria and results
        $preSaveIds = $this->_preSaveSearchCriteriaAndResults(
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
     * Default search options.
     *
     * @param string $tableName Table name
     * @return array
     */
    public function getDefaultOptions($tableName)
    {
        $result['display_columns'] = $this->getListingFields($tableName);
        $result['sort_by_field'] = current($result['display_columns']);
        $result['sort_by_order'] = $this->getDefaultSortByOrder();
        $result['limit'] = $this->getDefaultLimit();
        $result['aggregator'] = $this->getDefaultAggregator();

        return $result;
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
        // get Table instance
        $table = $this->_getTableInstance($table);

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
        // get Table instance
        $table = $this->_getTableInstance($table);

        if (method_exists($table, 'getListingFields') && is_callable([$table, 'getListingFields'])) {
            $result = $table->getListingFields();
        }

        if (empty($result)) {
            $result = $this->_getBasicSearchFields($table);
        }

        if (empty($result)) {
            $result[] = $table->primaryKey();
            $displayField = $table->displayField();
            // add display field to the result only if not a virtual field
            if (in_array($displayField, $table->schema()->columns())) {
                $result[] = $displayField;
            }
            foreach ($this->_basicSearchDefaultFields as $field) {
                if ($table->hasField($field)) {
                    $result[] = $field;
                }
            }
        }

        // skip display fields
        $result = array_diff((array)$result, $this->_skipDisplayFields);

        // reset numeric indexes
        $result = array_values($result);

        return $result;
    }

    /**
     * Prepare basic search query's where statement
     *
     * @param array $data search fields
     * @param \Cake\ORM\Table|string $table Table object or name
     * @param array $user User info
     * @return array
     */
    public function getBasicSearchCriteria(array $data, $table, $user)
    {
        $result = [];
        if (empty($data['query'])) {
            return $result;
        }

        // get Table instance
        $table = $this->_getTableInstance($table);

        $fields = $this->_getBasicSearchFields($table);
        if (empty($fields)) {
            return $result;
        }

        $searchableFields = $this->getSearchableFields($table);
        if (empty($searchableFields)) {
            return $result;
        }

        foreach ($fields as $field) {
            if (!array_key_exists($field, $searchableFields)) {
                continue;
            }

            $type = $searchableFields[$field]['type'];
            $operator = key($searchableFields[$field]['operators']);
            $value = $data['query'];

            if ('related' === $type) {
                $value = $this->_getRelatedModuleValues($searchableFields[$field]['source'], $data, $user);
            }

            $value = (array)$value;

            if (empty($value)) {
                continue;
            }

            foreach ($value as $val) {
                $result[$field][] = [
                    'type' => $type,
                    'operator' => $operator,
                    'value' => $val
                ];
            }
        }

        return $result;
    }

    /**
     * Gets basic search values from Related module.
     *
     * This method is useful when you do a basic search on a related field,
     * in which the values are always uuid's. What this method will do is
     * run a basic search in the related module (recursively) to fetch and
     * return the entities IDs matching the search string.
     *
     * @param string $module Related module name
     * @param array $data Search string
     * @param array $user User info
     * @return array
     */
    protected function _getRelatedModuleValues($module, $data, $user)
    {
        $result = [];
        if (!is_string($module) || empty($module) || empty($data) || empty($user)) {
            return $result;
        }

        $data = [
            'aggregator' => 'OR',
            'criteria' => $this->getBasicSearchCriteria($data, $module, $user)
        ];

        $search = $this->search(
            $module,
            $user,
            $data
        );

        foreach ($search['entities']['result'] as $entity) {
            $result[] = $entity->id;
        }

        return $result;
    }

    /**
     * Method that broadcasts an Event to generate the basic search fields.
     * If the Event result is empty then it falls back to using the display field.
     * If the display field is a virtual one then if falls back to searchable fields,
     * using the ones that their type matches the _basicSearchFieldTypes list.
     *
     * @param \Cake\ORM\Table $table Table instance
     * @return array
     */
    protected function _getBasicSearchFields(Table $table)
    {
        $event = new Event('Search.Model.Search.basicSearchFields', $this, [
            'table' => $table
        ]);
        $this->eventManager()->dispatch($event);

        $result = $event->result;

        if (empty($result)) {
            $result = $table->displayField();
        }

        $result = (array)$result;

        $columns = $table->schema()->columns();
        // remove non-existing database fields (virtual field for example)
        foreach ($result as $key => $field) {
            if (in_array($field, $columns)) {
                continue;
            }
            unset($result[$key]);
        }

        if (!empty($result)) {
            return $result;
        }

        $searchableFields = $this->getSearchableFields($table);
        if (empty($searchableFields)) {
            return $result;
        }

        foreach ($searchableFields as $field => $properties) {
            if (!in_array($properties['type'], $this->_basicSearchFieldTypes)) {
                continue;
            }

            $result[] = $field;
        }

        return $result;
    }

    /**
     * Instantiates and returns searchable Table instance.
     *
     * @param \Cake\ORM\Table|string $table Table name or Instance
     * @return \Cake\ORM\Table
     * @throws \InvalidArgumentException Thrown if table parameter is not a Table instance or string
     */
    protected function _getTableInstance($table)
    {
        if ($table instanceof Table) {
            return $table;
        }

        if (is_string($table)) {
            return TableRegistry::get($table);
        }

        throw new InvalidArgumentException(
            'Parameter $table must be a string or Cake\\ORM\\Table instance, ' . gettype($table) . ' provided.'
        );
    }

    /**
     * Base search data validation method.
     *
     * Retrieves current searchable table columns, validates and filters criteria, display columns
     * and sort by field against them. Then validates sort by order and limit againt available options
     * and sets them to the default options if they fail validation.
     *
     * @param \Cake\ORM\Table|string $table Table name or Instace
     * @param array $data Search data
     * @return array
     */
    public function validateData($table, array $data)
    {
        $table = $this->_getTableInstance($table);

        $fields = $this->getSearchableFields($table);
        $fields = array_keys($fields);

        // merge default options
        $data += $this->getDefaultOptions($table);

        if (!empty($data['criteria'])) {
            $data['criteria'] = $this->_validateCriteria($data['criteria'], $fields);
        }

        $data['display_columns'] = $this->_validateDisplayColumns($data['display_columns'], $fields);
        $data['sort_by_field'] = $this->_validateSortByField($data['sort_by_field'], $fields, $table);
        $data['sort_by_order'] = $this->_validateSortByOrder($data['sort_by_order'], $table);
        $data['limit'] = $this->_validateLimit($data['limit']);
        $data['aggregator'] = $this->_validateAggregator($data['aggregator']);

        return $data;
    }

    /**
     * Validate search criteria.
     *
     * @param array $data Criteria values
     * @param array $fields Searchable fields
     * @return array
     */
    protected function _validateCriteria(array $data, array $fields)
    {
        foreach ($data as $k => $v) {
            if (in_array($k, $fields)) {
                continue;
            }
            unset($data[$k]);
        }

        return $data;
    }

    /**
     * Validate search display field(s).
     *
     * @param array $data Display field(s) values
     * @param array $fields Searchable fields
     * @return array
     */
    protected function _validateDisplayColumns(array $data, array $fields)
    {
        foreach ($data as $k => $v) {
            if (in_array($v, $fields)) {
                continue;
            }
            unset($data[$k]);
        }

        return $data;
    }

    /**
     * Validate search sort by field.
     *
     * @param string $data Sort by field value
     * @param array $fields Searchable fields
     * @param \Cake\ORM\Table $table Table instance
     * @return string
     */
    protected function _validateSortByField($data, array $fields, Table $table)
    {
        if (!in_array($data, $fields)) {
            $data = $table->displayField();
        }

        return $data;
    }

    /**
     * Validate search sort by order.
     *
     * @param string $data Sort by order value
     * @param \Cake\ORM\Table $table Table instance
     * @return string
     */
    protected function _validateSortByOrder($data, Table $table)
    {
        $options = array_keys($this->getSortByOrderOptions());
        if (!in_array($data, $options)) {
            $data = $this->getDefaultSortByOrder();
        }

        return $data;
    }

    /**
     * Validate search limit.
     *
     * @param string $data Limit value
     * @return string
     */
    protected function _validateLimit($data)
    {
        $options = array_keys($this->getLimitOptions());
        if (!in_array($data, $options)) {
            $data = $this->getDefaultLimit();
        }

        return $data;
    }

    /**
     * Validate search aggregator.
     *
     * @param string $data Aggregator value
     * @return string
     */
    protected function _validateAggregator($data)
    {
        $options = array_keys($this->getAggregatorOptions());
        if (!in_array($data, $options)) {
            $data = $this->getDefaultAggregator();
        }

        return $data;
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
        $table = $this->_getTableInstance($tableName);

        $query = $table
            ->find('all')
            ->select($this->_getQueryFields($data, $table))
            ->where([$data['aggregator'] => $this->_prepareWhereStatement($data, $tableName)])
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

        $this->getSearchableFields($model);

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
                        case 'related':
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
    protected function _preSaveSearchCriteriaAndResults($model, array $results, $data, $userId)
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
