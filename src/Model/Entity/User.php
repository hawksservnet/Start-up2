<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $name_last
 * @property string $name_first
 * @property string $email
 * @property string $password
 * @property string $hiragana_name_last
 * @property string $hiragana_name_first
 * @property string $tel
 * @property \Cake\I18n\FrozenDate $birthday
 * @property int $sex
 * @property string $zip
 * @property int $pref
 * @property string $city
 * @property string $address
 * @property string $building
 * @property string $organization
 * @property string $position
 * @property string $job
 * @property int $interest
 * @property int $preparation
 * @property int $mailmagazine
 * @property int $mailmagazine_info
 * @property int $role01
 * @property int $role02
 * @property int $role03
 * @property int $role04
 * @property int $role05
 * @property int $role06
 * @property int $role07
 * @property int $role08
 * @property int $role09
 * @property int $role10
 * @property int $role11
 * @property int $role12
 * @property string $event
 * @property string $matching
 * @property \Cake\I18n\FrozenDate $entrepreneur_date
 * @property string $business_type
 * @property string $industry
 * @property int $deleted_at
 * @property int $created_at
 * @property int $updated_at
 * @property int $group
 * @property string $username
 * @property int $last_login
 * @property string $login_hash
 * @property string $profile_fields
 * @property int $type
 * @property string $nationality
 * @property string $cardid
 *
 * @property \App\Model\Entity\Reserve[] $reserves
 */
class User extends Entity
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

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];
}
