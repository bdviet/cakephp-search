<?php
namespace Search\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use \FileSystemIterator;
use \RuntimeException;

/**
 * Searchable component
 */
class SearchableComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Per field type operators
     *
     * @var array
     */
    protected $_fieldTypeOperators = [
        'uuid' => ['is' => 'Is'],
        'boolean' => ['is' => 'Is', 'is_not' => 'Is not'],
        'list' => ['is' => 'Is', 'is_not' => 'Is not'],
        'string' => [
            'contains' => 'Contains',
            'not_contains' => 'Does not contain',
            'starts_with' => 'Starts with',
            'ends_with' => 'Ends with'
        ],
        'text' => [
            'contains' => 'Contains',
            'not_contains' => 'Does not contain',
            'starts_with' => 'Starts with',
            'ends_with' => 'Ends with'
        ],
        'textarea' => [
            'contains' => 'Contains',
            'not_contains' => 'Does not contain',
            'starts_with' => 'Starts with',
            'ends_with' => 'Ends with'
        ],
        'integer' => ['is' => 'Is', 'is_not' => 'Is not', 'greater' => 'Greater', 'less' => 'Less'],
        'datetime' => ['is' => 'Is', 'is_not' => 'Is not', 'greater' => 'Greater', 'less' => 'Less'],
        'date' => ['is' => 'Is', 'is_not' => 'Is not', 'greater' => 'Greater', 'less' => 'Less'],
        'time' => ['is' => 'Is', 'is_not' => 'Is not', 'greater' => 'Greater', 'less' => 'Less']
    ];

    /**
     * Basic search default fields
     * @var array
     */
    protected $_basicSearchDefaultFields = [
        'modified',
        'created'
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
     * This functions constructs the searachable tables and also append the fields which
     * can be searched.
     *
     * @return array All tables with their searchable columns.
     */
    public function getSearchableTablesFields()
    {
        $tables = $this->getSearchableTables();
        foreach ($tables as $container => &$containerTables) {
            foreach ($containerTables as &$table) {
                if ($table['searchable']) {
                    if ($container === 'app') {
                        $modelTable = TableRegistry::get($table['name']);
                    } else {
                        $modelTable = TableRegistry::get($container . '.' . $table['name']);
                    }

                    $table['fields'] = $this->getSearchableFields($modelTable);
                }
            }
        }

        return $tables;
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

        return $result;
    }

    /**
     * Get all the tables with Searchable fields functionallity.
     *
     * @return array
     */
    public function getSearchableTables()
    {
        $tables = $this->_getAllTables();
        foreach ($tables as $container => &$containerTables) {
            foreach ($containerTables as $key => &$table) {
                if ($container === 'app') {
                    $modelTable = TableRegistry::get($table['name']);
                } else {
                    $modelTable = TableRegistry::get($container . '.' . $table['name']);
                }
                $table['searchable'] = $this->isSearchable($modelTable);
            }
        }

        return array_filter($tables);
    }

    /**
     * Returns true if table is searchable, false otherwise.
     *
     * @param  \Cake\ORM\Table|string $table Table object or name.
     * @return bool
     */
    public function isSearchable($table)
    {
        $result = false;
        /*
        get Table instance
         */
        if (is_string($table)) {
            $table = TableRegistry::get($table);
        }

        /*
        check if is searchable
         */
        if (method_exists($table, 'isSearchable') && is_callable([$table, 'isSearchable'])) {
            $result = $table->isSearchable();
        }

        return $result;
    }

    /**
     * Get all the tables from application and plugins.
     *
     * @return [type]                [description]
     */
    protected function _getAllTables()
    {
        $result = [];
        $result['app'] = $this->_getTables(APP . 'Model' . DS . 'Table');
        $plugins = Plugin::loaded();
        foreach ($plugins as $plugin) {
            $result[$plugin] = $this->_getTables(Plugin::path($plugin) . 'src' . DS . 'Model' . DS . 'Table');
        }

        return array_filter($result);
    }

    /**
     * Just get the tables from the the given path.
     *
     * @throws RuntimeException When path is not provided.
     * @param  string $path The directory which contains the tables.
     * @return array  Either empty array or the found tables.
     */
    protected function _getTables($path = '')
    {
        $result = [];

        if (empty($path)) {
            throw new RuntimeException('Please provide path of Tables to proceed.');
        }

        if (!file_exists($path)) {
            return $result;
        }

        $it = new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
        foreach ($it as $file) {
            //Exclude anything without the table prefix.
            if (!strpos($file->getFilename(), 'Table.php')) {
                continue;
            }
            $table = $file->getBasename('Table.php');
            $result[$table]['name'] = $table;
        }

        return $result;
    }

    /**
     * Return list of operators grouped by field type
     * @return array
     */
    public function getFieldTypeOperators()
    {
        return $this->_fieldTypeOperators;
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
                    $result['OR'][$model . '.' . $field . ' LIKE'] = '%' . $data['query'] . '%';
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
                    $key = $model . '.' . $fieldName . ' ' . $sqlOperator;

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
}
