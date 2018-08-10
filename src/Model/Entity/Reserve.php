<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Reserve Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenDate $work_date
 * @property string $work_time_start
 * @property string $work_time_end
 * @property int $concierge_id
 * @property int $user_id
 * @property int $reserve_status
 * @property int $cancel_status
 * @property int $created_id
 * @property \Cake\I18n\FrozenTime $created_date
 * @property int $modified_id
 * @property \Cake\I18n\FrozenTime $modified_date
 *
 * @property \App\Model\Entity\Concierge $concierge
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Created $created
 * @property \App\Model\Entity\Modified $modified
 * @property \App\Model\Entity\CounselNote[] $counsel_notes
 */
class Reserve extends Entity
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
