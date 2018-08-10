<style>
    .preentre-update-mgl-15{
        margin-left: -15px;
    }
    .preentre-update-mgl-15 span{
        margin-left: 10px;
    }
    .wrap-frm span.bold {
        font-size: 1.6rem;
        font-weight: bold;
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

$arr_info['work_date'] = date('Y/m/d', strtotime($data_save['work_date']));
$arr_info['work_time_start'] = substr($data_save['work_time_start'], 0, 2) . ':' . substr($data_save['work_time_start'], 2, 2);
$arr_info['concierge_name'] = $data_save['concierge_name'];
$arr_info['user_id'] = $data_save['user_id'];
$arr_info['user_name'] = $data_save['user_name'];
$arr_info['user_type'] = isset($arr_user_types[$data_save['user_type']]) ? $arr_user_types[$data_save['user_type']] : '';
?>
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
                <h2 class="title-01">コンシェルジュ相談申し込み(確認)</h2>
				<div style="margin: 20px; color=red;">
					<p class="text red" style="font-weight: bold;">※まだ予約は完了していません。</p>
				</div>
                <div class="user_search">
                    <div class="row m-b15">
                        <div class="u-col txt-15">予約日　：</div>
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
                <?php if($his=='106' || ($his=='111' && $dt!='' && time()>=strtotime($dt))) { ?>
                <div>
                    <input class="button btn-109" type="button" onclick="location.href='<?php echo $this->Url->build(["prefix" => "concierge", "controller" => "Schedule", "action" => "day", "?" => ["id" => $data_save['concierge_id'], "dt" => date("Y-m-d", strtotime($data_save['work_date']))]]); ?>'" value="コンシェルジュと時間を選ぶ"/>
                </div>
                <?php } ?>
                <div class="txct-total wrap-frm">
                    <h5>事前問診　：</h5>
                    <?= $this->Form->create(null, ['id' => 'submit-form']) ?>
                    <?= $this->Form->hidden('step', ['value' => 3, 'id' => 'step']) ?>
                    <h6>コンシェルジュサービスをどのようにして知りましたか</h6>
                    <p>
                        <?php
                        if ($data_save['question8_1']) {
                            echo $arr_question1[$data_save['question8_1']];
                        }
                        if ($data_save['question8_1'] == '3') {
                            if ($data_save['question8_2']) {
                                $question8_2= explode(',', $data_save['question8_2']);
                                if (count($question8_2)) {
                                    echo '<br/>';
                                    foreach ($question8_2 as $r_s) {
                                        if ($r_s == '4') {
                                            echo '　　' . $arr_question2[$r_s] . '　　' . $data_save['question8_2text'] . '<br/>';
                                        } else {
                                            echo '　　' . $arr_question2[$r_s] . '<br/>';
                                        }
                                    }
                                }
                            }
                        } else if ($data_save['question8_1'] == '4') {
                            if ($data_save['question8_3']) {
                                $question8_3= explode(',', $data_save['question8_3']);
                                if (count($question8_3)) {
                                    echo '<br/>';
                                    foreach ($question8_3 as $r_s) {
                                        if ($r_s == '4') {
                                            echo '　　' . $arr_question3[$r_s] . '　　' . $data_save['question8_3text'] . '<br/>';
                                        } else {
                                            echo '　　' . $arr_question3[$r_s] . '<br/>';
                                        }
                                    }
                                }
                            }
                        } else if ($data_save['question8_1'] == '5') {
                            if ($data_save['question8_4']) {
                                $question8_4 = explode(',', $data_save['question8_4']);
                                if (count($question8_4)) {
                                    echo '<br/>';
                                    foreach ($question8_4 as $r_s) {
                                        if ($r_s == '5') {
                                            echo '　　' . $arr_question4[$r_s] . '　　' . $data_save['question8_4text'] . '<br/>';
                                        } else {
                                            echo '　　' . $arr_question4[$r_s] . '<br/>';
                                        }
                                    }
                                }
                            }
                        } else if ($data_save['question8_1'] == '6') {
                            echo '　　' . $data_save['question8_1text'];
                        }
                        ?>
                    </p>
                    <ol>
                        <li class="parent-caret">
                            <p class="bold">起業への意思決定状況について教えてください。</p>
                            <p><?= isset($arr_question5[$data_save['question1_1']]) ? $arr_question5[$data_save['question1_1']] : '' ?></p>
                        </li>
                        <li class="parent-caret">
                            <p class="bold">現在のお仕事の状況について教えてください。</p>
                            <p><?= isset($arr_question6[$data_save['question2_1']]) ? $arr_question6[$data_save['question2_1']] : '' ?></p>
                        </li>
                        <li>
                            <p class="bold">起業に関する現在の悩みを教えてください。（複数回答可）</p>
                            <p>
                                <?php
                                if ($data_save['question3_1']) {
                                    $question3_1 = explode(',', $data_save['question3_1']);
                                    if (count($question3_1)) {
                                        foreach ($question3_1 as $r) {
                                            if ($r == '5') {
                                                echo $arr_question7[$r] . '<br/>';
                                                if ($data_save['question3_2']) {
                                                    $question3_2 = explode(',', $data_save['question3_2']);
                                                    if (count($question3_2)) {
                                                        foreach ($question3_2 as $r_s) {
                                                            if ($r_s == '5') {
                                                                echo '　　' . $arr_question8[$r_s] . '　　' . $data_save['question3_2text'] . '<br/>';
                                                            } else {
                                                                echo '　　' . $arr_question8[$r_s] . '<br/>';
                                                            }
                                                        }
                                                    }
                                                }
                                            } else if ($r == '9') {
                                                echo $arr_question7[$r] . '　　' . $data_save['question3_1text'] . '<br/>';
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
                            <p><?= isset($arr_question9[$data_save['question4_1']]) ? $arr_question9[$data_save['question4_1']] : '' ?></p>
                        </li>

                        <li>
                            <p class="bold">業種について教えてください。（複数回答可）</p>
                            <p><?php
                                if ($data_save['question6_1']) {
                                    $question6_1 = explode(',', $data_save['question6_1']);
                                    if (count($question6_1)) {
                                        foreach ($question6_1 as $r) {
                                            if ($r != '14') {
                                                echo $arr_question10[$r] . '<br/>';
                                            } else {
                                                echo $arr_question10[$r] . '　　' . $data_save['question6_1text'] . '<br/>';
                                            }
                                        }
                                    }
                                }
                                if ($data_save['question5_1text']) {
                                    echo '<br/>あなたが考えている事業の概略を可能な範囲で記入してください<br/>' . nl2br($data_save['question5_1text']);
                                }
                                ?>
                            </p>
                        </li>
                        <li>
                            <p class="bold">第三者に見せるための事業計画（書）の作成状況について教えてください。</p>
                            <p><?= isset($arr_question11[$data_save['question7_1']]) ? $arr_question11[$data_save['question7_1']] : '' ?></p>
                        </li>
                        <li>
                            <p class="bold">今回の相談の概要を教えてください。</p>
                            <p><?= !empty($data_save['question9_1text']) ? nl2br(h($data_save['question9_1text'])) : '' ?></p>
                        </li>
                        <?php if(!empty($data_save['user_type']) && $data_save['user_type']==2): ?>
                            <li>
                                <p class="bold">プレアントレメンバー更新希望</p>
                                <div class="parent-caret">

                                        <?= (!empty($data_save['preentre_update']) && $data_save['preentre_update'] == '1') ? '更新を希望する' : ''?>
                                </div>
                            </li>

                        <?php endif; ?>

                    </ol>
                    <div class="btn-block btn-frm">
                        <div class="btn w160 h60 icon-none back btn-medium">
                            <div class="btn-inner clear">
                                <a class="overlay-text" id="reset-btn" role="button" onclick="$('#step').val(1);$('#submit-form').submit(); return false;">
                                    <span class="text en">戻る</span>
                                </a>
                                <div class="line"></div>
                                <div class="line2"></div>
                            </div>
                        </div>
                        <div  class="btn btn-long">
                            <div class="btn-inner black">
                                <button onclick="$('#step').val(3);$('#submit-form').submit(); return false;" >
                                    <span class="text en">申し込み</span>
                                </button>
                                <div class="line"></div>
                                <div class="line2"></div>
                            </div>
                        </div>
                    </div>  
                    <?= $this->Form->end() ?>
                </div>
            </section>
        </div>
    </div>
</section>
