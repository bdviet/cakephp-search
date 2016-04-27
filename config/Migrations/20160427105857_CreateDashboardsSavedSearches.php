<?php
use Migrations\AbstractMigration;

class CreateDashboardsSavedSearches extends AbstractMigration
{

    public $autoId = false;

    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('dashboards_saved_searches');
        $table->addColumn('id', 'uuid', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('dashboard_id', 'string', [
            'default' => null,
            'limit' => 36,
            'null' => false,
        ]);
        $table->addColumn('saved_search_id', 'string', [
            'default' => null,
            'limit' => 36,
            'null' => false,
        ]);
        $table->addColumn('column', 'integer', [
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('row', 'integer', [
            'limit' => 11,
            'null' => false,
        ]);
        $table->addPrimaryKey([
            'id',
        ]);
        $table->create();
    }
}
