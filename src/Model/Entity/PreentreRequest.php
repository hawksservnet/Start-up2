<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PreentreRequest Entity
 *
 * @property int $id
 * @property int $user_id
 * @property int $status
 * @property int $intention
 * @property int $business_type
 * @property string $business_type_text
 * @property int $business_style
 * @property string $business_style_text
 * @property int $problem01
 * @property int $problem02
 * @property int $problem03
 * @property int $problem04
 * @property int $problem05
 * @property int $problem06
 * @property int $problem07
 * @property int $problem08
 * @property int $problem09
 * @property int $problem10
 * @property int $problem11
 * @property int $problem12
 * @property int $problem13
 * @property int $problem14
 * @property int $problem15
 * @property int $problem16
 * @property int $problem17
 * @property int $problem18
 * @property int $problem19
 * @property int $problem20
 * @property int $problem21
 * @property int $problem22
 * @property int $problem23
 * @property int $problem24
 * @property int $problem25
 * @property int $problem26
 * @property int $problem27
 * @property int $problem28
 * @property int $problem29
 * @property int $problem30
 * @property int $problem31
 * @property int $problem32
 * @property int $problem99
 * @property string $problem_text
 * @property int $wish
 * @property int $created_at
 * @property int $updated_at
 * @property int $wish01
 * @property int $wish02
 * @property int $wish03
 * @property int $wish04
 * @property int $wish05
 * @property int $wish06
 * @property int $wish07
 * @property int $wish08
 * @property int $wish09
 * @property int $wish10
 * @property int $wish11
 * @property int $wish12
 * @property int $wish13
 * @property int $wish14
 * @property int $wish15
 * @property int $wish16
 *
 * @property \App\Model\Entity\User $user
 */
class PreentreRequest extends Entity
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
