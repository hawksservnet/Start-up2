<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CounselNotes Model
 *
 * @property \App\Model\Table\ReservesTable|\Cake\ORM\Association\BelongsTo $Reserves
 * @property \App\Model\Table\CreatedsTable|\Cake\ORM\Association\BelongsTo $Createds
 * @property \App\Model\Table\ModifiedsTable|\Cake\ORM\Association\BelongsTo $Modifieds
 *
 * @method \App\Model\Entity\CounselNote get($primaryKey, $options = [])
 * @method \App\Model\Entity\CounselNote newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CounselNote[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CounselNote|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CounselNote patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CounselNote[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CounselNote findOrCreate($search, callable $callback = null, $options = [])
 */
class CounselNotesTable extends Table
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

        $this->setTable('counsel_notes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Reserves', [
            'foreignKey' => 'reserve_id',
            'joinType' => 'INNER'
        ]);
    }
}
