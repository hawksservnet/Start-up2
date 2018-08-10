<?php
namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use \Cake\Validation\Validator;

/**
 * Acounts Model
 */
class AcountsTable extends Table
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

        $this->setTable('acounts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
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
            ->requirePresence('login_id', 'create')
            ->notEmpty('login_id', $notEmptyMessage)
            ->add('login_id', [
                'maxLength' => [
                    'rule' => ['maxLength', 16],
                    'message' => 'ログインID：半角英数字16文字以下'
                ]
            ]);
        $validator
            ->add('account_name', [
                'maxLength' => [
                    'rule' => ['maxLength', 50],
                    'message' => '名称：50文字以下'
                ]
            ]);
        $passwordLengthMessage = Configure::read('MESSAGE_NOTIFICATION.MSG_020');
        $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password', $passwordLengthMessage)
            ->add('password', [
                'minLength' => [
                    'rule' => ['minLength', 8],
                    'last' => true,
                    'message' => $passwordLengthMessage
                ],
                'maxLength' => [
                    'rule' => ['maxLength', 20],
                    'message' => $passwordLengthMessage
                ]
            ]);
        $validator
            ->allowEmpty('account_name');

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
        $addMoreRule = function ($acounts) {
            return isset($acounts->login_id) &&
                isset($acounts->password) &&
                isset($acounts->account_name) &&
                mb_strlen($acounts->login_id) <= 16 &&
                mb_strlen($acounts->password) <= 50 &&
                mb_strlen($acounts->account_name) <= 50;
        };
        $rules->add($addMoreRule);

        return $rules;
    }
}
