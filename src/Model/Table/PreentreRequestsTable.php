<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PreentreRequests Model
 *
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\PreentreRequest get($primaryKey, $options = [])
 * @method \App\Model\Entity\PreentreRequest newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\PreentreRequest[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PreentreRequest|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\PreentreRequest patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\PreentreRequest[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\PreentreRequest findOrCreate($search, callable $callback = null, $options = [])
 */
class PreentreRequestsTable extends Table
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

        $this->setTable('preentre_requests');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

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
            ->integer('status')
            ->allowEmpty('status');

        $validator
            ->integer('intention')
            ->allowEmpty('intention');

        $validator
            ->integer('business_type')
            ->allowEmpty('business_type');

        $validator
            ->allowEmpty('business_type_text');

        $validator
            ->integer('business_style')
            ->allowEmpty('business_style');

        $validator
            ->allowEmpty('business_style_text');

        $validator
            ->integer('problem01')
            ->allowEmpty('problem01');

        $validator
            ->integer('problem02')
            ->allowEmpty('problem02');

        $validator
            ->integer('problem03')
            ->allowEmpty('problem03');

        $validator
            ->integer('problem04')
            ->allowEmpty('problem04');

        $validator
            ->integer('problem05')
            ->allowEmpty('problem05');

        $validator
            ->integer('problem06')
            ->allowEmpty('problem06');

        $validator
            ->integer('problem07')
            ->allowEmpty('problem07');

        $validator
            ->integer('problem08')
            ->allowEmpty('problem08');

        $validator
            ->integer('problem09')
            ->allowEmpty('problem09');

        $validator
            ->integer('problem10')
            ->allowEmpty('problem10');

        $validator
            ->integer('problem11')
            ->allowEmpty('problem11');

        $validator
            ->integer('problem12')
            ->allowEmpty('problem12');

        $validator
            ->integer('problem13')
            ->allowEmpty('problem13');

        $validator
            ->integer('problem14')
            ->allowEmpty('problem14');

        $validator
            ->integer('problem15')
            ->allowEmpty('problem15');

        $validator
            ->integer('problem16')
            ->allowEmpty('problem16');

        $validator
            ->integer('problem17')
            ->allowEmpty('problem17');

        $validator
            ->integer('problem18')
            ->allowEmpty('problem18');

        $validator
            ->integer('problem19')
            ->allowEmpty('problem19');

        $validator
            ->integer('problem20')
            ->allowEmpty('problem20');

        $validator
            ->integer('problem21')
            ->allowEmpty('problem21');

        $validator
            ->integer('problem22')
            ->allowEmpty('problem22');

        $validator
            ->integer('problem23')
            ->allowEmpty('problem23');

        $validator
            ->integer('problem24')
            ->allowEmpty('problem24');

        $validator
            ->integer('problem25')
            ->allowEmpty('problem25');

        $validator
            ->integer('problem26')
            ->allowEmpty('problem26');

        $validator
            ->integer('problem27')
            ->allowEmpty('problem27');

        $validator
            ->integer('problem28')
            ->allowEmpty('problem28');

        $validator
            ->integer('problem29')
            ->allowEmpty('problem29');

        $validator
            ->integer('problem30')
            ->allowEmpty('problem30');

        $validator
            ->integer('problem31')
            ->allowEmpty('problem31');

        $validator
            ->integer('problem32')
            ->allowEmpty('problem32');

        $validator
            ->integer('problem99')
            ->requirePresence('problem99', 'create')
            ->notEmpty('problem99');

        $validator
            ->allowEmpty('problem_text');

        $validator
            ->integer('wish')
            ->allowEmpty('wish');

        $validator
            ->integer('created_at')
            ->allowEmpty('created_at');

        $validator
            ->integer('updated_at')
            ->allowEmpty('updated_at');

        $validator
            ->integer('wish01')
            ->allowEmpty('wish01');

        $validator
            ->integer('wish02')
            ->allowEmpty('wish02');

        $validator
            ->integer('wish03')
            ->allowEmpty('wish03');

        $validator
            ->integer('wish04')
            ->allowEmpty('wish04');

        $validator
            ->integer('wish05')
            ->allowEmpty('wish05');

        $validator
            ->integer('wish06')
            ->allowEmpty('wish06');

        $validator
            ->integer('wish07')
            ->allowEmpty('wish07');

        $validator
            ->integer('wish08')
            ->allowEmpty('wish08');

        $validator
            ->integer('wish09')
            ->allowEmpty('wish09');

        $validator
            ->integer('wish10')
            ->allowEmpty('wish10');

        $validator
            ->integer('wish11')
            ->allowEmpty('wish11');

        $validator
            ->integer('wish12')
            ->allowEmpty('wish12');

        $validator
            ->integer('wish13')
            ->allowEmpty('wish13');

        $validator
            ->integer('wish14')
            ->allowEmpty('wish14');

        $validator
            ->integer('wish15')
            ->allowEmpty('wish15');

        $validator
            ->integer('wish16')
            ->allowEmpty('wish16');

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
}
