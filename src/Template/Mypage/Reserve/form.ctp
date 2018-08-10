<style>
    input[type="checkbox"] + label:before {
        margin: 0 1.5rem 0 0;
    }
    .preentre-update-mgl-15{
        margin-left: -15px;
    }
    .preentre-update-mgl-15 span{
        margin-left: 10px;
    }
</style>
<?php
echo $this->Html->css('/assets-sht2/css/main');
use Cake\Core\Configure;

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
$arr_question_achive = Configure::read('QUESTION_ACHIVE');

$question3_1 = '';
$question3_2 = '';
$question6_1 = '';
$question8_2 = '';
$question8_3 = '';
$question8_4 = '';
/*
 * Notice: Undefined index: question3_1
 **/
if (isset($data_load['question3_1'])) {
    $question3_1 = ',' . $data_load['question3_1'] . ',';
}
/*
 * Notice: Undefined index: question3_2
 **/
if (isset($data_load['question3_2'])) {
    $question3_2 = ',' . $data_load['question3_2'] . ',';
}
/*
 * Notice: Undefined index: question6_1
 **/
if (isset($data_load['question6_1'])) {
    $question6_1 = ',' . $data_load['question6_1'] . ',';
}
/*
 * Notice: Undefined index: question8_2
 **/
if (isset($data_load['question8_2'])) {
    $question8_2 = ',' . $data_load['question8_2'] . ',';
}
/*
 * Notice: Undefined index: question8_3
 **/
if (isset($data_load['question8_3'])) {
    $question8_3 = ',' . $data_load['question8_3'] . ',';
}
/*
 * fix Notice: Undefined index: question8_4
 **/
if (isset($data_load['question8_4'])) {
    $question8_4 = ',' . $data_load['question8_4'] . ',';
}
/*
 * fix Notice: Undefined index: reserves
 **/
$data_load['reserves'] = !(empty($data_load['reserves'])) ? $data_load['reserves'] : 0;

$arr_info['work_date'] = date('Y/m/d', strtotime($data_load['work_date']));
$arr_info['work_time_start'] = substr($data_load['work_time_start'], 0, 2) . ':' . substr($data_load['work_time_start'], 2, 2);
$arr_info['concierge_name'] = $data_load['concierge_name'];
$arr_info['user_id'] = $data_load['user_id'];
$arr_info['user_name'] = $data_load['user_name'];
$arr_info['user_type'] = isset($arr_user_types[$data_load['user_type']]) ? $arr_user_types[$data_load['user_type']] : '';

// Variable of $isDisable, $TimeFlag
$TimeFlag = false ;
$isDisable =false;

//予約日時重複チェック
/*
 * Edit by Huynh
 * Notice: undefined index: reserve_flg display on screen
 **/
if (!empty($data_load['reserve_flg'])) {
	$ReserveFlg = true;
}else{
	$ReserveFlg = false;
}
//シフト日時超過チェック
$workDateTine = strtotime($data_load['work_date'] . ' ' . $arr_info['work_time_start']);
//$TimeFlag = time() > $workDateTine;
if (time() > $workDateTine) {
	$TimeFlag = true;
}else{
	$TimeFlag = false;
}

//$isDisable = ($TimeFlag || $ReserveFlg || (!empty($data_load['reserve_status']) && $data_load['reserve_status'] == 9));
//予約時間超過、予約日時重複、
if ($TimeFlag || $ReserveFlg || (!empty($data_load['reserve_status']) && $data_load['reserve_status'] == 9)){
	$isDisable = true;

}
//過去予約データ
if ((!empty($data_load['reserve_status']) && $TimeFlag)){
	$isDisable = true;
	$TimeFlag = false;
}
?>
<?= $this->Html->css('jquery-ui') ?>
<style>
    label.error {
        color: #e84c3d;
    }
    .radio_validate{
        margin-left: 10px;
    }
</style>
<h2 id="page-title" class="clearfix">
    <div class="page-title-inner">
        <span class="en">CONCIERGE</span>
        <span class="jp">コンシェルジュ</span>
    </div>
</h2>
<section id="concierge" class="section-container">
    <div class="section-inner">
        <div class="section-contents">
            <section class="main-cont">
                <h2 class="title-01">コンシェルジュ相談申し込み</h2>
                <div class="user_search">
                    <div class="row m-b15">
                        <div class="u-col txt-15">予約日　： </div>
                        <div class="u-col txt-20">
                            <label><?= h($arr_info['work_date']) ?></label>
                        </div>
                        <div class="u-col txt-20 tcenter hidden-mobile"></div>
                        <div class="u-col txt-40 hidden-mobile">
                        </div>
                    </div>
                    <div class="row m-b15">
                        <div class="u-col txt-15">開始時刻　：</div>
                        <div class="u-col txt-20">
                            <label><?= h($arr_info['work_time_start']) ?></label>
                        </div>
                        <div class="u-col txt-20 tcenter">予約者名：</div>
                        <div class="u-col txt-40">
                            <label><?= h($arr_info['user_name']) ?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="u-col txt-15">コンシェルジュ　：</div>
                        <div class="u-col txt-20">
                            <label><?= h($arr_info['concierge_name']) ?></label>
                        </div>
                        <div class="u-col txt-20 tcenter">会員種別：</div>
                        <div class="u-col txt-40">
                            <label><?= $arr_info['user_type'] ?></label>
                        </div>
                    </div>
                </div>
                <div class="txct-total wrap-frm">
                    <?php if(!$TimeFlag): ?>
                    <?php if(!$ReserveFlg): ?>
                    <h5>事前問診　：</h5>
                    <?php endif;?>
                    <?php endif;?>
                    <?= $this->Form->create(null, ['id' => 'submit-form']) ?>
                    <?= $this->Form->hidden('step', ['value' => 2, 'id' => 'step']) ?>
                    <?= $this->Form->hidden('reserves', ['value' => $data_load['reserves'], 'id' => 'reserves']) ?>
                    <?= $this->Form->hidden('work_date', ['value' => date('Y-m-d', strtotime($data_load['work_date']))]) ?>
                    <?= $this->Form->hidden('work_time_start', ['value' => $data_load['work_time_start']]) ?>
                    <?= $this->Form->hidden('work_time_end', ['value' => $data_load['work_time_end']]) ?>
                    <?= $this->Form->hidden('concierge_id', ['value' => $data_load['concierge_id']]) ?>
                    <?= $this->Form->hidden('concierge_name', ['value' => $data_load['concierge_name']]) ?>
                    <?= $this->Form->hidden('user_id', ['value' => $data_load['user_id']]) ?>
                    <?= $this->Form->hidden('user_name', ['value' => $data_load['user_name']]) ?>
                    <?= $this->Form->hidden('user_type', ['value' => $data_load['user_type']]) ?>
                    <?php if($isDisable): ?>
                        <?php if($TimeFlag || $ReserveFlg): ?>
                        <div class="show-time"><?= $this->Flash->render() ?></div>
                        <?php else: ?>
                        <h6>コンシェルジュサービスをどのようにして知りましたか</h6>
                        <p>
                            <?php
                            if ($data_load['question8_1']) {
                                echo $arr_question1[$data_load['question8_1']];
                            }
                            if ($data_load['question8_1'] == '3') {
                                if ($data_load['question8_2']) {
                                    $question8_2= explode(',', $data_load['question8_2']);
                                    if (count($question8_2)) {
                                        echo '<br/>';
                                        foreach ($question8_2 as $r_s) {
                                            if ($r_s == '4') {
                                                echo '　　' . $arr_question2[$r_s] . '　　' . $data_load['question8_2text'] . '<br/>';
                                            } else {
                                                echo '　　' . $arr_question2[$r_s] . '<br/>';
                                            }
                                        }
                                    }
                                }
                            } else if ($data_load['question8_1'] == '4') {
                                if ($data_load['question8_3']) {
                                    $question8_3= explode(',', $data_load['question8_3']);
                                    if (count($question8_3)) {
                                        echo '<br/>';
                                        foreach ($question8_3 as $r_s) {
                                            if ($r_s == '4') {
                                                echo '　　' . $arr_question3[$r_s] . '　　' . $data_load['question8_3text'] . '<br/>';
                                            } else {
                                                echo '　　' . $arr_question3[$r_s] . '<br/>';
                                            }
                                        }
                                    }
                                }
                            } else if ($data_load['question8_1'] == '5') {
                                if ($data_load['question8_4']) {
                                    $question8_4 = explode(',', $data_load['question8_4']);
                                    if (count($question8_4)) {
                                        echo '<br/>';
                                        foreach ($question8_4 as $r_s) {
                                            if ($r_s == '5') {
                                                echo '　　' . $arr_question4[$r_s] . '　　' . $data_load['question8_4text'] . '<br/>';
                                            } else {
                                                echo '　　' . $arr_question4[$r_s] . '<br/>';
                                            }
                                        }
                                    }
                                }
                            } else if ($data_load['question8_1'] == '6') {
                                echo '　　' . $data_load['question8_1text'];
                            }
                            ?>
                        </p>
                        <ol>
                            <li class="parent-caret">
                                <p class="bold">起業への意思決定状況について教えてください。</p>
                                <p><?= isset($arr_question5[$data_load['question1_1']]) ? $arr_question5[$data_load['question1_1']] : '' ?></p>
                            </li>
                            <li class="parent-caret">
                                <p class="bold">現在のお仕事の状況について教えてください。</p>
                                <p><?= isset($arr_question6[$data_load['question2_1']]) ? $arr_question6[$data_load['question2_1']] : '' ?></p>
                            </li>
                            <li>
                                <p class="bold">起業に関する現在の悩みを教えてください。（複数回答可）</p>
                                <p>
                                    <?php
                                    if ($data_load['question3_1']) {
                                        $question3_1 = explode(',', $data_load['question3_1']);
                                        if (count($question3_1)) {
                                            foreach ($question3_1 as $r) {
                                                if ($r == '5') {
                                                    echo $arr_question7[$r] . '<br/>';
                                                    if ($data_load['question3_2']) {
                                                        $question3_2 = explode(',', $data_load['question3_2']);
                                                        if (count($question3_2)) {
                                                            foreach ($question3_2 as $r_s) {
                                                                if ($r_s == '5') {
                                                                    echo '　　' . $arr_question8[$r_s] . '　　' . $data_load['question3_2text'] . '<br/>';
                                                                } else {
                                                                    echo '　　' . $arr_question8[$r_s] . '<br/>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else if ($r == '9') {
                                                    echo $arr_question7[$r] . '　　' . $data_load['question3_1text'] . '<br/>';
                                                } else {
                                                    echo $arr_question7[$r] . '<br/>';
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </p>
                            </li>
                            <li>
                                <p class="bold">想定している顧客層について教えてください。</p>
                                <p><?= isset($arr_question9[$data_load['question4_1']]) ? $arr_question9[$data_load['question4_1']] : '' ?></p>
                            </li>

                            <li>
                                <p class="bold">業種について教えてください。（複数回答可）</p>
                                <p><?php
                                    if ($data_load['question6_1']) {
                                        $question6_1 = explode(',', $data_load['question6_1']);
                                        if (count($question6_1)) {
                                            foreach ($question6_1 as $r) {
                                                if ($r != '14') {
                                                    echo $arr_question10[$r] . '<br/>';
                                                } else {
                                                    echo $arr_question10[$r] . '　　' . $data_load['question6_1text'] . '<br/>';
                                                }
                                            }
                                        }
                                    }
                                    if ($data_load['question5_1text']) {
                                        echo '<br/>あなたが考えている事業の概略を可能な範囲で記入してください<br/>' . nl2br($data_load['question5_1text']);
                                    }
                                    ?>
                                </p>
                            </li>
                            <li>
                                <p class="bold">第三者に見せるための事業計画（書）の作成状況について教えてください。</p>
                                <p><?= isset($arr_question11[$data_load['question7_1']]) ? $arr_question11[$data_load['question7_1']] : '' ?></p>
                            </li>
                            <li>
                                <p class="bold">今回の相談の概要を教えてください。</p>
                                <p><?= !empty($data_load['question9_1text']) ? nl2br(h($data_load['question9_1text'])) : '' ?></p>
                            </li>
                            <?php if(!empty($data_load['user_type']) && $data_load['user_type']==2): ?>
                            <div class="preentre-update-mgl-15">
                                <p class="bold">
                                    プレアントレ更新希望
                                    <span><?=(!empty($data_load['preentre_update']) && $data_load['preentre_update'] == '1') ? '希望' : 'なし'?></span>
                                </p>
                            </div>
                            <?php endif; ?>
                        </ol>
                        <?php endif; ?>
                    <?php else: ?>
                        <h6>コンシェルジュサービスをどのようにして知りましたか？</h6>
                        <?= $this->Form->hidden('question8_1_validate'); ?>
                        <div class="row m-b15">
                            <input type="radio" name="question8_1" id="question8_1_1"
                                   value="1" <?= ($data_load['question8_1'] == '1') ? 'checked="checked"' : '' ?>>
                            <label for="question8_1_1" class="radio foucus_t"><?= isset($arr_question1['1']) ? $arr_question1['1'] : '' ?></label>
                        </div>
                        <div class="row m-b15">
                            <input type="radio" name="question8_1" id="question8_1_2"
                                   value="2" <?= ($data_load['question8_1'] == '2') ? 'checked="checked"' : '' ?>>
                            <label for="question8_1_2" class="radio foucus_t"><?= isset($arr_question1['2']) ? $arr_question1['2'] : '' ?></label>
                        </div>
                        <div class="row m-b15">
                            <input type="radio" name="question8_1" id="question8_1_3"
                                   value="3" <?= ($data_load['question8_1'] == '3') ? 'checked="checked"' : '' ?>>
                            <label for="question8_1_3" class="radio foucus_t"><?= isset($arr_question1['3']) ? $arr_question1['3'] : '' ?></label>

                            <ul class="list-checking">
                                <li>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question8_2[]',
                                        'id' => 'question8_2_1',
                                        'value' => '1',
                                        'checked' => (strpos($question8_2, ',1,') !== false) ? 'checked="checked"' : '',
                                        'label' => isset($arr_question2['1']) ? $arr_question2['1'] : '',
                                    ]); ?>
                                </li>
                                <li>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question8_2[]',
                                        'id' => 'question8_2_2',
                                        'value' => '2',
                                        'checked' => (strpos($question8_2, ',2,') !== false) ? 'checked="checked"' : '',
                                        'label' => isset($arr_question2['2']) ? $arr_question2['2'] : '',
                                    ]); ?>
                                </li>
                                <li>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question8_2[]',
                                        'id' => 'question8_2_3',
                                        'value' => '3',
                                        'checked' => (strpos($question8_2, ',3,') !== false) ? 'checked="checked"' : '',
                                        'label' => isset($arr_question2['3']) ? $arr_question2['3'] : '',
                                    ]); ?>
                                </li>
                                <li>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question8_2[]',
                                        'id' => 'question8_2_4',
                                        'value' => '4',
                                        'checked' => (strpos($question8_2, ',4,') !== false) ? 'checked="checked"' : '',
                                        'label' => isset($arr_question2['4']) ? $arr_question2['4'] : '',
                                    ]); ?>
                                </li>
                                <li><input type="text" name="question8_2text" id="question8_2text" class="foucus_t text w160"
                                           value="<?= $data_load['question8_2text'] ?>" />
                                </li>
                                <div class="radio_validate">
                                    <?= $this->Form->hidden('question8_2_validate'); ?>
                                </div>
                            </ul>
                        </div>
                        <div class="row m-b15">
                            <input type="radio" name="question8_1" id="question8_1_4"
                                   value="4" <?= ($data_load['question8_1'] == '4') ? 'checked="checked"' : '' ?>>
                            <label  class="radio foucus_t" for="question8_1_4"><?= isset($arr_question1['4']) ? $arr_question1['4'] : '' ?></label>

                            <ul class="list-checking">
                                <li>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question8_3[]',
                                        'id' => 'question8_3_1',
                                        'value' => '1',
                                        'checked' => (strpos($question8_3, ',1,') !== false) ? 'checked="checked"' : '',
                                        'label' => isset($arr_question3['1']) ? $arr_question3['1'] : '',
                                    ]); ?>
                                </li>
                                <li>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question8_3[]',
                                        'id' => 'question8_3_2',
                                        'value' => '2',
                                        'checked' => (strpos($question8_3, ',2,') !== false) ? 'checked="checked"' : '',
                                        'label' => isset($arr_question3['2']) ? $arr_question3['2'] : '',
                                    ]); ?>
                                </li>
                                <li>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question8_3[]',
                                        'id' => 'question8_3_3',
                                        'value' => '3',
                                        'checked' => (strpos($question8_3, ',3,') !== false) ? 'checked="checked"' : '',
                                        'label' => isset($arr_question3['3']) ? $arr_question3['3'] : '',
                                    ]); ?>
                                </li>
                                <li>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question8_3[]',
                                        'id' => 'question8_3_4',
                                        'value' => '4',
                                        'checked' => (strpos($question8_3, ',4,') !== false) ? 'checked="checked"' : '',
                                        'label' => isset($arr_question3['4']) ? $arr_question3['4'] : '',
                                    ]); ?>
                                </li>
                                <li><input type="text" name="question8_3text" id="question8_3text" class="foucus_t text w160"
                                           value="<?= $data_load['question8_3text'] ?>" />
                                </li>
                                <div class="radio_validate">
                                    <?= $this->Form->hidden('question8_3_validate'); ?>
                                </div>
                            </ul>
                        </div>
                        <div class="row m-b15">
                            <input type="radio" name="question8_1" id="question8_1_5"
                                   value="5" <?= ($data_load['question8_1'] == '5') ? 'checked="checked"' : '' ?>>
                            <label class="radio foucus_t" for="question8_1_5"><?= isset($arr_question1['5']) ? $arr_question1['5'] : '' ?></label>

                            <ul class="list-checking">
                                <li>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question8_4[]',
                                        'id' => 'question8_4_1',
                                        'value' => '1',
                                        'checked' => (strpos($question8_4, ',1,') !== false) ? 'checked="checked"' : '',
                                        'label' => isset($arr_question4['1']) ? $arr_question4['1'] : '',
                                    ]); ?>
                                </li>
                                <li>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question8_4[]',
                                        'id' => 'question8_4_2',
                                        'value' => '2',
                                        'checked' => (strpos($question8_4, ',2,') !== false) ? 'checked="checked"' : '',
                                        'label' => isset($arr_question4['2']) ? $arr_question4['2'] : '',
                                    ]); ?>
                                </li>
                                <li>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question8_4[]',
                                        'id' => 'question8_4_3',
                                        'value' => '3',
                                        'checked' => (strpos($question8_4, ',3,') !== false) ? 'checked="checked"' : '',
                                        'label' => isset($arr_question4['3']) ? $arr_question4['3'] : '',
                                    ]); ?>
                                </li>
                                <li>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question8_4[]',
                                        'id' => 'question8_4_4',
                                        'value' => '4',
                                        'checked' => (strpos($question8_4, ',4,') !== false) ? 'checked="checked"' : '',
                                        'label' => isset($arr_question4['4']) ? $arr_question4['4'] : '',
                                    ]); ?>
                                </li>
                                <li>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question8_4[]',
                                        'id' => 'question8_4_5',
                                        'value' => '5',
                                        'checked' => (strpos($question8_4, ',5,') !== false) ? 'checked="checked"' : '',
                                        'label' => isset($arr_question4['5']) ? $arr_question4['5'] : '',
                                    ]); ?>
                                </li>
                                <li><input type="text" name="question8_4text" id="question8_4text" class="foucus_t text w80"
                                           value="<?= $data_load['question8_4text'] ?>" />
                                </li>
                                <div class="radio_validate">
                                    <?= $this->Form->hidden('question8_4_validate'); ?>
                                </div>
                            </ul>
                        </div>
                        <div class="row m-b15">
                            <input type="radio" name="question8_1" id="question8_1_6"
                                   value="6" <?= ($data_load['question8_1'] == '6') ? 'checked="checked"' : '' ?>>
                            <label class="radio foucus_t m-r15" for="question8_1_6"><?= isset($arr_question1['6']) ? $arr_question1['6'] : '' ?></label>
                            <input type="text" name="question8_1text" id="question8_1text" class="foucus_t text w160"
                                   value="<?= $data_load['question8_1text'] ?>" />
                        </div>
                        <ol>
                            <li class="parent-caret">
                                <p class="bold">起業への意思決定状況について教えてください。</p>
                                <div class="select half foucus_t w600">
                                    <?= $this->Form->select('question1_1', ['0' => ''] + $arr_question5, [
                                        'label' => false,
                                        'value' => !empty($data_load['question1_1']) ? $data_load['question1_1'] : '0',
                                        'default' => '0',
                                        'id' => 'question1',
                                        'class' => 'select half',
                                    ]);
                                    ?>
                                </div>
                            </li>
                            <li class="parent-caret">
                                <p class="bold">現在のお仕事の状況について教えてください。</p>
                                <div class="select half foucus_t w600">
                                    <?= $this->Form->select('question2_1', ['0' => ''] + $arr_question6, [
                                        'label' => false,
                                        'value' => !empty($data_load['question2_1']) ? $data_load['question2_1'] : '0',
                                        'default' => '0',
                                        'id' => 'question2',
                                        'class' => 'select half',
                                    ]);
                                    ?>
                                </div>
                            </li>
                            <li>
                                <p class="bold">起業に関する現在の悩みを教えてください。（複数回答可）</p>
                                <?= $this->Form->hidden('question3_1_validate'); ?>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question3_1[]',
                                        'id' => 'question3_1_1',
                                        'value' => '1',
                                        'checked' => (strpos($question3_1, ',1,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question7['1']) ? $arr_question7['1'] : '',
                                    ]); ?>
                                </p>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question3_1[]',
                                        'id' => 'question3_1_2',
                                        'value' => '2',
                                        'checked' => (strpos($question3_1, ',2,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question7['2']) ? $arr_question7['2'] : '',
                                    ]); ?>
                                </p>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question3_1[]',
                                        'id' => 'question3_1_3',
                                        'value' => '3',
                                        'checked' => (strpos($question3_1, ',3,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question7['3']) ? $arr_question7['3'] : '',
                                    ]); ?>
                                </p>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question3_1[]',
                                        'id' => 'question3_1_4',
                                        'value' => '4',
                                        'checked' => (strpos($question3_1, ',4,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question7['4']) ? $arr_question7['4'] : '',
                                    ]); ?>
                                </p>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question3_1[]',
                                        'id' => 'question3_1_5',
                                        'value' => '5',
                                        'checked' => (strpos($question3_1, ',5,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question7['5']) ? $arr_question7['5'] : '',
                                    ]); ?>
                                <ul class="list-checking">
                                    <li class="no-margin-bottom">
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question3_2[]',
                                            'id' => 'question3_2_1',
                                            'value' => '1',
                                            'checked' => (strpos($question3_2, ',1,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question8['1']) ? $arr_question8['1'] : '',
                                        ]); ?>
                                    </li>
                                    <li class="no-margin-bottom">
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question3_2[]',
                                            'id' => 'question3_2_2',
                                            'value' => '2',
                                            'checked' => (strpos($question3_2, ',2,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question8['2']) ? $arr_question8['2'] : '',
                                        ]); ?>
                                    </li>
                                    <li class="no-margin-bottom">
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question3_2[]',
                                            'id' => 'question3_2_3',
                                            'value' => '3',
                                            'checked' => (strpos($question3_2, ',3,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question8['3']) ? $arr_question8['3'] : '',
                                        ]); ?>
                                    </li>
                                    <li class="no-margin-bottom">
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question3_2[]',
                                            'id' => 'question3_2_4',
                                            'value' => '4',
                                            'checked' => (strpos($question3_2, ',4,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question8['4']) ? $arr_question8['4'] : '',
                                        ]); ?>
                                    </li>
                                    <li class="no-margin-bottom">
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question3_2[]',
                                            'id' => 'question3_2_5',
                                            'value' => '5',
                                            'checked' => (strpos($question3_2, ',5,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question8['5']) ? $arr_question8['5'] : '',
                                        ]); ?>
                                    </li>
                                    <li class="no-margin-bottom"><input type="text" name="question3_2text" id="question3_2text" class="foucus_t text w160"
                                                                        value="<?= $data_load['question3_2text'] ?>"/>
                                    </li>
                                    <div class="radio_validate">
                                        <?= $this->Form->hidden('question3_2_validate'); ?>
                                    </div>
                                </ul>
                                </p>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question3_1[]',
                                        'id' => 'question3_1_6',
                                        'value' => '6',
                                        'checked' => (strpos($question3_1, ',6,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question7['6']) ? $arr_question7['6'] : '',
                                    ]); ?>
                                </p>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question3_1[]',
                                        'id' => 'question3_1_7',
                                        'value' => '7',
                                        'checked' => (strpos($question3_1, ',7,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question7['7']) ? $arr_question7['7'] : '',
                                    ]); ?>
                                </p>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question3_1[]',
                                        'id' => 'question3_1_8',
                                        'value' => '8',
                                        'checked' => (strpos($question3_1, ',8,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question7['8']) ? $arr_question7['8'] : '',
                                    ]); ?>
                                </p>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question3_1[]',
                                        'id' => 'question3_1_9',
                                        'value' => '9',
                                        'checked' => (strpos($question3_1, ',9,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question7['9']) ? $arr_question7['9'] : '',
                                    ]); ?>
                                    <input type="text" name="question3_1text" id="question3_1text" class="foucus_t text w160 m-l15"
                                           value="<?= $data_load['question3_1text'] ?>"/>
                                </p>
                            </li>
                            <li>
                                <p class="bold">想定している顧客層について教えてください。</p>
                                <div class="select half foucus_t w600">
                                    <?= $this->Form->select('question4_1', ['0' => ''] + $arr_question9, [
                                        'label' => false,
                                        'value' => !empty($data_load['question4_1']) ? $data_load['question4_1'] : '0',
                                        'default' => '0',
                                        'id' => 'question4_1',
                                        'class' => 'select half',
                                    ]);
                                    ?>
                                </div>
                            </li>
                            <li>
                                <p class="bold">業種について教えてください。（複数回答可）</p>
                                <?= $this->Form->hidden('question6_1_validate'); ?>
                                <p>
                                <ul class="list-checking list-checking2" style="margin: 10px 0 0 0px;">
                                    <li>
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question6_1[]',
                                            'id' => 'question6_1_1',
                                            'value' => '1',
                                            'checked' => (strpos($question6_1, ',1,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question10['1']) ? $arr_question10['1'] : '',
                                        ]); ?>
                                    </li>
                                    <li>
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question6_1[]',
                                            'id' => 'question6_1_2',
                                            'value' => '2',
                                            'checked' => (strpos($question6_1, ',2,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question10['2']) ? $arr_question10['2'] : '',
                                        ]); ?>
                                    </li>
                                    <li>
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question6_1[]',
                                            'id' => 'question6_1_3',
                                            'value' => '3',
                                            'checked' => (strpos($question6_1, ',3,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question10['3']) ? $arr_question10['3'] : '',
                                        ]); ?>
                                    </li>
                                    <li>
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question6_1[]',
                                            'id' => 'question6_1_4',
                                            'value' => '4',
                                            'checked' => (strpos($question6_1, ',4,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question10['4']) ? $arr_question10['4'] : '',
                                        ]); ?>
                                    </li>
                                    <li>
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question6_1[]',
                                            'id' => 'question6_1_5',
                                            'value' => '5',
                                            'checked' => (strpos($question6_1, ',5,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question10['5']) ? $arr_question10['5'] : '',
                                        ]); ?>
                                    </li>
                                    <li>
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question6_1[]',
                                            'id' => 'question6_1_6',
                                            'value' => '6',
                                            'checked' => (strpos($question6_1, ',6,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question10['6']) ? $arr_question10['6'] : '',
                                        ]); ?>
                                    </li>
                                </ul>
                                <ul class="list-checking list-checking2" style="margin: 10px 0 0 0px;">
                                    <li>
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question6_1[]',
                                            'id' => 'question6_1_7',
                                            'value' => '7',
                                            'checked' => (strpos($question6_1, ',7,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question10['7']) ? $arr_question10['7'] : '',
                                        ]); ?>
                                    </li>
                                    <li>
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question6_1[]',
                                            'id' => 'question6_1_8',
                                            'value' => '8',
                                            'checked' => (strpos($question6_1, ',8,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question10['8']) ? $arr_question10['8'] : '',
                                        ]); ?>
                                    </li>
                                    <li>
                                        <?= $this->Html->shtFrontCheckBox([
                                            'name' => 'question6_1[]',
                                            'id' => 'question6_1_9',
                                            'value' => '9',
                                            'checked' => (strpos($question6_1, ',9,') !== false) ? 'checked' : '',
                                            'label' => isset($arr_question10['9']) ? $arr_question10['9'] : '',
                                        ]); ?>
                                    </li>
                                </ul>
                                </p>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question6_1[]',
                                        'id' => 'question6_1_10',
                                        'value' => '10',
                                        'checked' => (strpos($question6_1, ',10,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question10['10']) ? $arr_question10['10'] : '',
                                    ]); ?>
                                </p>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question6_1[]',
                                        'id' => 'question6_1_11',
                                        'value' => '11',
                                        'checked' => (strpos($question6_1, ',11,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question10['11']) ? $arr_question10['11'] : '',
                                    ]); ?>
                                </p>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question6_1[]',
                                        'id' => 'question6_1_12',
                                        'value' => '12',
                                        'checked' => (strpos($question6_1, ',12,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question10['12']) ? $arr_question10['12'] : '',
                                    ]); ?>
                                </p>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question6_1[]',
                                        'id' => 'question6_1_13',
                                        'value' => '13',
                                        'checked' => (strpos($question6_1, ',13,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question10['13']) ? $arr_question10['13'] : '',
                                    ]); ?>
                                </p>
                                <p>
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'question6_1[]',
                                        'id' => 'question6_1_14',
                                        'value' => '14',
                                        'checked' => (strpos($question6_1, ',14,') !== false) ? 'checked' : '',
                                        'label' => isset($arr_question10['14']) ? $arr_question10['14'] : '',
                                    ]); ?>
                                    <input type="text" name="question6_1text" id="question6_1text" class="foucus_t text w160 m-l15"
                                           value="<?= $data_load['question6_1text'] ?>" />
                                </p>
                                <br/>
                                <div>あなたが考えている事業の概略を可能な範囲で記入してください</div>
                                <?= $this->Form->input('question5_1text', [
                                    'label' => false,
                                    'type' => 'textarea',
                                    'value' => $data_load['question5_1text'],
                                    'id' => 'question5_1text',
                                    'rows' => '5',
                                    'style' => "resize:both;",
                                    'class' => 'wpcf7-form-control wpcf7-textarea text',
                                ]);
                                ?>
                            </li>
                            <li>
                                <p class="bold">第三者に見せるための事業計画（書）の作成状況について教えてください。</p>
                                <div class="select half foucus_t w600">
                                    <?= $this->Form->select('question7_1', ['0' => ''] + $arr_question11, [
                                        'label' => false,
                                        'value' => !empty($data_load['question7_1']) ? $data_load['question7_1'] : '0',
                                        'default' => '0',
                                        'id' => 'question7_1',
                                        'class' => 'select half',
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
                                        'value' => $data_load['question9_1text'],
                                        'id' => 'question9_1text',
                                        'rows' => '5',
                                        'style' => "resize:both;",
                                        'class' => 'wpcf7-form-control wpcf7-textarea text',
                                    ]);
                                    ?>
                                </div>
                            </li>
                            <?php if(!empty($data_load['user_type']) && $data_load['user_type']==2): ?>
                            <li>
                                <p class="bold">プレアントレメンバー更新希望</p>
                                <div style="width: 100%;border-width:1px; border-style:solid;padding:10px;border-color:red;">
                                <p class="text" style="">
                                    ・プレアントレメンバーは有効期限が三か月間です。<br>
                                    継続してプレアントレメンバーをご希望される場合は【有効期限満了月】にコンシェルジュと面談いただき、起業の意思、進捗の状況を確認させていただきます。
                                    期限は承認された月の翌月から「三か月間」となります。（例：3月に承認された場合、6月中に面談を実施いただく必要があります）<br>
                                    ・継続のための更新面談を希望される場合、「該当月」に下記「更新を希望する」にチェックをいれ、お申込みください。<br>
                                    また、更新面談は期限満了後もお受けいただくことは可能です。<br>
                                </p>
                            </div></br>
                                <div class="parent-caret">
                                    <?= $this->Html->shtFrontCheckBox([
                                        'name' => 'preentre_update',
                                        'id' => 'preentre_update',
                                        'value' => '1',
                                        'checked' => (!empty($data_load['preentre_update']) && $data_load['preentre_update'] == '1') ? 'checked' : '',
                                        'label' => '更新を希望する',
                                    ]); ?>
                                </div>
                            </li>
                            <?php endif; ?>
                        </ol>
                    <?php endif; ?>
                    <div class="btn-block btn-frm">
                        <?php
                        $value_text = '利用規約・個人情報取り扱いに同意し申し込む';
                        if($his=='111') {
                            $value_text = '内容を修正する';
                            if($dt!='') {
                                if(time()<strtotime($dt)) {
                                    $value_text = '内容を修正する';
                                } else {
                                    $value_text = '内容を修正する';
                                }
                            }
                        } else {
                            $value_text = '申し込み内容を確認';
                        }

                        $url_back = '#';
                        if ($his == '111') {
                            $session = $this->request->session();
                            $queryRedirect['page'] = !empty($session->read('search111.page')) ? $session->read('search111.page') : 1;
                            $url_back = $this->Url->build(["controller" => "Reserve", "action" => "index", '?' => $queryRedirect]);
                        } else if ($his == '104') {
                            $url_back = $this->Url->build(["prefix" => "concierge", "controller" => "Index", "action" => "index", "?" => ["id" => $data_load['reserves'], "dt" => date("Y-m-d", strtotime($data_load['work_date']))]]);
                        } else if ($his == '107') {
                            $url_back = $this->Url->build(["prefix" => "concierge", "controller" => "Schedule", "action" => "month", "?" => ["id" => $data_load['reserves'], "dt" => date("Y-m-d", strtotime($data_load['work_date']))]]);
                        } else if ($his == '108') {
                            $url_back = $this->Url->build(["prefix" => "concierge", "controller" => "Schedule", "action" => "week", "?" => ["id" => $data_load['reserves'], "dt" => date("Y-m-d", strtotime($data_load['work_date']))]]);
                        } else if ($his == '106') {
                            $url_back = $this->Url->build(["prefix" => "concierge", "controller" => "Schedule", "action" => "day", "?" => ["id" => $data_load['reserves'], "dt" => date("Y-m-d", strtotime($data_load['work_date']))]]);
                        }
                        ?>
                        <div class="btn w160 h60 icon-none back btn-medium">
                            <div class="btn-inner clear">
                                <a class="overlay-text" id="reset-btn" href="<?=$url_back ?>">
                                    <span class="text en">戻る</span>
                                </a>
                                <div class="line"></div>
                                <div class="line2"></div>
                            </div>
                        </div>
                        <?php if(!$isDisable): ?>
                        <div  class="btn btn-long">
                            <div class="btn-inner black">
                                <button onclick="<?= $isSupperUser ? '$("#submit-form").submit(); return false;' : 'return submitForm();' ?>" >
                                    <span class="text en"><?=$value_text?></span>
                                </button>
                                <div class="line"></div>
                                <div class="line2"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?= $this->Form->end(); ?>
                </div>
            </section>
        </div>
    </div>
</section>
<?= $this->Html->script('jquery-ui') ?>
<?= $this->Html->script('jquery.validate.min.js') ?>
<?= $this->Html->script('tabs.js') ?>
<?= $this->Html->script('pop-up.js') ?>
<?php if(!$isSupperUser): ?>
<script>
    function submitForm(){
        if($('#submit-form').valid()){
            $('#submit-form').submit();
        } else {
            var st = $(".btn-long").offset().top;
            var l = $("label.error").length;
            for(var i=0; i<l; i++){
                if($("label.error:eq("+i+")").offset().top < st) st = $("label.error:eq("+i+")").offset().top;
            }
            st = st - 100;
            $('html, body').animate({
                scrollTop: st
            }, 600);
        }
        return false;
    }
    $(document).ready(function () {
        var requiredMessage = "※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_004')?>";
        $.validator.addMethod("question_8_1_required", function (value, element) {
            return $('input[name="question8_1"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("question_8_2_required", function (value, element) {
            return  $('input[name="question8_2[]"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("question_8_3_required", function (value, element) {
            return $('input[name="question8_3[]"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("question_8_4_required", function (value, element) {
            return $('input[name="question8_4[]"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("question_3_1_required", function (value, element) {
            return $('input[name="question3_1[]"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("question_3_2_required", function (value, element) {
            return $('input[name="question3_2[]"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("question_6_1_required", function (value, element) {
            return $('input[name="question6_1[]"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("select_box_not_empty", function (value, element) {
            return this.optional(element) || (value.length > 0 && parseInt(value) > 0);
        },requiredMessage);
        $("#submit-form").validate({
            ignore: [],
            rules: {
                /*
                question8_1_validate: {
                    question_8_1_required: true,
                },
                */
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
                <?php if(!empty($data_load['user_type']) && $data_load['user_type']==2): ?>
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
                /*
                question3_1_validate: {
                    question_3_1_required: true,
                },
                */
                question3_2_validate: {
                    question_3_2_required: function(){
                        return $('#question3_1_5').prop('checked') == true;
                    },
                },
                /*
                question4_1: {
                    select_box_not_empty: true,
                },
                question6_1_validate: {
                    question_6_1_required: true,
                },
                question7_1: {
                    select_box_not_empty: true,
                },
                */
            },
        });
    });

</script>
<?php endif; ?>
