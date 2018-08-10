<?php

use Cake\Core\Configure;

$arr_user_types = Configure::read('USER_TYPES');
$arr_interest = Configure::read('INTEREST_CODE');
$arr_preparations = Configure::read('PREPARATIONS');
$arr_pref = Configure::read('PREFECTURE_CODES');
$arr_intention = Configure::read('PREENTRE_INTENTION_OPTION');
$arr_business = Configure::read('PREENTRE_BUSINESS_OPTION');
$arr_business_style = Configure::read('PREENTRE_BUSINESS_STYLE_OPTION');
$arr_problem = Configure::read('PREENTRE_PROBLEM_OPTION');
$arr_wish = Configure::read('PREENTRE_WISH_OPTION');
$str_year_old = '';
if ($user->birthday) {
    $str_year_old =(int)date('Y') - (int)date_format($user->birthday, 'Y');
    if ($str_year_old) {
        $str_year_old = $str_year_old . '歳';
    }
}
$str_problem = '';
for ($i = 1; $i <= 22; $i++) {
    $col = ($i < 10) ? '0' . $i : $i;
    if ($user->PreentreRequests['problem' . $col] == '1') {
        $str_problem .= $arr_problem[$col] . '<br/>';
    }
}
if ($user->PreentreRequests['problem99'] == '1') {
    $str_problem .= $arr_problem['99'] . '<br/>';
}
if ($user->PreentreRequests['problem_text']) {
    $str_problem .= $user->PreentreRequests['problem_text'];
}

$str_wish = '';
for ($i = 1; $i <= 8; $i++) {
    $col = ($i < 10) ? '0' . $i : $i;
    if ($user->PreentreRequests['wish' . $col] == '1') {
        $str_wish .= $arr_wish[$col] . '<br/>';
    }
}
if ($user->PreentreRequests['wish']) {
    $col = ($user->PreentreRequests['wish'] < 10) ? '0' . $user->PreentreRequests['wish'] : $user->PreentreRequests['wish'];
    $str_wish .= $arr_wish[$col];
}
echo $this->Html->css('scrolltabs.css');
?>

<div class="the-title">
    <h2 class="title-01">カルテ詳細</h2>
</div>

<div class="show-time">
    <?= $this->Flash->render('flash') ?>
</div>
<?php
$count =count($reserves->toArray());

if (  $count < 6) { ?>
<ul class="tabs">
    <li class="active"
        onclick="javascript:location.href = '<?= $this->Url->build(["controller" => "CounselNote", "action" => "info", "?" => ["id" => $user_id, 'cn' => $cn]]) ?>'">
        基本情報
    </li>
    <?php

        foreach ($reserves as $row) {
            $url = $this->Url->build(["controller" => "CounselNote", "action" => "note", "?" => ["id" => $user->id, "rs" => $row['id']]]);
            echo '<li onclick="javascript:location.href=\'' . $url . '\'"> ' . strftime('%Y/%m/%d', strtotime($row['work_date'])) . ' ' . substr_replace($row['work_time_start'], ':', 2, 0) . '</li>';
        }

    ?>
</ul>
<?php } else if ($count>=6) { ?>
    <div class="indented_text" style="display: none;">
        <div id="tabs" class="scroll_tabs_theme_dark">
            <ul class="tabs scroll-tabs">

                <li class="active"
                    onclick="javascript:location.href = '<?= $this->Url->build(["controller" => "CounselNote", "action" => "info", "?" => ["id" => $user_id, 'cn' => $cn]]) ?>'">
                    基本情報
                </li>
                <?php
                if ( count($reserves) ) {
                    foreach ($reserves as $row) {
                        $url = $this->Url->build(["controller" => "CounselNote", "action" => "note", "?" => ["id" => $user->id, "rs" => $row['id']]]);
                        echo '<li onclick="javascript:location.href=\'' . $url . '\'"> ' . strftime('%Y/%m/%d', strtotime($row['work_date'])) . ' ' . substr_replace($row['work_time_start'], ':', 2, 0) . '</li>';
                    }
                }
                ?>

            </ul>
        </div>
    </div>
    <?php
    echo $this->Html->script('jquery.scrolltabs');
    echo $this->Html->script('jquery.mousewheel');
    ?>
    <script>
        $(document).ready(function () {

            tabs = $('#tabs').scrollTabs();
            $('.indented_text').show();
            tabs.scrollSelectedIntoView();
        });

    </script>
    <?php

}
?>
<?php
 $sty='display: block';
 if ($count<6)
     $sty='display: block;border-top:1px solid #000 !important;';
?>
<div style="clear:both"></div>
<div class="tab_container">
    <div id="tabs-one"  class="tab_content" style="<?= $sty?>">
        <div class="swap-left fm-left">
            <div class="swap-spacing">
                <p><span>会員番号</span>：<span><?= str_pad( $user->id, 9, '0', STR_PAD_LEFT) ?></span></p>
                <p>
                    <span>会員種別</span>：<span><?= isset($arr_user_types[$user->type]) ? $arr_user_types[$user->type] : '' ?></span>
                </p>
                <p><span>氏名</span>：<span><?= $user->name_last ?>　<?= $user->name_first ?></span></p>
                <p><span>年齢</span>：<span><?= $str_year_old ?></span></p>
                <p><span>国籍</span>：<span><?= $user->nationality ?></span></p>
                <p><span>都道府県</span>：<span><?= isset($arr_pref[$user->pref])?$arr_pref[$user->pref]:'' ?></span></p>
            </div>
        </div>

        <div class="swap-right fm-right">
            <div class="swap-spacing">
                <p><span>登録日</span>：<span><?= strftime('%Y/%m/%d', $user->created_at) ?></span></p>
                <p>
                    <span>起業への興味</span>：<span><?= isset($arr_interest[$user->interest])?$arr_interest[$user->interest]:'' ?></span>
                </p>
                <p>
                    <span>起業への準備</span>：<span><?= isset($arr_preparations[$user->preparation]) ? $arr_preparations[$user->preparation] : '' ?></span>
                </p>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="swap-element">
            <h4>プレアントレ問診：</h4>
            <?php if(empty($user->PreentreRequests['id'])): ?>
                申請はありません。
            <?php else: ?>
                <p>
                    <span>起業への意思</span><span><?= isset($arr_intention[$user->PreentreRequests['intention']]) ? $arr_intention[$user->PreentreRequests['intention']] : '' ?></span>
                </p>
                <p><span>どういった事業をお考えですか？</span><span>
              <?php
              if ($user->PreentreRequests['business_type'] != '8') {
                  echo isset($arr_business[$user->PreentreRequests['business_type']]) ? $arr_business[$user->PreentreRequests['business_type']] : '';
              } else {
                  echo $user->PreentreRequests['business_type_text'];
              }
              ?></span></p>
                    <p><span>起業スタイルはどのようにお考えですか？</span><span>
              <?php
              if ($user->PreentreRequests['business_style'] != '7') {
                  echo isset($arr_business_style[$user->PreentreRequests['business_style']]) ? $arr_business_style[$user->PreentreRequests['business_style']] : '';
              } else {
                  echo $user->PreentreRequests['business_style_text'];
              }
              ?></span></p>
                <p><span>現在どういったお悩みを持っていますか？</span><span><?= $str_problem ?></span></p>
                <p><span>希望するサービス</span><span><?= $str_wish ?></span></p>
            <?php endif; ?>
        </div>
    </div><!-- #tab1 -->
</div> <!-- .tab_container -->
