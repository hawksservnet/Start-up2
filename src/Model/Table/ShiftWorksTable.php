<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ShiftWorks Model
 *
 * @property |\Cake\ORM\Association\BelongsTo $Concierges
 * @property |\Cake\ORM\Association\BelongsTo $Createds
 * @property |\Cake\ORM\Association\BelongsTo $Modifieds
 *
 * @method \App\Model\Entity\ShiftWork get($primaryKey, $options = [])
 * @method \App\Model\Entity\ShiftWork newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ShiftWork[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ShiftWork|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ShiftWork patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ShiftWork[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ShiftWork findOrCreate($search, callable $callback = null, $options = [])
 */
class ShiftWorksTable extends Table
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

        $this->setTable('shift_works');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Concierges', [
            'foreignKey' => 'concierge_id',
            'joinType' => 'INNER'
        ]);
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
        $rules->add($rules->existsIn(['concierge_id'], 'Concierges'));
        $rules->add($rules->isUnique(['work_date', 'work_time_start', 'concierge_id']));

        return $rules;
    }
}
