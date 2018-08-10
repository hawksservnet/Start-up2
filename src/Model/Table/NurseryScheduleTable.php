<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * NurserySchedule Model
 *
 * @property \App\Model\Table\CreatedsTable|\Cake\ORM\Association\BelongsTo $Createds
 * @property \App\Model\Table\ModifiedsTable|\Cake\ORM\Association\BelongsTo $Modifieds
 *
 * @method \App\Model\Entity\NurserySchedule get($primaryKey, $options = [])
 * @method \App\Model\Entity\NurserySchedule newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\NurserySchedule[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\NurserySchedule|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\NurserySchedule patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\NurserySchedule[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\NurserySchedule findOrCreate($search, callable $callback = null, $options = [])
 */
class NurseryScheduleTable extends Table
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

        $this->setTable('nursery_schedule');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsTo('Createds', [
            'foreignKey' => 'created_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Modifieds', [
            'foreignKey' => 'modified_id'
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
            ->dateTime('created_date')
            ->requirePresence('created_date', 'create')
            ->notEmpty('created_date');

        $validator
            ->dateTime('modified_date')
            ->allowEmpty('modified_date');

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
        return $rules;
    }
}
