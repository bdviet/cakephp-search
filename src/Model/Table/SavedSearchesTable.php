<?php
namespace Search\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
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
     * Method that deletes old pre-save search records.
     *
     * @return void
     */
    public function deleteOldPreSavedSearches()
    {
        $this->deleteAll([
            'modified <' => new \DateTime(static::DELETE_OLDER_THAN),
            'name IS' => null
        ]);
    }
}
