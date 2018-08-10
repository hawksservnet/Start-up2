<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * NurseryReserve Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenDate $reserve_date
 * @property string $reserve_time_start
 * @property string $reserve_time_end
 * @property int $user_id
 * @property int $purpose
 * @property int $status
 * @property int $approval
 * @property string $phone
 * @property string $mailaddress
 * @property string $name1
 * @property string $name_k1
 * @property int $age_year1
 * @property int $age_month1
 * @property int $sex1
 * @property string $name2
 * @property string $name_k2
 * @property int $age_year2
 * @property int $age_month2
 * @property int $sex2
 * @property string $remarks
 * @property int $created_id
 * @property \Cake\I18n\FrozenTime $created_date
 * @property int $modified_id
 * @property \Cake\I18n\FrozenTime $modified_date
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Created $created
 * @property \App\Model\Entity\Modified $modified
 */
class NurseryReserve extends Entity
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
