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
     * This functions constructs the searachable tables and also append the fields which
     * can be searched.
     *
     * @return array All tables with their searchable columns.
     */
    public function getSearchableTablesFields()
    {
        $db = ConnectionManager::get('default');
        $collection = $db->schemaCollection();
        $dbTables = $collection->listTables();
        $tables = $this->getSearchableTables();
        foreach ($tables as $container => &$containerTables) {
            foreach ($containerTables as $tableName => &$table) {
                if (in_array($tableName, $dbTables) && $table['searchable']) {
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

        if (is_callable([$table, 'getSearchableFields'])) {
            $result = $table->getSearchableFields();
        } else {
            //By defeault, all schema fields can be searched.
            $result = $collection->describe($table->alias())->columns();
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
                if (method_exists($modelTable, 'isSearchable')) {
                    $table['searchable'] = $modelTable->isSearchable();
                } else {
                    //By default, table is not searchable.
                    $table['searchable'] = false;
                }
            }
        }

        return array_filter($tables);
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
}
