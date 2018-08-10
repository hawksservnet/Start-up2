<style>
    label.error {
        color: #e84c3d;
    }
    span.error {
        color: #e84c3d;
        font-weight: bold;
    }

    .d_error {
        display: none;
    }

    ul.list-checking2 {
        padding: 0;
    }

    ul.list-checking2 li {
        margin: 0 20px 0 0;
    }

    .m-l20 {
        margin-left: 25px;
    }

    .w80 {
        width: 80px;
    }
    .finnal-right ul li:nth-child(2)
    {
        width: 32%;
    }
    .finnal-right div.input
    {
        text-align:right;
    }
    .finnal-right div.input input,
    .finnal-right div.input select
    {
        width:42px;
    }

    .textarea-resizable
    {
        /*resize:vertical;*/
        height:70px;

    }
    .preentre-update-mgl-15{
        margin-left: -15px;
        font-weight: bold;
    }
    .preentre-update-mgl-15 span{
        margin-left: 10px;
    }
    .ui-resizable-se {
        right: 4px !important;
        bottom: 20px !important;
    }
</style>
<?php

use Cake\Core\Configure;
$now = new \DateTime();
$arr_user_types = Configure::read('USER_TYPES');
$arr_question1 = Configure::read('QUESTION1');
$arr_question2 = Configure::read('QUESTION2');
$arr_question3 = Configure::read('QUESTION3');
$arr_question4 = Configure::read('QUESTION4');
$arr_question5 = Configure::read('QUESTION5');
$arr_question6 = Configure::read('QUESTION6');
$arr_question7 = Configure::read('QUESTION7');
$arr_question8 = Configure::read('QUESTION8');
$arr_question9 = Configure::read('QUESTION9');
$arr_question10 = Configure::read('QUESTION10');
$arr_question11 = Configure::read('QUESTION11');
$arr_evaluate = Configure::read('EVALUATE');
$arr_question_achive = Configure::read('QUESTION_ACHIVE');
$config_status = Configure::read('QUESTION_STATUS');
$str_work_date_time_start =  strftime('%Y-%m-%d', strtotime($reserve_data->work_date)) . ' '. substr_replace($reserve_data->work_time_start,':',2,0);
$work_date_time_start= Date(strtotime($str_work_date_time_start));
 $timeCurrent = strtotime($now->format('Y-m-d H:i'));
$ControlButton=true;
$ControlTextBox=true;
$ControlRadio=true;
$ControlSelectBox=true;
$ControlSelectBoxStatus=true;
/*
if ($reserve_data->reserve_status==9 || $timeCurrent > $work_date_time_start)
{
    $ControlButton=false;
    $ControlTextBox=false;
    $ControlSelectBox=false;
    $ControlRadio=false;
    $ControlSelectBoxStatus=false;
    $ControlSelectBoxStatus=false;
}
*/
if ($reserve_data->reserve_status==9)
{
    $ControlButton=false;
    $ControlTextBox=false;
    $ControlSelectBox=false;
    $ControlRadio=false;
    $ControlSelectBoxStatus=false;
    $ControlSelectBoxStatus=false;
}
if ($loginInfo['AUTH'] == 1 || $loginInfo['AUTH']== 5 )
{
    $ControlTextBox=false;
    $ControlButton=false;
    $ControlRadio=false;
    $ControlSelectBox=false;
    $ControlSelectBoxStatus=true;
}

$question3_1 = '';
$question3_2 = '';
$question6_1 = '';
$question8_2 = '';
$question8_3 = '';
$question8_4 = '';
if ($reserve_data->CounselNotes['question3_1']) {
    $question3_1 = ',' . $reserve_data->CounselNotes['question3_1'] . ',';
}
if ($reserve_data->CounselNotes['question3_2']) {
    $question3_2 = ',' . $reserve_data->CounselNotes['question3_2'] . ',';
}
if ($reserve_data->CounselNotes['question6_1']) {
    $question6_1 = ',' . $reserve_data->CounselNotes['question6_1'] . ',';
}
if ($reserve_data->CounselNotes['question8_2']) {
    $question8_2 = ',' . $reserve_data->CounselNotes['question8_2'] . ',';
}
if ($reserve_data->CounselNotes['question8_3']) {
    $question8_3 = ',' . $reserve_data->CounselNotes['question8_3'] . ',';
}
if ($reserve_data->CounselNotes['question8_4']) {
    $question8_4 = ',' . $reserve_data->CounselNotes['question8_4'] . ',';
}
$reserve_time = substr($reserve_data->work_time_start, 0, 2) . ':' . substr($reserve_data->work_time_start, 2, 2);
$reserve_time .= '～';
$reserve_time .= substr($reserve_data->work_time_end, 0, 2) . ':' . substr($reserve_data->work_time_end, 2, 2);

if ($reserve_data->CounselNotes['work_time_start']) {
    $counsel_notes_time_start = substr($reserve_data->CounselNotes['work_time_start'], 0, 2) . ':' . substr($reserve_data->CounselNotes['work_time_start'], 2, 2);
} else {
    $counsel_notes_time_start = '';
}
if ($reserve_data->CounselNotes['work_time_end']) {
    $counsel_notes_time_end = substr($reserve_data->CounselNotes['work_time_end'], 0, 2) . ':' . substr($reserve_data->CounselNotes['work_time_end'], 2, 2);
} else {
    $counsel_notes_time_end = '';
}

echo $this->Html->css('jquery-ui');
echo $this->Html->css('scrolltabs.css');
?>
<div class="the-title">
    <h2 class="title-01">カルテ詳細</h2>
</div>

<div class="show-time">
    <?= $this->Flash->render('flash') ?>
</div>
<?php
echo $this->Html->script('jquery.scrolltabs');
echo $this->Html->script('jquery.mousewheel');
?>
<?php
$count = count($reserves->toArray());
?>
<?php if($count<6):?>
    <ul class="tabs">
        <li onclick="javascript:location.href='<?= $this->Url->build(["controller" => "CounselNote", "action" => "info", "?" => ['id'=>$reserve_data->Users['id'], "cn" => $reserve_data->concierge_id]]) ?>'">
            基本情報
        </li>
        <?php

        foreach ($reserves as $row) {
            $class = ($reserve_data->Users['id'] . '_' . $row['id'] == $page_current) ? 'class="active tab_selected"' : '';
            $url = $this->Url->build(["controller" => "CounselNote", "action" => "note", "?" => ["id" => $reserve_data->Users['id'], "rs" => $row['id']]]);
            echo '<li ' . $class . ' onclick="javascript:location.href=\'' . $url . '\'"> ' . strftime('%Y/%m/%d', strtotime($row['work_date'])) . ' '. substr_replace($row['work_time_start'],':',2,0) . '</li>';
        }

        ?>
    </ul>
<?php elseif($count>=6):?>

    <div class="indented_text" style="display: none">
        <div id="tabs" class="scroll_tabs_theme_dark">
            <ul class="tabs scroll-tabs">

                <li onclick="javascript:location.href='<?= $this->Url->build(["controller" => "CounselNote", "action" => "info", "?" => ['id'=>$reserve_data->Users['id'], "cn" => $reserve_data->concierge_id]]) ?>'">
                    基本情報
                </li>
                <?php
                foreach ($reserves as $row) {
                    $class = ($reserve_data->Users['id'] . '_' . $row['id'] == $page_current) ? 'class="active tab_selected"' : '';
                    $style='';
                    $url = $this->Url->build(["controller" => "CounselNote", "action" => "note", "?" => ["id" => $reserve_data->Users['id'], "rs" => $row['id']]]);
                    if ($row->reserve_status == 9) {
                        $style =' style="background:#959595;color:#ffffff"';
                    }
                    echo '<li ' . $class . $style.' onclick="javascript:location.href=\'' . $url . '\'"> ' . strftime('%Y/%m/%d', strtotime($row['work_date'])) . ' '. substr_replace($row['work_time_start'],':',2,0) . '</li>';
                }
                ?>

            </ul>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            tabs = $('#tabs').scrollTabs();
            $('.indented_text').show();
            tabs.scrollSelectedIntoView();
        });

    </script>
<?php endif?>

<?php
$sty='display: block';
if ($count<6)
    $sty='display: block;border-top:1px solid #000 !important;';
?>
<?= $this->Form->create(null, ['id' => 'submit-update-form']) ?>
<?= $this->Form->hidden('id', ['value' => !empty($reserve_data->Users['id']) ? $reserve_data->Users['id'] : 0]) ?>
<?= $this->Form->hidden('rs', ['value' => !empty($reserve_data->id) ? $reserve_data->id : 0]) ?>
<?= $this->Form->hidden('counsel_notes_id', ['value' => !empty($reserve_data->CounselNotes['id']) ? $reserve_data->CounselNotes['id'] : 0]) ?>
<div class="tab_container">
    <div id="tabs-one" class="tab_content" style="<?=$sty?>">
        <div class="swap-total">
            <div class="col-md-6">
                <div class="left-list">
                    <ul>
                        <?php if($reserve_data->reserve_classification == Configure::read('RESERVE_CLASSIFICATION.FROM_RECEPTION') && (empty($reserve_data->csv_user_id) || !is_numeric($reserve_data->csv_user_id))): ?>

                            <li><span>ユーザID</span>:<span><?= $reserve_data->csv_user_id ?></span></li>
                            <li>
                                <span>会員種別</span>:<span><?= $reserve_data->csv_member_classification ?></span>
                            </li>
                            <li><span>氏名</span>:<span><?= $reserve_data->csv_user_name ?></span></li>
                        <?php else: ?>

                            <li><span>ユーザID</span>:<span><?= str_pad( $reserve_data->Users['id'], 9, '0', STR_PAD_LEFT) ?></span></li>
                            <li>
                                <span>会員種別</span>:<span><?= isset($arr_user_types[$reserve_data->Users['type']]) ? $arr_user_types[$reserve_data->Users['type']] : '' ?></span>
                            </li>
                            <li><span>氏名</span>:<span><?= $reserve_data->Users['name_last'] ?>
                                    　<?= $reserve_data->Users['name_first'] ?></span></li>
                        <?php endif; ?>

                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="left-list">
                    <ul>
                        <li>
                            <span>予約日</span>:<span><?= strftime('%Y/%m/%d', strtotime($reserve_data->work_date)) ?></span>
                        </li>
                        <li><span>予約時間</span>:<span><?= $reserve_time ?></span></li>
                        <li><span>予約区分</span>:<span><?= $reserve_data->reserve_classification ? Configure::read('RESERVE_CLASSIFICATION.CLASSIFICATION')[$reserve_data->reserve_classification] : '' ?></span></li>
                        <li>
                            <span>実施時間</span>:
                            <?php if ($ControlTextBox==false) { ?>
                                <input type="hidden" value="<?= $counsel_notes_time_start ?>" name="work_time_start" id="work_time_start"/>
                                <input type="hidden" value="<?= $counsel_notes_time_end ?>" name="work_time_end" id="work_time_end"/>

                                <input type="text" name="" id="" <?= ($ControlTextBox==false?'disabled':'') ?>
                                       value="<?= $counsel_notes_time_start ?>" maxlength="5" placeholder="00:00">~
                                <input type="text" name="" id="" <?= ($ControlTextBox==false?'disabled':'') ?>
                                       value="<?= $counsel_notes_time_end ?>" maxlength="5" placeholder="00:00">

                            <?php } else { ?>
                            <input type="text" name="work_time_start" id="work_time_start" <?= ($ControlTextBox==false?'disabled':'') ?>
                                   value="<?= $counsel_notes_time_start ?>" maxlength="5" placeholder="00:00">~
                            <input type="text" name="work_time_end" id="work_time_end" <?= ($ControlTextBox==false?'disabled':'') ?>
                                   value="<?= $counsel_notes_time_end ?>" maxlength="5" placeholder="00:00">
                            <div class="d_error"><label for="work_time_start" class="error"></label></div>
                            <div class="d_error"><label for="work_time_end" class="error"></label></div>
                            <?php } ?>
                        </li>
                        <li><span>担当</span>:<span><?= $reserve_data->Concierges['name'] ?></span></li>
                        <li class="parent-caret">
                            <span>ステータス</span>
                            <?php
                            $val = '';
                            if ($reserve_data->reserve_status != 9) {
                                $val = 'r_' . $reserve_data->reserve_status;
                            } else {
                                if ($reserve_data->cancel_status) {
                                    $val = 'c_' . $reserve_data->cancel_status;
                                }
                            }

                            echo $this->Form->select('status', ['' => ''] + $config_status, [
                                'label' => false,
                                'value' => $val,
                                'data-reserve_status' => $val,
                                'default' => '',
                                'id' => 'status',
                                'disabled' =>  ($ControlSelectBoxStatus==false?'true':'')
                            ]);
                            ?>
                            <?php if ($ControlSelectBoxStatus) :?>
                                <div class="fm-form">
                                    <button onclick="submitUpdateForm(); return false;" style="float:none; width:18%;">保存</button>
                                </div>
                            <?php endif;?>
                        </li>
                        <div class="d_error"><label for="status" class="error"></label></div>
                    </ul>
                </div>
            </div>
        </div>
        <div class="txct-total">
            <h5 class="m-b15">事前問診　：</h5>
            <h4 class="m-b15 m-l20"><strong>コンシェルジュサービスをどのようにして知りましたか？</strong></h4>
            <div class="m-b15 m-l20">
                <input type="radio" name="question8_1" id="question8_1_1"  <?= ($ControlRadio==false?'disabled':'') ?>
                       value="1" <?= ($reserve_data->CounselNotes['question8_1'] == '1') ? 'checked="checked"' : '' ?>>
                <label for="question8_1_1"><?= isset($arr_question1['1']) ? $arr_question1['1'] : '' ?></label>
            </div>
            <div class="m-b15 m-l20">
                <input type="radio" name="question8_1" id="question8_1_2"  <?= ($ControlRadio==false?'disabled':'') ?>
                       value="2" <?= ($reserve_data->CounselNotes['question8_1'] == '2') ? 'checked="checked"' : '' ?>>
                <label for="question8_1_2"><?= isset($arr_question1['2']) ? $arr_question1['2'] : '' ?></label>
            </div>
            <div class="m-b15 m-l20">
                <input type="radio" name="question8_1" id="question8_1_3"  <?= ($ControlRadio==false?'disabled':'') ?>
                       value="3" <?= ($reserve_data->CounselNotes['question8_1'] == '3') ? 'checked="checked"' : '' ?>>
                <label for="question8_1_3"><?= isset($arr_question1['3']) ? $arr_question1['3'] : '' ?></label>

                <ul class="list-checking">
                    <li>
                        <input class="checking" type="checkbox" name="question8_2[]" id="question8_2_1"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="1" <?= (strpos($question8_2, ',1,') !== false) ? 'checked="checked"' : '' ?>>
                        <label for="question8_2_1"><?= isset($arr_question2['1']) ? $arr_question2['1'] : '' ?></label>
                    </li>
                    <li>
                        <input class="checking" type="checkbox" name="question8_2[]" id="question8_2_2"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="2" <?= (strpos($question8_2, ',2,') !== false) ? 'checked="checked"' : '' ?>>
                        <label for="question8_2_2"><?= isset($arr_question2['2']) ? $arr_question2['2'] : '' ?></label>
                    </li>
                    <li>
                        <input class="checking" type="checkbox" name="question8_2[]" id="question8_2_3"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="3" <?= (strpos($question8_2, ',3,') !== false) ? 'checked="checked"' : '' ?>>
                        <label for="question8_2_3"><?= isset($arr_question2['3']) ? $arr_question2['3'] : '' ?></label>
                    </li>
                    <li>
                        <input class="checking" type="checkbox" name="question8_2[]" id="question8_2_4"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="4" <?= (strpos($question8_2, ',4,') !== false) ? 'checked="checked"' : '' ?>>
                        <label for="question8_2_4"><?= isset($arr_question2['4']) ? $arr_question2['4'] : '' ?></label>
                    </li>
                    <li><input type="text" name="question8_2text" id="question8_2text"  <?= ($ControlTextBox==false?'disabled':'') ?>
                               value="<?= $reserve_data->CounselNotes['question8_2text'] ?>"/>
                    </li>
                    <div class="radio_validate">
                        <?= $this->Form->hidden('question8_2_validate'); ?>
                    </div>
                </ul>
            </div>
            <div class="m-b15 m-l20">
                <input type="radio" name="question8_1" id="question8_1_4"  <?= ($ControlRadio==false?'disabled':'') ?>
                       value="4" <?= ($reserve_data->CounselNotes['question8_1'] == '4') ? 'checked="checked"' : '' ?>>
                <label for="question8_1_4"><?= isset($arr_question1['4']) ? $arr_question1['4'] : '' ?></label>

                <ul class="list-checking">
                    <li>
                        <input class="checking" type="checkbox" name="question8_3[]" id="question8_3_1"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="1" <?= (strpos($question8_3, ',1,') !== false) ? 'checked="checked"' : '' ?>>
                        <label for="question8_3_1"><?= isset($arr_question3['1']) ? $arr_question3['1'] : '' ?></label>
                    </li>
                    <li>
                        <input class="checking" type="checkbox" name="question8_3[]" id="question8_3_2"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="2" <?= (strpos($question8_3, ',2,') !== false) ? 'checked="checked"' : '' ?>>
                        <label for="question8_3_2"><?= isset($arr_question3['2']) ? $arr_question3['2'] : '' ?></label>
                    </li>
                    <li>
                        <input class="checking" type="checkbox" name="question8_3[]" id="question8_3_3"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="3" <?= (strpos($question8_3, ',3,') !== false) ? 'checked="checked"' : '' ?>>
                        <label for="question8_3_3"><?= isset($arr_question3['3']) ? $arr_question3['3'] : '' ?></label>
                    </li>
                    <li>
                        <input class="checking" type="checkbox" name="question8_3[]" id="question8_3_4"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="4" <?= (strpos($question8_3, ',4,') !== false) ? 'checked="checked"' : '' ?>>
                        <label for="question8_3_4"><?= isset($arr_question3['4']) ? $arr_question3['4'] : '' ?></label>
                    </li>
                    <li><input type="text" name="question8_3text" id="question8_3text"  <?= ($ControlTextBox==false?'disabled':'') ?>
                               value="<?= $reserve_data->CounselNotes['question8_3text'] ?>"/>
                    </li>
                    <div class="radio_validate">
                        <?= $this->Form->hidden('question8_3_validate'); ?>
                    </div>
                </ul>
            </div>
            <div class="m-b15 m-l20">
                <input type="radio" name="question8_1" id="question8_1_5"  <?= ($ControlRadio==false?'disabled':'') ?>
                       value="5" <?= ($reserve_data->CounselNotes['question8_1'] == '5') ? 'checked="checked"' : '' ?>>
                <label for="question8_1_5"><?= isset($arr_question1['5']) ? $arr_question1['5'] : '' ?></label>

                <ul class="list-checking">
                    <li>
                        <input class="checking" type="checkbox" name="question8_4[]" id="question8_4_1"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="1" <?= (strpos($question8_4, ',1,') !== false) ? 'checked="checked"' : '' ?>>
                        <label for="question8_4_1"><?= isset($arr_question4['1']) ? $arr_question4['1'] : '' ?></label>
                    </li>
                    <li>
                        <input class="checking" type="checkbox" name="question8_4[]" id="question8_4_2"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="2" <?= (strpos($question8_4, ',2,') !== false) ? 'checked="checked"' : '' ?>>
                        <label for="question8_4_2"><?= isset($arr_question4['2']) ? $arr_question4['2'] : '' ?></label>
                    </li>
                    <li>
                        <input class="checking" type="checkbox" name="question8_4[]" id="question8_4_3"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="3" <?= (strpos($question8_4, ',3,') !== false) ? 'checked="checked"' : '' ?>>
                        <label for="question8_4_3"><?= isset($arr_question4['3']) ? $arr_question4['3'] : '' ?></label>
                    </li>
                    <li>
                        <input class="checking" type="checkbox" name="question8_4[]" id="question8_4_4"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="4" <?= (strpos($question8_4, ',4,') !== false) ? 'checked="checked"' : '' ?>>
                        <label for="question8_4_4"><?= isset($arr_question4['4']) ? $arr_question4['4'] : '' ?></label>
                    </li>
                    <li>
                        <input class="checking" type="checkbox" name="question8_4[]" id="question8_4_5"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="5" <?= (strpos($question8_4, ',5,') !== false) ? 'checked="checked"' : '' ?>>
                        <label for="question8_4_5"><?= isset($arr_question4['5']) ? $arr_question4['5'] : '' ?></label>
                    </li>
                    <li><input type="text" name="question8_4text" id="question8_4text"  <?= ($ControlTextBox==false?'disabled':'') ?>
                               value="<?= $reserve_data->CounselNotes['question8_4text'] ?>"/>
                    </li>
                    <div class="radio_validate">
                        <?= $this->Form->hidden('question8_4_validate'); ?>
                    </div>
                </ul>
            </div>
            <div class="m-b15 m-l20">
                <input type="radio" name="question8_1" id="question8_1_6"  <?= ($ControlRadio==false?'disabled':'') ?>
                       value="6" <?= ($reserve_data->CounselNotes['question8_1'] == '6') ? 'checked="checked"' : '' ?>>
                <label for="question8_1_6"><?= isset($arr_question1['6']) ? $arr_question1['6'] : '' ?></label>
                <input type="text" name="question8_1text" id="question8_1text" class="question8_text"  <?= ($ControlTextBox==false?'disabled':'') ?>
                       value="<?= $reserve_data->CounselNotes['question8_1text'] ?>"/>
            </div>
            <ol>
                <li class="parent-caret">
                    <p class="bold">起業への意思決定状況について教えてください。</p>
                    <?= $this->Form->select('question1_1', ['0' => ''] + $arr_question5, [
                        'label' => false,
                        'value' => !empty($reserve_data->CounselNotes['question1_1']) ? $reserve_data->CounselNotes['question1_1'] : '0',
                        'default' => '0',
                        'id' => 'question1',
                        'disabled' =>  ($ControlSelectBox==false?'true':'')
                    ]);
                    ?>
                </li>
                <li class="parent-caret">
                    <p class="bold">現在のお仕事の状況について教えてください。</p>
                    <?= $this->Form->select('question2_1', ['0' => ''] + $arr_question6, [
                        'label' => false,
                        'value' => !empty($reserve_data->CounselNotes['question2_1']) ? $reserve_data->CounselNotes['question2_1'] : '0',
                        'default' => '0',
                        'id' => 'question2',
                        'disabled' =>  ($ControlSelectBox==false?'true':'')
                    ]);
                    ?>
                </li>
                <li>
                    <p class="bold">起業に関する現在の悩みを教えてください。（複数回答可）</p>
                    <div>
                        <input class="checking" type="checkbox" name="question3_1[]" id="question3_1_1"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="1" <?= (strpos($question3_1, ',1,') !== false) ? 'checked' : '' ?>>
                        <label for="question3_1_1"><?= isset($arr_question7['1']) ? $arr_question7['1'] : '' ?></label>
                    </div>
                    <div>
                        <input class="checking" type="checkbox" name="question3_1[]" id="question3_1_2"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="2" <?= (strpos($question3_1, ',2,') !== false) ? 'checked' : '' ?>>
                        <label for="question3_1_2"><?= isset($arr_question7['2']) ? $arr_question7['2'] : '' ?></label>
                    </div>
                    <div>
                        <input class="checking" type="checkbox" name="question3_1[]" id="question3_1_3"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="3" <?= (strpos($question3_1, ',3,') !== false) ? 'checked' : '' ?>>
                        <label for="question3_1_3"><?= isset($arr_question7['3']) ? $arr_question7['3'] : '' ?></label>
                    </div>
                    <div>
                        <input class="checking" type="checkbox" name="question3_1[]" id="question3_1_4"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="4" <?= (strpos($question3_1, ',4,') !== false) ? 'checked' : '' ?>>
                        <label for="question3_1_4"><?= isset($arr_question7['4']) ? $arr_question7['4'] : '' ?></label>
                    </div>
                    <div>
                        <input class="checking" type="checkbox" name="question3_1[]" id="question3_1_5"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="5" <?= (strpos($question3_1, ',5,') !== false) ? 'checked' : '' ?>>
                        <label for="question3_1_5"><?= isset($arr_question7['5']) ? $arr_question7['5'] : '' ?></label>

                        <ul class="list-checking">
                            <li>
                                <input class="checking" type="checkbox" name="question3_2[]" id="question3_2_1"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="1" <?= (strpos($question3_2, ',1,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question3_2_1"><?= isset($arr_question8['1']) ? $arr_question8['1'] : '' ?></label>
                            </li>
                            <li>
                                <input class="checking" type="checkbox" name="question3_2[]" id="question3_2_2"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="2" <?= (strpos($question3_2, ',2,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question3_2_2"><?= isset($arr_question8['2']) ? $arr_question8['2'] : '' ?></label>
                            </li>
                            <li>
                                <input class="checking" type="checkbox" name="question3_2[]" id="question3_2_3"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="3" <?= (strpos($question3_2, ',3,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question3_2_3"><?= isset($arr_question8['3']) ? $arr_question8['3'] : '' ?></label>
                            </li>
                            <li>
                                <input class="checking" type="checkbox" name="question3_2[]" id="question3_2_4"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="4" <?= (strpos($question3_2, ',4,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question3_2_4"><?= isset($arr_question8['4']) ? $arr_question8['4'] : '' ?></label>
                            </li>
                            <li>
                                <input class="checking" type="checkbox" name="question3_2[]" id="question3_2_5"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="5" <?= (strpos($question3_2, ',5,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question3_2_5"><?= isset($arr_question8['5']) ? $arr_question8['5'] : '' ?></label>
                            </li>
                            <li><input type="text" name="question3_2text" id="question3_2text"  <?= ($ControlTextBox==false?'disabled':'') ?>
                                       value="<?= $reserve_data->CounselNotes['question3_2text'] ?>"/>
                            </li>
                            <div class="radio_validate">
                                <?= $this->Form->hidden('question3_2_validate'); ?>
                            </div>
                        </ul>
                    </div>
                    <div>
                        <input class="checking" type="checkbox" name="question3_1[]" id="question3_1_6"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="6" <?= (strpos($question3_1, ',6,') !== false) ? 'checked' : '' ?>>
                        <label for="question3_1_6"><?= isset($arr_question7['6']) ? $arr_question7['6'] : '' ?></label>
                    </div>
                    <div>
                        <input class="checking" type="checkbox" name="question3_1[]" id="question3_1_7"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="7" <?= (strpos($question3_1, ',7,') !== false) ? 'checked' : '' ?>>
                        <label for="question3_1_7"><?= isset($arr_question7['7']) ? $arr_question7['7'] : '' ?></label>
                    </div>
                    <div>
                        <input class="checking" type="checkbox" name="question3_1[]" id="question3_1_8"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="8" <?= (strpos($question3_1, ',8,') !== false) ? 'checked' : '' ?>>
                        <label for="question3_1_8"><?= isset($arr_question7['8']) ? $arr_question7['8'] : '' ?></label>
                    </div>
                    <div>
                        <input class="checking" type="checkbox" name="question3_1[]" id="question3_1_9"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="9" <?= (strpos($question3_1, ',9,') !== false) ? 'checked' : '' ?>>
                        <label for="question3_1_9"><?= isset($arr_question7['9']) ? $arr_question7['9'] : '' ?></label>
                        <input type="text" name="question3_1text" id="question3_1text"  <?= ($ControlTextBox==false?'disabled':'') ?>
                               value="<?= $reserve_data->CounselNotes['question3_1text'] ?>"/>
                    </div>
                </li>
                <li>
                    <p class="bold">想定している顧客層について教えてください。</p>
                    <div class="parent-caret">
                        <?= $this->Form->select('question4_1', ['0' => ''] + $arr_question9, [
                            'label' => false,
                            'value' => !empty($reserve_data->CounselNotes['question4_1']) ? $reserve_data->CounselNotes['question4_1'] : '0',
                            'default' => '0',
                            'id' => 'question4_1',
                            'disabled' =>  ($ControlSelectBox==false?'true':'')
                        ]);
                        ?>
                    </div>
                </li>
                <li>
                    <p class="bold">業種について教えてください。（複数回答可）</p>
                    <div>
                        <ul class="list-checking list-checking2">
                            <li>
                                <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_1"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="1" <?= (strpos($question6_1, ',1,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question6_1_1"><?= isset($arr_question10['1']) ? $arr_question10['1'] : '' ?></label>
                            </li>
                            <li>
                                <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_2"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="2" <?= (strpos($question6_1, ',2,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question6_1_2"><?= isset($arr_question10['2']) ? $arr_question10['2'] : '' ?></label>
                            </li>
                            <li>
                                <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_3"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="3" <?= (strpos($question6_1, ',3,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question6_1_3"><?= isset($arr_question10['3']) ? $arr_question10['3'] : '' ?></label>
                            </li>
                            <li>
                                <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_4"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="4" <?= (strpos($question6_1, ',4,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question6_1_4"><?= isset($arr_question10['4']) ? $arr_question10['4'] : '' ?></label>
                            </li>
                            <li>
                                <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_5"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="5" <?= (strpos($question6_1, ',5,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question6_1_5"><?= isset($arr_question10['5']) ? $arr_question10['5'] : '' ?></label>
                            </li>
                            <li>
                                <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_6"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="6" <?= (strpos($question6_1, ',6,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question6_1_6"><?= isset($arr_question10['6']) ? $arr_question10['6'] : '' ?></label>
                            </li>
                        </ul>
                        <ul class="list-checking list-checking2">
                            <li>
                                <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_7"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="7" <?= (strpos($question6_1, ',7,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question6_1_7"><?= isset($arr_question10['7']) ? $arr_question10['7'] : '' ?></label>
                            </li>
                            <li>
                                <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_8"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="8" <?= (strpos($question6_1, ',8,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question6_1_8"><?= isset($arr_question10['8']) ? $arr_question10['8'] : '' ?></label>
                            </li>
                            <li>
                                <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_9"  <?= ($ControlRadio==false?'disabled':'') ?>
                                       value="9" <?= (strpos($question6_1, ',9,') !== false) ? 'checked' : '' ?>>
                                <label
                                    for="question6_1_9"><?= isset($arr_question10['9']) ? $arr_question10['9'] : '' ?></label>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_10"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="10" <?= (strpos($question6_1, ',10,') !== false) ? 'checked' : '' ?>>
                        <label
                            for="question6_1_10"><?= isset($arr_question10['10']) ? $arr_question10['10'] : '' ?></label>
                    </div>
                    <div>
                        <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_11"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="11" <?= (strpos($question6_1, ',11,') !== false) ? 'checked' : '' ?>>
                        <label
                            for="question6_1_11"><?= isset($arr_question10['11']) ? $arr_question10['11'] : '' ?></label>
                    </div>
                    <div>
                        <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_12"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="12" <?= (strpos($question6_1, ',12,') !== false) ? 'checked' : '' ?>>
                        <label
                            for="question6_1_12"><?= isset($arr_question10['12']) ? $arr_question10['12'] : '' ?></label>
                    </div>
                    <div>
                        <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_13"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="13" <?= (strpos($question6_1, ',13,') !== false) ? 'checked' : '' ?>>
                        <label for="question6_1_13"><?= isset($arr_question10['13']) ? $arr_question10['13'] : '' ?></label>
                    </div>
                    <div>
                        <input class="checking" type="checkbox" name="question6_1[]" id="question6_1_14"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="14" <?= (strpos($question6_1, ',14,') !== false) ? 'checked' : '' ?>>
                        <label for="question6_1_14"><?= isset($arr_question10['14']) ? $arr_question10['14'] : '' ?></label>
                        <input type="text" name="question6_1text" id="question6_1text"  <?= ($ControlTextBox==false?'disabled':'') ?>
                               value="<?= $reserve_data->CounselNotes['question6_1text'] ?>"/>
                    </div>                    <br/>
                    <div>あなたが考えている事業の概略を可能な範囲で記入してください</div>
                    <?= $this->Form->input('question5_1text', [
                        'label' => false,
                        'type' => 'textarea',
                        'class' => 'textarea-resizable',
                        'value' => $reserve_data->CounselNotes['question5_1text'],
                        'id' => 'question5_1text',
                        'rows' => '2',
                        'readonly' =>  ($ControlSelectBox==false?'true':'')
                    ]);
                    ?>
                </li>
                <li>
                    <p class="bold">第三者に見せるための事業計画（書）の作成状況について教えてください。</p>
                    <div class="parent-caret">
                        <?= $this->Form->select('question7_1', ['0' => ''] + $arr_question11, [
                            'label' => false,
                            'value' => !empty($reserve_data->CounselNotes['question7_1']) ? $reserve_data->CounselNotes['question7_1'] : '0',
                            'default' => '0',
                            'id' => 'question7_1',
                            'disabled' =>  ($ControlSelectBox==false?'true':'')
                        ]);
                        ?>
                    </div>
                </li>
                <li>
                    <p class="bold">今回の相談の概要を教えてください。</p>
                    <div class="parent-caret">
                        <?= $this->Form->input('question9_1text', [
                            'label' => false,
                            'type' => 'textarea',
                            'class' => 'textarea-resizable',
                            'value' => $reserve_data->CounselNotes['question9_1text'],
                            'id' => 'question9_1text',
                            'rows' => '2',
                            'readonly' =>  ($ControlTextBox==false?'true':'')
                        ]);
                        ?>
                    </div>
                </li>
                <?php if(!empty($reserve_data->Users['type']) && $reserve_data->Users['type']==2): ?>
                <li>
                    <p class="bold">プレアントレメンバー更新希望</p>
                    <div class="parent-caret">
                        <input class="checking" type="checkbox" name="preentre_update" id="preentre_update"  <?= ($ControlRadio==false?'disabled':'') ?>
                               value="1" <?= (!empty($reserve_data->CounselNotes['preentre_update']) && $reserve_data->CounselNotes['preentre_update'] == '1') ? 'checked' : '' ?>>
                        <label for="preentre_update">更新を希望する</label>
                    </div>
                </li>

                <?php endif; ?>
            </ol>
            <div class="fm-form">
                <?php if ($ControlButton) :?>
                <button onclick="submitUpdateForm(); return false;">保存</button>
                <?php endif;?>
            </div>
        </div>
        <div class="txct-finnal">
            <div class="col-md-6">
                <div class="finnal-left">
                    <h5>今回の相談について</h5>
                    <span>事業内容</span>
                    <span>顧客（ターゲット）／プロダクト＆サービス／特徴／将来目標</span>
                    <?= $this->Form->input('anser1', [
                        'label' => false,
                        'type' => 'textarea',
                        'class' => 'textarea-resizable',
                        'value' => $reserve_data->CounselNotes['anser1'],
                        'id' => 'anser1',
                        'rows' => '2',
                        'readonly' =>  ($ControlTextBox==false?'true':'')
                    ]);
                    ?>
                    <span>現状把握</span>
                    <span>起業準備状況/創業者スキル/チーム/課題</span>
                    <?= $this->Form->input('anser2', [
                        'label' => false,
                        'type' => 'textarea',
                        'class' => 'textarea-resizable',
                        'value' => $reserve_data->CounselNotes['anser2'],
                        'id' => 'anser2',
                        'rows' => '2',
                        'readonly' =>  ($ControlTextBox==false?'true':'')
                    ]);
                    ?>
                    <span>相談内容とアドバイス</span>
                    <span>相談内容/アドバイス/紹介連携機関/次回までの宿題</span>
                    <?= $this->Form->input('anser3', [
                        'label' => false,
                        'type' => 'textarea',
                        'class' => 'textarea-resizable',
                        'value' => $reserve_data->CounselNotes['anser3'],
                        'id' => 'anser3',
                        'rows' => '2',
                        'readonly' =>  ($ControlTextBox==false?'true':'')
                    ]);
                    ?>
                    <span>所見・対応・特記事項</span>
                    <span>次回申し送り/意欲・人物への所見/事業への所見/起業・成長可能性</span>
                    <?= $this->Form->input('anser4', [
                        'label' => false,
                        'type' => 'textarea',
                        'class' => 'textarea-resizable',
                        'value' => $reserve_data->CounselNotes['anser4'],
                        'id' => 'anser4',
                        'rows' => '2',
                        'readonly' =>  ($ControlTextBox==false?'true':'')
                    ]);
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="finnal-right">
                    <!--<h5>評価</h5>-->
                    <ul>
                        <li>事業選択の必然性</li>
                        <li>

                            <div class="input text">
                            <?= $this->Form->select('evaluate1', [''=>'']+  $arr_evaluate, [
                                'label' => false,
                                'value' => !empty($reserve_data->CounselNotes['evaluate1']) ? $reserve_data->CounselNotes['evaluate1'] : '0',
                                'default' => '1',
                                'id' => 'evaluate1',
                                'class' => 'evaluate',
                                'disabled' =>  ($ControlSelectBox==false?'true':'')
                            ]);
                            ?>
                            </div>
                        </li>
                        <li>点</li>
                        <div class="d_error"><label for="evaluate1" class="error"></label></div>
                        <p>（やる気、決意、経歴やスキルとのマッチ度、相談者がその事業を行う納得性）</p>
                    </ul>
                    <ul>
                        <li>事業アイデアの魅力</li>
                        <li>   <div class="input text">
                                <?= $this->Form->select('evaluate2', [''=>'']+  $arr_evaluate, [
                                    'label' => false,
                                    'value' => !empty($reserve_data->CounselNotes['evaluate2']) ? $reserve_data->CounselNotes['evaluate2'] : '0',
                                    'default' => '1',
                                    'id' => 'evaluate2',
                                    'class' => 'evaluate',
                                    'disabled' =>  ($ControlSelectBox==false?'true':'')
                                ]);
                                ?>
                            </div></li>
                        <li>点</li>
                        <div class="d_error"><label for="evaluate2" class="error"></label></div>
                        <p>（事業の新規性や社会性、ワクワク感）</p>
                    </ul>
                    <ul>
                        <li>事業アイデアの実現性</li>
                        <li>
                            <div class="input text">
                                <?= $this->Form->select('evaluate3', [''=>'']+ $arr_evaluate, [
                                    'label' => false,
                                    'value' => !empty($reserve_data->CounselNotes['evaluate3']) ? $reserve_data->CounselNotes['evaluate3'] : '0',
                                    'default' => '1',
                                    'id' => 'evaluate3',
                                    'class' => 'evaluate',
                                    'disabled' =>  ($ControlSelectBox==false?'true':'')
                                ]);
                                ?>
                            </div>
                        </li>
                        <li>点</li>
                        <div class="d_error"><label for="evaluate3" class="error"></label></div>
                        <p>（直感的な市場性、収益性）</p>
                    </ul>
                    <ul>
                        <li>経営遂行能力</li>
                        <li>
                            <div class="input text">
                                <?= $this->Form->select('evaluate4', [''=>'']+  $arr_evaluate, [
                                    'label' => false,
                                    'value' => !empty($reserve_data->CounselNotes['evaluate4']) ? $reserve_data->CounselNotes['evaluate4'] : '0',
                                    'default' => '1',
                                    'id' => 'evaluate4',
                                    'class' => 'evaluate',
                                    'disabled' =>  ($ControlSelectBox==false?'true':'')

                                ]);
                                ?>
                            </div>
                        </li>
                        <li>点</li>
                        <div class="d_error"><label for="evaluate4" class="error"></label></div>
                        <p>（経営リテラシー、経営資源を揃える力、事業計画書作成能力）</p>
                    </ul>
                    <ul>
                        <li>合計</li>
                        <li>
                            <div class="input text">
                            <?= $this->Form->input('evaluate5', [
                                'label' => false,
                                'type' => 'text',
                                'value' => $reserve_data->CounselNotes['evaluate5'],
                                'id' => 'evaluate5',
                                'maxlength' => '2',
                                'disabled' =>  ($ControlTextBox==false?'true':'')
                            ]);
                            ?>
                            </div>
                        </li>
                        <li>点</li>
                        <div class="d_error"><label for="evaluate5" class="error"></label></div>
                        <div class=""><span class="error">実施時間・ステータスを切り替えること。</span></div>
                    </ul>
                </div>
                <div class="clearfix"></div>
                <?php if ($ControlButton) :?>
                <button class="fm-magic" onclick="submitUpdateForm(); return false;" style="width:15%;">保存</button>
                <?php endif;?>
            </div>
        </div>
    </div><!-- #tab1 -->
</div> <!-- .tab_container -->

<div class="clearfix"></div>
<div class="btn-block btn-frm">
      <a href="/management/reserve" class="btn">戻る</a>
 </div>

<!-- Modal -->
<div id="myModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="btn_close" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">承認・非承認確認フォーム</h4>

            </div>
            <div class="modal-body">
                <div class="fm-text">
                    <p>以下の予約をキャンセルします。</p>
                    <p>理由を入力し、「送信」ボタンを押下してください</p>
                    <!--<div class="show-time">
                        <a class="fm-submit" id="md_msg"  href="javascript:void(0)">メッセージ表示エリア</a>
                    </div> -->
                    <ul class="p-none m-b15">
                        <li><span>利用者名</span>:<span id="md_username"><?= $reserve_data->Users['name_last'] ?>
                                　<?= $reserve_data->Users['name_first'] ?></span></li>
                        <li><span>予約日</span>:<span id="md_md_purpose"><?= strftime('%Y/%m/%d', strtotime($reserve_data->work_date)) ?></span></li>
                        <li><span>時間</span>:<span id="md_reserve_date"><?= substr_replace($reserve_data->work_time_start,':',2,0)  ?></span></li>
                        <li><span>担当者</span>:<span id="md_reserve_time"><?=$reserve_data->Concierges['name']?></span></li>
                    </ul>

                    <p>理由</p>
                </div>
                <div class="fm-form clearfix">
                    <textarea name="comment" id="comment" cols="5" rows="5"></textarea>
                    <div class="col-md-6 msg_error" style="color:red;"></div>
                    <div class="pright">
                        <input type="button" id="md_cancel" value="キャンセル" data-dismiss="modal" name="" class="btn">
                        <input type="button" value="実行" onclick="submitCanel(); return false;" id="md_submit" name="" class="btn" >
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!--end Modal -->

<?= $this->Form->end() ?>


<!-- End Form -->
<?= $this->Html->script('jquery.validate.min') ?>
<?= $this->Html->script('jquery-ui') ?>

<?=  $this->Html->script('jquery.scrolltabs'); ?>
<?=  $this->Html->script('jquery.mousewheel'); ?>
<div id="dialog-confirm" style="display:none;">
    <p style="text-align: center; margin-top: 10px; font-weight: bold;">
        更新します。よろしいですか？
    </p>
</div>
<script>
     function submitCanel () {
        if ($('#submit-update-form').valid()) {
            $('#submit-update-form').submit();
        }
    }
    $(document).ready(function () {

        // $('.textarea-resizable').resizable({
        //      handles: 'se',
        //     resize: function (event, ui) {

        //          maxWidth=500;
        //     }

        // });
        $('.textarea-resizable').each(function( index ) {
            $(this).resizable({
                handles: 'se',
                minHeight:70,
                maxWidth:$(this).width()+6,
                minWidth:$(this).width()+6,
            });
        });

        $('#btn_close,#md_cancel').click(function () {
            $('textarea#comment').val('');
            $('textarea#comment').text('');
            // remove validate
            $( "textarea#comment" ).rules( "remove" );
            /*$('select#status').val($('select#status').attr('data-reserve_status'));*/
            $('#myModal').modal('hide') ;
        });
        $.validator.addMethod("time", function (value, element) {
            return this.optional(element) || /^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/i.test(value);
        });

        var requiredMessage = "※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_004')?>";
        $.validator.addMethod("question_8_2_required", function (value, element) {
            return  $('input[name="question8_2[]"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("question_8_3_required", function (value, element) {
            return $('input[name="question8_3[]"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("question_8_4_required", function (value, element) {
            return $('input[name="question8_4[]"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("question_3_2_required", function (value, element) {
            return $('input[name="question3_2[]"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("select_box_not_empty", function (value, element) {
            return this.optional(element) || (value.length > 0 && parseInt(value) > 0);
        },requiredMessage);
        $("#submit-update-form").validate({
            ignore: [],
            rules: {
                work_time_start: {
                    required: true,
                    minlength: 5,
                    maxlength: 5,
                    time: true
                },
                work_time_end: {
                    required: true,
                    minlength: 5,
                    maxlength: 5,
                    time: true
                },
                status: {
                    required: true
                },
                anser1: {
                    required: function (element) {
                        return ($("#status").val() == 'r_3') ? true : false;
                    }
                },
                anser2: {
                    required: function (element) {
                        return ($("#status").val() == 'r_3') ? true : false;
                    }
                },
                anser3: {
                    required: function (element) {
                        return ($("#status").val() == 'r_3') ? true : false;
                    }
                },
                anser4: {
                    required: function (element) {
                        return ($("#status").val() == 'r_3') ? true : false;
                    }
                },
                evaluate1: {
                    required: function (element) {
                        return ($("#status").val() == 'r_3') ? true : false;
                    },
                    number: true,
                    max: 5
                },
                evaluate2: {
                    required: function (element) {
                        return ($("#status").val() == 'r_3') ? true : false;
                    },
                    number: true,
                    max: 5
                },
                evaluate3: {
                    required: function (element) {
                        return ($("#status").val() == 'r_3') ? true : false;
                    },
                    number: true,
                    max: 5
                },
                evaluate4: {
                    required: function (element) {
                        return ($("#status").val() == 'r_3') ? true : false;
                    },
                    number: true,
                    max: 5
                },
                evaluate5: {
                    required: function (element) {
                        return ($("#status").val() == 'r_3') ? true : false;
                    },
                    number: true,
                    max: 20
                },
                question8_2_validate: {
                    question_8_2_required: function(){
                        return $('input[name=question8_1]:checked').val() == '3';
                    },
                },
                question8_3_validate: {
                    question_8_3_required: function(){
                        return $('input[name=question8_1]:checked').val() == '4';
                    },
                },
                question8_4_validate: {
                    question_8_4_required: function(){
                        return $('input[name=question8_1]:checked').val() == '5';
                    },
                },
                question3_2_validate: {
                    question_3_2_required: function(){
                        return $('#question3_1_5').prop('checked') == true;
                    },
                },
                <?php if(!empty($reserve_data->Users['type']) && $reserve_data->Users['type']==2): ?>
                question1_1: {
                    select_box_not_empty: function(){
                        return $('input[name=preentre_update]:checked').val() == '1';
                    },
                },
                question2_1: {
                    select_box_not_empty: function(){
                        return $('input[name=preentre_update]:checked').val() == '1';
                    },
                },
                <?php endif; ?>
            },
            messages: {
                work_time_start: {
                    required: requiredMessage,
                    minlength: "※５文字以内で入力して下さい。",
                    maxlength: "※５文字以内で入力して下さい。",
                    time: "※時間規則違犯、例：「10：00」。"
                },
                work_time_end: {
                    required: requiredMessage,
                    minlength: "※５文字以内で入力して下さい。",
                    maxlength: "※５文字以内で入力して下さい。",
                    time: "※時間規則違犯、例：「10：00」。"
                },
                status: {
                    required: requiredMessage
                },
                anser1: {
                    required: requiredMessage
                },
                anser2: {
                    required: requiredMessage
                },
                anser3: {
                    required: requiredMessage
                },
                anser4: {
                    required: requiredMessage
                },
                evaluate1: {
                    required: requiredMessage,
                    number: "※番号を入力するだけです",
                    max: "※<?=str_replace(['{0}', '{1}'], ['事業選択の必然性', '5'], Configure::read('MESSAGE_NOTIFICATION.MSG_008'))?>"
                },
                evaluate2: {
                    required: requiredMessage,
                    number: "※番号を入力するだけです",
                    max: "※<?=str_replace(['{0}', '{1}'], ['事業アイデアの魅力', '5'], Configure::read('MESSAGE_NOTIFICATION.MSG_008'))?>"
                },
                evaluate3: {
                    required: requiredMessage,
                    number: "※番号を入力するだけです",
                    max: "※<?=str_replace(['{0}', '{1}'], ['事業アイデアの実現性', '5'], Configure::read('MESSAGE_NOTIFICATION.MSG_008'))?>"
                },
                evaluate4: {
                    required: requiredMessage,
                    number: "※番号を入力するだけです",
                    max: "※<?=str_replace(['{0}', '{1}'], ['経営遂行能力', '5'], Configure::read('MESSAGE_NOTIFICATION.MSG_008'))?>"
                },
                evaluate5: {
                    required: requiredMessage,
                    number: "※番号を入力するだけです",
                    max: "※<?=str_replace(['{0}', '{1}'], ['合計', '20'], Configure::read('MESSAGE_NOTIFICATION.MSG_008'))?>"
                },
                comment: {
                    required: requiredMessage,
                }
            }
        });
    });

    $('select.evaluate').change(function () {
        var total=0;
        $( "select.evaluate" ).each(function( index ) {
            var valAdd = $( this ).val();
            if(valAdd == '') valAdd = 0;
            total += parseInt(valAdd);
        });
        $('#evaluate5').val(total);
        $('#evaluate5').text(total);

    })

    function submitUpdateForm() {
        $('.d_error').hide();
        $('.show-time').html('');
        if ($('#submit-update-form').valid()) {
            $("#dialog-confirm").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "OK": function () {
                        $(this).dialog("close");
                        var old_status = $('select#status').attr('data-reserve_status');
                        var new_status_change = $('select#status').val();
                        if(old_status != new_status_change && new_status_change == 'c_9'){
                            $('textarea#comment').rules( "add", {
                                required: true,
                            });
                            $('#myModal').modal('show') ;
                        }else{
                            $('#submit-update-form').submit();
                        }
                    },
                    "キャンセル": function () {
                        $(this).dialog("close");
                    }
                },
                closeOnEscape: false,
                open: function (event, ui) {
                    $(".ui-dialog-titlebar", ui.dialog | ui).hide();
                }
            });
        } else {
            $('.d_error').show();
            if($("#status").val() == 'r_3' &&
                (
                    $("#anser1").val().trim().length < 1 || $("#anser2").val().trim().length < 1 || $("#anser3").val().trim().length < 1 || $("#anser4").val().trim().length < 1 ||
                    $("#evaluate1").val().trim().length < 1 || $("#evaluate2").val().trim().length < 1 || $("#evaluate3").val().trim().length < 1 || $("#evaluate4").val().trim().length < 1 || $("#evaluate5").val().trim().length < 1
                )){
                var body = $("html, body");
                body.stop().animate({scrollTop:0}, 500, 'swing', function() {

                });
                $('.show-time').html('<div class="flash-message flash-error"><button type="button" class="close" onclick="$(this).parent().remove();" aria-hidden="true">×</button><p><?=Configure::read('MESSAGE_NOTIFICATION.MSG_025')?></p></div>');
            }else{
                $('html, body').animate({
                    scrollTop: $("label.error:visible").offset().top - 100
                }, 600);
            }
        }
    }
</script>
