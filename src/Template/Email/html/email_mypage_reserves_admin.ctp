<span style="display: none;"><?php echo $user_name;?> 様<br/>
<br/></span>
<?php use Cake\Core\Configure; ?>
相談申し込みがありました <br/>
<br/>
予約日：	<?php echo $work_date;?><br/>
時間： <?php echo $work_time_start . '～' . $work_time_end;?><br/>
担当者： <?php echo $pre_concierge_name;?><br/>
会員：  <?php echo $user_gender ; ?>    <?php echo $user_birthday ; ?>歳 <br/>
会員番号： <?php echo $user_id; ?> <br/>
問診内容：<br/>
<?php if(!empty($data_save)): ?>
    <?php
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
    <div>
        <p style="font-weight: bold">コンシェルジュサービスをどのようにして知りましたか</p>
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
    </div>
    <div>
        <div>
            <p style="font-weight: bold">1. 起業への意思決定状況について教えてください。</p>
            <p><?= isset($arr_question5[$data_save['question1_1']]) ? $arr_question5[$data_save['question1_1']] : '' ?></p>
        </div>
        <div>
            <p style="font-weight: bold">2. 現在のお仕事の状況について教えてください。</p>
            <p><?= isset($arr_question6[$data_save['question2_1']]) ? $arr_question6[$data_save['question2_1']] : '' ?></p>
        </div>
        <div>
            <p style="font-weight: bold">3. 起業に関する現在の悩みを教えてください。（複数回答可）</p>
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
        </div>
        <div>
            <p style="font-weight: bold">4. 想定している顧客層について教えてください。</p>
            <p><?= isset($arr_question9[$data_save['question4_1']]) ? $arr_question9[$data_save['question4_1']] : '' ?></p>
        </div>

        <div>
            <p  style="font-weight: bold">5. 業種について教えてください。（複数回答可）</p>
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
        </div>
        <div>
            <p style="font-weight: bold">6. 第三者に見せるための事業計画（書）の作成状況について教えてください。</p>
            <p><?= isset($arr_question11[$data_save['question7_1']]) ? $arr_question11[$data_save['question7_1']] : '' ?></p>
        </div>
        <div>
            <p style="font-weight: bold">7. 今回の相談の概要を教えてください。</p>
            <p><?= !empty($data_save['question9_1text']) ? nl2br(h($data_save['question9_1text'])) : '' ?></p>
        </div>
        <?php if(!empty($data_save['user_type']) && $data_save['user_type']==2): ?>
        <div>
            <p style="font-weight: bold">8. プレアントレメンバー更新希望</p>
            <p> <?= (!empty($data_save['preentre_update']) && $data_save['preentre_update'] == '1') ? '更新を希望する' : ''?></p>
        </div>
        <?php endif; ?>

    </div>
<?php endif; ?>

<br/>
<br/>
ご確認よろしくお願いいたします。<br/>
<br/>
※本メールにお心当たりのない方は、お手数ですが、破棄していただけますようお願い申し上げます。 <br/>
※このメールに直接返信いただきましても、システム上お問い合わせを受付することが 出来かねます。 <br/>
　お問い合わせは、StartupHubTokyoのHP「問い合わせフォームからご連絡くださいますようお願いいたします。 <br/>
<hr>
　Startup Hub Tokyo（スタートアップハブトウキョウ）<br/>
　〒100－0005<br/>
　東京都千代田区丸の内2-1-1明治安田生命ビル1階<br/>
　TOKYO創業ステーション　Startup Hub Tokyo運営事務局<br/>
<hr>
