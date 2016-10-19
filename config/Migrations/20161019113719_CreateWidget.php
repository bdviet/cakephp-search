<?php
use Migrations\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateWidget extends AbstractMigration
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
        $table = $this->table('widgets');

        $table->addColumn('id', 'uuid', [
            'limit' => 36,
            'null' => false
        ]);

        $table->addColumn('dashboard_id','string', [
            'default' => null,
            'limit' => 36,
            'null' => false,
        ])->addIndex('dashboard_id');

        $table->addColumn('widget_id', 'string', [
            'default' => null,
            'limit' => 36,
            'null' => false,
        ])->addIndex('widget_id');

        $table->addColumn('widget_type', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false
        ])->addIndex('widget_type');

		$table->addColumn('widget_options', 'text', [
			'default' => null,
            'limit' => MysqlAdapter::TEXT_LONG,
            'null' => false,
		]);

        $table->addColumn('column', 'integer', [
            'limit' => 11,
            'null' => false,
        ]);

        $table->addColumn('row', 'integer', [
            'limit' => 11,
            'null' => false
        ]);

	   	$table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);

		$table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);

		$table->addColumn('trashed', 'datetime', [
            'default' => null,
            'null' => false,
        ]);

		$table->addPrimaryKey([
            'id',
        ]);
        $table->create();
    }
}
