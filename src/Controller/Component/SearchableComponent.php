<?php
namespace Search\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Plugin;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use \FileSystemIterator;
use \RuntimeException;
use Search\Controller\Traits\SearchableTrait;

/**
 * Searchable component
 */
class SearchableComponent extends Component
{
    use SearchableTrait;

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
     * Returns saved searches filtered by users and models.
     *
     * @param  array  $users  users ids
     * @param  array  $models models names
     * @return Cake\ORM\ResultSet
     */
    public function getSavedSearches(array $users = [], array $models = [])
    {
        $savedSearches = TableRegistry::get('Search.SavedSearches');

        $conditions = [
            'SavedSearches.name IS NOT' => null
        ];

        if (!empty($users)) {
            $conditions['SavedSearches.user_id IN'] = $users;
        }

        if (!empty($models)) {
            $conditions['SavedSearches.model IN'] = $models;
        }

        $query = $savedSearches->find('all', [
            'conditions' => $conditions
        ]);

        return $query->toArray();
    }

    /**
     * Get all the tables from application and plugins.
     *
     * @return array
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
     *
     * @return array
     */
    public function getFieldTypeOperators()
    {
        return $this->_fieldTypeOperators;
    }
}
