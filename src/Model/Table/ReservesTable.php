<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Reserves Model
 *
 * @property \App\Model\Table\ConciergesTable|\Cake\ORM\Association\BelongsTo $Concierges
 * @property \App\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CreatedsTable|\Cake\ORM\Association\BelongsTo $Createds
 * @property \App\Model\Table\ModifiedsTable|\Cake\ORM\Association\BelongsTo $Modifieds
 * @property \App\Model\Table\CounselNotesTable|\Cake\ORM\Association\HasMany $CounselNotes
 *
 * @method \App\Model\Entity\Reserve get($primaryKey, $options = [])
 * @method \App\Model\Entity\Reserve newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Reserve[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Reserve|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Reserve patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Reserve[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Reserve findOrCreate($search, callable $callback = null, $options = [])
 */
class ReservesTable extends Table
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

        $this->setTable('reserves');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Concierges', [
            'foreignKey' => 'concierge_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->hasOne('CsvImportInfos', [
            'foreignKey' => 'reserve_id'
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
        $rules->add($rules->isUnique(['work_date', 'work_time_start', 'concierge_id', 'user_id', 'reserve_status', 'cancel_status', 'cancel_date']));

        return $rules;
    }
}
