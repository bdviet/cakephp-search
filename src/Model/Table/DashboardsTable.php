<?php
namespace Search\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Search\Model\Entity\Dashboard;

/**
 * Dashboards Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Roles
 * @property \Cake\ORM\Association\BelongsToMany $SavedSearches
 */
class DashboardsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('dashboards');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'className' => 'Search.Roles'
        ]);
        $this->belongsToMany('SavedSearches', [
            'foreignKey' => 'dashboard_id',
            'targetForeignKey' => 'saved_search_id',
            'joinTable' => 'dashboards_saved_searches',
            'className' => 'Search.SavedSearches'
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
            ->notEmpty('name');

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
        $rules->add($rules->existsIn(['role_id'], 'Roles'));

        return $rules;
    }

    /**
     * Prepare saved searches to be passed to the View.
     *
     * @param  array  $savedSearches Dashboard's saved searches
     * @param  array  $user          User
     * @return array
     */
    public function prepareSavedSearches(array $savedSearches, array $user)
    {
        $result = [];

        foreach ($savedSearches as $savedSearch) {
            switch ($savedSearch->type) {
                case $this->SavedSearches->getCriteriaType():
                    $search = $this->SavedSearches->search(
                        $savedSearch->model,
                        $user,
                        json_decode($savedSearch->content, true),
                        true
                    );
                    $search['entities'] = $search['entities'];
                    break;

                case $this->SavedSearches->getResultType():
                    $search = $this->SavedSearches->get($savedSearch->id);
                    $search['entities'] = json_decode($search->content, true);
                    break;
            }

            /*
             * filter out skipped display fields
             */
            $search['entities']['display_columns'] = array_diff(
                $search['entities']['display_columns'],
                $this->SavedSearches->getSkippedDisplayFields()
            );

            $result[] = [
                'search_name' => $savedSearch->name,
                'model' => $savedSearch->model,
                'entities' => $search['entities'],
                'row' => $savedSearch->_joinData->row,
                'column' => $savedSearch->_joinData->column
            ];
        }

        return $result;
    }

    /**
     * Prepare associated saved searches data to be stored in the joined DB table.
     *
     * @param  array $savedSearches post request saved searches related data
     * @return array
     */
    public function prepareToSaveSavedSearches($savedSearches)
    {
        $result = [];
        foreach ($savedSearches['_ids'] as $k => $id) {
            $result[] = ['id' => $id,
                '_joinData' => [
                    'row' => $savedSearches['_rows'][$k],
                    'column' => $savedSearches['_columns'][$k]
                ]
            ];
        }

        return $result;
    }

    /**
     * Get specified user accessible dashboards.
     *
     * @param  array $user user details
     * @return \Cake\ORM\Query
     */
    public function getUserDashboards($user)
    {
        $groupsTable = TableRegistry::get('Groups.Groups');
        $capsTable = TableRegistry::get('RolesCapabilities.Capabilities');

        $query = $this->find('all')->order('name');

        if (!$user['is_superuser']) {
            $userGroups = $groupsTable->getUserGroups($user['id']);

            $userRoles = [];
            if (!empty($userGroups)) {
                $userRoles = $capsTable->getGroupsRoles($userGroups);
            }

            $query = $query->where([
                'OR' => [
                    'Dashboards.role_id IN' => array_keys($userRoles),
                    'Dashboards.role_id IS NULL'
                ]
            ]);
        }

        return $query;
    }
}
