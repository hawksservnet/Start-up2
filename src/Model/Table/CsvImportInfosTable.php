<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CounselNotes Model
 *
 */
class CsvImportInfosTable extends Table
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

        $this->setTable('csv_import_infos');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Reserves', [
            'foreignKey' => 'reserve_id',
            'joinType' => 'INNER'
        ]);
    }
}
