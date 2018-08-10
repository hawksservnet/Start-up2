<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ShiftWork Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenDate $work_date
 * @property string $work_time_start
 * @property string $work_time_end
 * @property int $concierge_id
 * @property int $status
 * @property int $created_id
 * @property \Cake\I18n\FrozenTime $created_date
 * @property int $modified_id
 * @property \Cake\I18n\FrozenTime $modified_date
 */
class ShiftWork extends Entity
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
