<?php
namespace Search\Controller\Traits;

use Cake\ORM\TableRegistry;

trait SearchableTrait
{
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
}
