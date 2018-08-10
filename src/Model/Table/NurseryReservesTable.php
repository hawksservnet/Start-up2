<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * NurseryReserves Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CreatedsTable|\Cake\ORM\Association\BelongsTo $Createds
 * @property \App\Model\Table\ModifiedsTable|\Cake\ORM\Association\BelongsTo $Modifieds
 *
 * @method \App\Model\Entity\NurseryReserve get($primaryKey, $options = [])
 * @method \App\Model\Entity\NurseryReserve newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\NurseryReserve[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\NurseryReserve|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\NurseryReserve patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\NurseryReserve[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\NurseryReserve findOrCreate($search, callable $callback = null, $options = [])
 */
class NurseryReservesTable extends Table
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

        $this->setTable('nursery_reserves');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
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
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->date('reserve_date')
            ->requirePresence('reserve_date', 'create')
            ->notEmpty('reserve_date');

        $validator
            ->requirePresence('reserve_time_start', 'create')
            ->notEmpty('reserve_time_start');

        $validator
            ->requirePresence('reserve_time_end', 'create')
            ->notEmpty('reserve_time_end');

        $validator
            ->integer('purpose')
            ->requirePresence('purpose', 'create')
            ->notEmpty('purpose');

        $validator
            ->integer('status')
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        $validator
            ->integer('approval')
            ->requirePresence('approval', 'create')
            ->notEmpty('approval');

        $validator
            ->requirePresence('phone', 'create')
            ->notEmpty('phone');
        $validator
            ->allowEmpty('name1');

        $validator
            ->allowEmpty('name_k1');

        $validator
            ->integer('age_year1')
            ->requirePresence('age_year1', 'create')
            ->notEmpty('age_year1');

        $validator
            ->integer('age_month1')
            ->requirePresence('age_month1', 'create')
            ->notEmpty('age_month1');

        $validator
            ->integer('sex1')
            ->requirePresence('sex1', 'create')
            ->notEmpty('sex1');

        $validator
            ->allowEmpty('name2');

        $validator
            ->allowEmpty('name_k2');

        $validator
            ->integer('age_year2')
            ->allowEmpty('age_year2');

        $validator
            ->integer('age_month2')
            ->allowEmpty('age_month2');

        $validator
            ->integer('sex2')
            ->allowEmpty('sex2');

        $validator
            ->allowEmpty('approval_text');

        $validator
            ->allowEmpty('remarks');

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
        $addMoreRule = function ($nursery) {
            return isset($nursery->reserve_date) &&
                isset($nursery->reserve_time_start) &&
                isset($nursery->reserve_time_end) &&
                isset($nursery->user_id) &&
                isset($nursery->purpose) &&
                isset($nursery->status) &&
                isset($nursery->approval) &&
                isset($nursery->phone) &&
                isset($nursery->age_year1) &&
                isset($nursery->age_month1) &&
                isset($nursery->sex1) &&
                mb_strlen($nursery->reserve_time_start) <= 4 &&
                mb_strlen($nursery->reserve_time_end) <= 4 &&
                mb_strlen($nursery->phone) <= 20 &&
                mb_strlen($nursery->mailaddress) <= 100 &&
                (empty($nursery->name1) || mb_strlen($nursery->name1) <= 50) &&
                (empty($nursery->name_k1) || mb_strlen($nursery->name_k1) <= 50) &&
                (empty($nursery->name2) || mb_strlen($nursery->name2) <= 50) &&
                (empty($nursery->name_k2) || mb_strlen($nursery->name_k2) <= 50);
        };
        $rules->add($addMoreRule);

        return $rules;
    }
}
