<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property |\Cake\ORM\Association\HasMany $EventRequests
 * @property |\Cake\ORM\Association\HasMany $Events
 * @property |\Cake\ORM\Association\HasMany $InterviewAnswers
 * @property |\Cake\ORM\Association\HasMany $PreentreRequests
 * @property \App\Model\Table\ReservesTable|\Cake\ORM\Association\HasMany $Reserves
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 */
class UsersTable extends Table
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

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('EventRequests', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Events', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('InterviewAnswers', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('PreentreRequests', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Reserves', [
            'foreignKey' => 'user_id'
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
            ->requirePresence('name_last', 'create')
            ->notEmpty('name_last');

        $validator
            ->requirePresence('name_first', 'create')
            ->notEmpty('name_first');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password');

        $validator
            ->allowEmpty('hiragana_name_last');

        $validator
            ->allowEmpty('hiragana_name_first');

        $validator
            ->allowEmpty('tel');

        $validator
            ->date('birthday')
            ->allowEmpty('birthday');

        $validator
            ->integer('sex')
            ->allowEmpty('sex');

        $validator
            ->allowEmpty('zip');

        $validator
            ->integer('pref')
            ->allowEmpty('pref');

        $validator
            ->allowEmpty('city');

        $validator
            ->allowEmpty('address');

        $validator
            ->allowEmpty('building');

        $validator
            ->allowEmpty('organization');

        $validator
            ->allowEmpty('position');

        $validator
            ->allowEmpty('job');

        $validator
            ->integer('interest')
            ->allowEmpty('interest');

        $validator
            ->integer('preparation')
            ->allowEmpty('preparation');

        $validator
            ->integer('mailmagazine')
            ->allowEmpty('mailmagazine');

        $validator
            ->integer('mailmagazine_info')
            ->allowEmpty('mailmagazine_info');

        $validator
            ->integer('role01')
            ->allowEmpty('role01');

        $validator
            ->integer('role02')
            ->allowEmpty('role02');

        $validator
            ->integer('role03')
            ->allowEmpty('role03');

        $validator
            ->integer('role04')
            ->allowEmpty('role04');

        $validator
            ->integer('role05')
            ->allowEmpty('role05');

        $validator
            ->integer('role06')
            ->allowEmpty('role06');

        $validator
            ->integer('role07')
            ->allowEmpty('role07');

        $validator
            ->integer('role08')
            ->allowEmpty('role08');

        $validator
            ->integer('role09')
            ->allowEmpty('role09');

        $validator
            ->integer('role10')
            ->allowEmpty('role10');

        $validator
            ->integer('role11')
            ->allowEmpty('role11');

        $validator
            ->integer('role12')
            ->allowEmpty('role12');

        $validator
            ->allowEmpty('event');

        $validator
            ->allowEmpty('matching');

        $validator
            ->date('entrepreneur_date')
            ->allowEmpty('entrepreneur_date');

        $validator
            ->allowEmpty('business_type');

        $validator
            ->allowEmpty('industry');

        $validator
            ->integer('deleted_at')
            ->allowEmpty('deleted_at');

        $validator
            ->integer('created_at')
            ->allowEmpty('created_at');

        $validator
            ->integer('updated_at')
            ->allowEmpty('updated_at');

        $validator
            ->integer('group')
            ->allowEmpty('group');

        $validator
            ->allowEmpty('username');

        $validator
            ->integer('last_login')
            ->allowEmpty('last_login');

        $validator
            ->allowEmpty('login_hash');

        $validator
            ->allowEmpty('profile_fields');

        $validator
            ->integer('type')
            ->allowEmpty('type');

        $validator
            ->allowEmpty('nationality');

        $validator
            ->allowEmpty('cardid');

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
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->isUnique(['username']));

        return $rules;
    }
}
