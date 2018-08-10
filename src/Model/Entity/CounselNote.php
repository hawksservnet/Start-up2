<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CounselNote Entity
 *
 * @property int $id
 * @property int $reserve_id
 * @property string $work_time_start
 * @property string $work_time_end
 * @property string $achieve
 * @property string $achieve_text
 * @property string $question1_1
 * @property string $question1_1text
 * @property string $question2_1
 * @property string $question2_1text
 * @property string $question3_1
 * @property string $question3_1text
 * @property string $question3_2
 * @property string $question3_2text
 * @property string $question4_1
 * @property string $question4_1text
 * @property string $question5_1
 * @property string $question5_1text
 * @property string $question6_1
 * @property string $question6_1text
 * @property string $question7_1
 * @property string $question7_1text
 * @property string $question8_1
 * @property string $question8_1text
 * @property string $question8_2
 * @property string $question8_2text
 * @property string $question8_3
 * @property string $question8_3text
 * @property string $question8_4
 * @property string $question8_4text
 * @property string $customerguide
 * @property string $anser1
 * @property string $anser2
 * @property string $anser3
 * @property string $anser4
 * @property int $evaluate1
 * @property int $evaluate2
 * @property int $evaluate3
 * @property int $evaluate4
 * @property int $evaluate5
 * @property int $created_id
 * @property \Cake\I18n\FrozenTime $created_date
 * @property int $modified_id
 * @property \Cake\I18n\FrozenTime $modified_date
 *
 * @property \App\Model\Entity\Reserve $reserve
 * @property \App\Model\Entity\Created $created
 * @property \App\Model\Entity\Modified $modified
 */
class CounselNote extends Entity
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
