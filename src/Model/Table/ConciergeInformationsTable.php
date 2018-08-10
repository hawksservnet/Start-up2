<?php
namespace App\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use \Cake\Validation\Validator;

/**
 * ConciergeInformations Model
 */
class ConciergeInformationsTable extends Table
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

        $this->setTable('concierge_informations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Concierges', [
            'foreignKey' => 'concierge_id',
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
        $notEmptyMessage = Configure::read('MESSAGE_NOTIFICATION.MSG_004');
        $validator
            ->requirePresence('info_text', 'create')
            ->notEmpty('info_text', $notEmptyMessage)
            ->add('info_text', [
                'maxLength' => [
                    'rule' => ['maxLength', 100],
                    'message' => 'キーワード、対応外国語：100文字以内で入力してください'
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
        $rules->add($rules->existsIn(['concierge_id'], 'Concierges'));
        $addMoreRule = function ($conciergeInformation) {
            return isset($conciergeInformation->info_type) &&
                isset($conciergeInformation->info_text) &&
                mb_strlen($conciergeInformation->info_text) <= 100;
        };
        $rules->add($addMoreRule);

        return $rules;
    }
}
