<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * NurserySchedule Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenDate $reserve_date
 * @property int $created_id
 * @property \Cake\I18n\FrozenTime $created_date
 * @property int $modified_id
 * @property \Cake\I18n\FrozenTime $modified_date
 *
 * @property \App\Model\Entity\Created $created
 * @property \App\Model\Entity\Modified $modified
 */
class NurserySchedule extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
