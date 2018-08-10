<?php
use Cake\Core\Configure;
$arr_day_of_week = Configure::read('DAYOFWEEK');
$arr_nursery_status =  Configure::read('NURSERY_STATUS');
?>
<style>
    .mh800{
        min-height: 325px;
    }
    .mg-center{
        margin: 0 auto;
    }
    .no-data-message
    {
        padding-top:35px;
        margin-bottom: 100px;
    }
    .tbl th
    {
        width: 25%;
    }
</style>

<link rel="stylesheet" href="<?= $this->Html->getVersion('/css/jquery-ui.css'); ?>" />
<link rel="stylesheet" href="<?= $this->Html->getVersion('/assets-sht2/css/main.css'); ?>" />
<h2 id="page-title" class="clearfix no-margin-bottom">
    <div class="page-title-inner">
        <span class="en">MYPAGE</span>
        <span class="jp">マイページ</span>
    </div>
</h2>
<div id="concierge" class="section-container">
    <div id="mypage">
        <div class="mypage-navi">
            <div class="inner">
                <ul>
                    <li><a href="<?= Configure::read('FUEL_ADMIN_URL') .'mypage/index' ?>">予約・開催済みイベント</a></li>
                    <li><a href="<?= Configure::read('FUEL_ADMIN_URL') .'mypage/profile' ?>">登録情報</a></li>
                    <li><a href="<?= $this->Url->build(['controller' => 'Reserve', 'action'=>'index']) ?>">コンシェルジュ相談申込履歴</a></li>
                    <li class="active"><a href="<?= $this->Url->build(['action'=>'index']) ?>">一時保育サービス予約状況</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="section-inner" style="margin-top: 30px;">
        <div class="section-contents">
            <section class="main-cont">
                <h2 class="title-01"  style="margin-bottom: 0px;">一時保育サービス予約状況</h2>
                <div class="show-time">
                    <?= $this->Flash->render() ?>
                </div>
                <div class="mh800">
                <?php if ($nurseryReserves->count() > 0): ?>
                    <div class="row">
                        <div class="pager">
                            <p class="pagerlist">
                                <?= $this->Html->shtCommonPaging($this->Paginator, $pagingConfig); ?></p>
                            </p>
                        </div>
                    </div>

                    <!-- Res Table -->
                    <div class="res-tbl">
                        <table class="tbl" cellpadding="0" cellspacing="0" summary="新規登録">
                            <thead>
                            <tr>
                                <th scope="col">予約日付</th>
                                <th scope="col">時間</th>
                                <th scope="col">お子様の人数</th>
                                <th scope="col">予約状況</th>
                                <!--
                                <th scope="col">状態</th> -->
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($nurseryReserves as $item):
                            ?>
                                <?php
                                $workDate = $item->reserve_date ? date("Y年m月d日", strtotime($item->reserve_date)) : '';
                                $week = '（' . $arr_day_of_week[date_format($item->reserve_date, 'w')] . '）';
                                $workTimeStart = $item->reserve_time_start ? substr($item->reserve_time_start, 0, 2) . ':' . substr($item->reserve_time_start, 2, 2) : '';
                                $workTimeEnd = $item->reserve_time_end ? substr($item->reserve_time_end, 0, 2) . ':' . substr($item->reserve_time_end, 2, 2) : '';
                                ?>
                                <tr>
                                    <?php
                                    $newStatusName = [
                                        '0' => 'お申込み中',
                                        '1' => '予約中',
                                        '2' => 'キャンセル',
                                    ];
                                    $status = isset($item->status)?$newStatusName[$item->status]:'';
                                    if($item->approval==2 && $item->status == 1) $status = 'キャンセル';
                                    ?>
                                    <td scope="row"><?= $workDate . $week ?></td>
                                    <td data-title="時間"><?= $workTimeStart ?>～<?= $workTimeEnd ?></td>
                                    <td data-title="お子様の人数">
                                        <?php

                                        $child = '';
                                        if (isset($item->sex1) && !empty(isset($item->sex1))) {
                                            $child = 1;
                                        }
                                        if (isset($item->sex2) && !empty(isset($item->sex2))) {
                                            $child = 2;
                                        }
                                        echo $child;
                                        ?>
                                    </td>
                                    <td data-title="予約完了">
                                    <?php
                                        $string = '';
                                        $button='';
                                        $rd = strtotime($item->reserve_date);
                                        $rdt = strtotime($item->reserve_date . ' ' . $workTimeStart);
                                        $current_date =strtotime(date('Y-m-d'));
                                        $current_time = date('H:i');
                                        $threeDaysLater = strtotime(date('Y-m-d', strtotime('+3 days')));
                                        $cancel_limit_date  = strtotime($item->cancel_limit_date);

                                        if ($item->approval == 4)
                                            $string = 'キャンセル';
                                        elseif($item->approval == 3)
                                            $string = 'キャンセル';
                                        elseif ($item->status == 2)
                                            $string = '非承認';
                                        elseif($item->status == 1 && $item->approval == 2)
                                            $string = 'キャンセル';
                                        elseif ($rd<$current_date && $workTimeStart<$current_time &&($item->status== 0 || $item->status== 1 ) && ($item ->approval == 0 || $item ->approval ==1)){
                                            $string = '済';
                                            }
                                        else {
                                            if ($cancel_limit_date >= $current_date){
                                                if ($item->status == 0 && ($item->approval == 0 ||$item->approval == 1)){
                                                    $button ='<button type="button" class="fm-smit" onclick="cancelStatus(' . $item->id . ', 2 ,3 );">キャンセルする</button>';
                                                    $string ='お申し込み中';
                                                }elseif ($item->status == 1 && ($item->approval == 0 ||$item->approval == 1)){
                                                    $button ='<button type="button" class="fm-smit" onclick="cancelStatus(' . $item->id . ',2,4);">キャンセルする</button>';
                                                    $string ='予約中';
                                                }
                                            }
                                            else {
                                                if (($item->status==1 || $item->status == 0) && ($item->approval == 0 ||$item->approval == 1)){
                                                    $string = 'キャンセルできません';
                                                }
                                            }
                                        }
                                        if ( !empty( $button))
                                        echo $button.'<br>';
                                        echo $string;
                                        ?>
                                    </td>
                                    <!--
                                    <td>
                                        <?php

                                        $out = '';
                                        $rd = strtotime($item->reserve_date);
                                        $rdt = strtotime($item->reserve_date . ' ' . $workTimeStart);

                                        $threeDaysLater = strtotime(date('Y-m-d', strtotime('+3 days')));
                                        if($rdt < time()) {
                                            $out = '済';
                                        } else {
                                             /* author: Thovo
                                              * date: 2017-12-14
                                              * Comment code to change new flown
                                              */
                                            /*
                                            if($item->status != 2){
                                                if($item->approval == 0 || $item->approval == 1){
                                                    if($item->status == 1 || $item->status == 0){
                                                        if($rd < $threeDaysLater){
                                                            $out = 'キャンセルできません';
                                                        }else{
                                                            $out = '<button type="button" class="fm-smit" onclick="cancelStatus(' . $item->id . ');">キャンセル</button>';
                                                        }
                                                    }
                                                }elseif($item->approval==2 && $item->status == 1){
                                                    $out = '';
                                                }
                                            }
                                            */
                                            /* Change Status*/
                                            if ($item->status == 1 && $item->cancel_limit_date < date('Y-m-d')){
                                                $out='キャンセルできません';
                                            }elseif ($item->status == 1 && $item->cancel_limit_date >= date('Y-m-d')){
                                                $out = '<button type="button" class="fm-smit" onclick="cancelStatus(' . $item->id . ');">キャンセル</button>';
                                            }
                                            else
                                            {
                                                $out = $arr_nursery_status[$item->status];
                                            }
                                        }
                                        echo $out;

                                        ?>
                                    </td>
                                    -->
                                </tr>
                            <?php
                            endforeach;
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Res Table -->

                    <!-- Pager -->
                    <div class="row">
                        <div class="pager">
                            <p class="pagerlist">
                                <?= $this->Html->shtCommonPaging($this->Paginator, $pagingConfig); ?></p>
                            </p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="res-tbl">
                        <table class="tbl" cellpadding="0" cellspacing="0" summary="新規登録">
                            <thead>
                            <tr>
                                <th scope="col">予約日付</th>
                                <th scope="col">時間</th>
                                <th scope="col">予約状況</th>
                                <th scope="col">状態</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="show-time"><div class="message no-data-message">予約データはありません</div></div>
                <?php endif; ?>
                </div>
                <div class="fm-privacy-container" style="overflow-y: auto; height:auto;">
                    <ul class="fm-infotext">
                        <li>
                            【ご利用場所】

                        </li>
                        <li>「Startup HubTokyo」キッズスペース
                        </li>
                        <li>【ご利用条件】
                            <ul>
                                <ol>①TOKYO創業ステーション（1階・2階）の施設ご利用
                                </ol>
                                <ol>②「Startup HubTokyo」メンバーご登録</ol>
                            </ul>
                        </li>
                        <li>
                            【お申込み方法】
                            <ul>
                                <ol>
                                    ご予約日の6営業日前（土日祝除く）23：59までにMY PAGEにて仮予約申込手続きをしてください。 その後、仮予約メールを事務局よりご送付いたします。 後日、仮予約結果のお知らせメールをもって、ご予約が完了いたします。（ご連絡までは数日いただきます） ご予約状況により、お断りする場合もございますので、予めご了承ください。

                                </ol>
                            </ul>
                        </li>
                        <li>&nbsp</li>
                        <li>【ご予約確定後について】
                            <ul>
                                <ol>
                                    ■キャンセル
                                </ol>
                                <ol>
                                    ご予約日の3営業日前（土日祝除く）23：59までにMY PAGEにてキャンセル手続きをしてください。
                                </ol>
                                <ol>■ご予約時間の変更
                                </ol>
                                <ol>ご予約日の6営業日前（土日祝除く）23：59までにMY PAGEにてキャンセル手続きをして、再度ご希望の日時にてご予約ください。 ご予約日直前及び当日の時間変更はお受け致しかねます。予めご了承ください。
                                </ol>
                                <ol>■当日
                                </ol>
                                <ol>
                                    万一遅れそうな場合は、必ず事務局までお電話にてご連絡をお願いいたします。 その際の時間延長などはお受け致しかねます。 やむを得ない事情にて当日キャンセルされる場合は、かならず予約時間前までにお電話ください。 ご予約時間開始後、10分経ってもご連絡がない場合は、キャンセル扱いとさせていただきます。

                                </ol>
                            </ul>
                        </li>
                        <li>&nbsp</li>
                        <li>Startup Hub Tokyo運営事務局：03-6551-2470
                        </li>

                    </ul>
                </div>
                <div class="btn-list clearfix tcenter">
                    <div id="submit-btn" class="btn mg-center">
                        <div class="btn-inner black">
                            <a id="submit-btn" href="<?= $this->Url->build(['action' => 'form']) ?>">
                                <span class="text">仮予約申込</span>
                            </a>
                            <div class="line"></div>
                            <div class="line2"></div>
                        </div>
                    </div>
                </div>
                <!-- End Pager -->
            </section>
        </div><!-- /.section-contents -->
    </div><!-- /.section-inner -->
</div>

<div class="clearfix"></div>

<?= $this->Form->create(null,['id'=>'cancel-status-frm']) ?>
<div id="dialog-confirm" title="" style="display:none;"><p>キャンセルします。よろしいですか？</p></div>
<?=$this->Form->hidden('nurse_reserve_id',['id'=>'nurse_reserve_id'])?>
<?=$this->Form->hidden('nurse_reserve_status',['id'=>'nurse_reserve_status'])?>
<?=$this->Form->hidden('nurse_reserve_appvoral',['id'=>'nurse_reserve_appvoral'])?>
<?= $this->Form->end() ?>

<script src="<?= $this->Html->getVersion('/js/jquery-ui.js'); ?>"></script>
<script>
    function cancelStatus(id, status, appvoral){
        $('#nurse_reserve_id').val(id);
        $('#nurse_reserve_status').val(status);
        $('#nurse_reserve_appvoral').val(appvoral);
        $("#dialog-confirm").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                "OK": function () {
                    $(this).dialog("close");
                    $('#cancel-status-frm').submit();
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
        return false;
    }
</script>
