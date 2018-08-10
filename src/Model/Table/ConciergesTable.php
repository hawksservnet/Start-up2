<?php
namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use \Cake\Validation\Validator;

/**
 * Concierges Model
 */
class ConciergesTable extends Table
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

        $this->setTable('concierges');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Acounts', [
            'foreignKey' => 'account_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('ConciergeInformations', [
            'foreignKey' => 'concierge_id'
        ]);
        $this->hasMany('ShiftWorks', [
            'foreignKey' => 'concierge_id'
        ]);
        $this->hasMany('Reserves', [
            'foreignKey' => 'concierge_id'
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
        $notEmptyMessage = Configure::read('MESSAGE_NOTIFICATION.MSG_004');
        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name', $notEmptyMessage)
            ->add('name', [
                'maxLength' => [
                    'rule' => ['maxLength', 50],
                    'message' => '氏名：50文字以内で入力してください'
                ]
            ]);
        $validator
            ->requirePresence('name_e', 'create')
            ->notEmpty('name_e', $notEmptyMessage)
            ->add('name_e', [
                'maxLength' => [
                    'rule' => ['maxLength', 50],
                    'message' => '氏名：50文字以内で入力してください'
                ]
            ]);
        $validator
            ->allowEmpty('image_name')
            ->add('image_name', [
                'maxLength' => [
                    'rule' => ['maxLength', 256],
                    'message' => '写真：256文字以内で入力してください'
                ]
            ]);

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
        $rules->add($rules->existsIn(['account_id'], 'Acounts'));
        $addMoreRule = function ($concierges) {
            return isset($concierges->name) &&
                isset($concierges->name_e) &&
                isset($concierges->career) &&
                isset($concierges->image_name) &&
                mb_strlen($concierges->name) <= 50 &&
                mb_strlen($concierges->name_e) <= 50 &&
                mb_strlen($concierges->image_name) <= 256;
        };
        $rules->add($addMoreRule);

        return $rules;
    }
}
