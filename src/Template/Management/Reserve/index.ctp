<?=$this->Html->css('simple.calendar.css') ?>
<?=$this->Html->script('simple.calendar') ?>
<?php
use Cake\Core\Configure;

echo $this->Html->css('jquery-ui');
?>
<?= $this->Html->script('jquery-ui')?>
<?= $this->Html->script('jquery.validate.min.js')?>
<style>
    /*
    .text-date
    {
        background-image: url(url to calender icon);
        background-repeat: no-repeat;
    }
    */
    .w-20-p{
        width: 20% !important;
    }
    #csv-upload{
        margin-left: 20px;
    }
</style>
<?php if($reason == 1) { ?>

<h2 class="title-01">予約キャンセルフォーム</h2>
<div class="show-time"><?= $this->Flash->render() ?></div>

<div class="fm-text">
    <p>以下の予約をキャンセルします。</p>
    <p>理由を入力し、「送信」ボタンを押下してください</p>
    <ul>
        <li><span>利用者名</span>:<span id=""><?php echo isset($arrRe['name'])?$arrRe['name']:'' ?></span></li>
        <li><span>予約日</span>:<span id=""><?php echo isset($arrRe['date'])?$arrRe['date']:'' ?></span></li>
        <li><span>時間</span>:<span id=""><?php echo isset($arrRe['time'])?$arrRe['time']:'' ?></span></li>
        <li><span>担当者</span>:<span id=""><?php echo isset($arrRe['con'])?$arrRe['con']:'' ?></span></li>
    </ul>
    <p>理由</p>
</div>

<?= $this->Form->create(null,['class'=>'','id'=>'reasonfrm','method'=>'post','url'=>'']) ?>
<div class="fm-form">
    <form action="">
        <textarea name="comment" id="comment" cols="30" rows="10"></textarea>
        <div class="col-md-6 msg_error" style="color:red;"></div>
        <div class="pright">
            <input type="button" value="キャンセル" name="" class="btn" id="cancel">
            <input type="button" value="実行" name="update" class="btn" id="update"
            onclick="submitUpdateForm(); return false;">
            <input type="hidden" value="<?php echo isset($arrRe['rid'])?$arrRe['rid']:'' ?>" name="hid_rid" class="" id="hid_rid">
            <input type="hidden" value="<?php echo isset($arrRe['stat'])?$arrRe['stat']:'' ?>" name="hid_stat" class="" id="hid_stat">
        </div>
    </form>
</div>
<?= $this->Form->end() ?>

<?php } else { ?>

<h2 class="title-01">予約カルテ一覧</h2>

<?= $this->Form->create(null,['class'=>'','id'=>'namefrm','method'=>'post','url'=>'']) ?>
<div class="user_search">
    <div class="row m-b15">
        <div class="u-col txt-15">氏名かな</div>
        <div class="u-col txt-80">
            <input class="txt-40" type="text" value="<?=$username?>" id="name1-0" name="username" />
        </div>
    </div>
    <div class="row m-b15">
        <div class="u-col txt-15">予約日付</div>
        <div class="u-col txt-80">
            <input class="txt-15 text-date" type="text" value="<?=$timeStart?>" name="time_start" id="time_start" readonly/>
            &nbsp;～&nbsp;
            <input class="txt-15 text-date" type="text" value="<?=$timeEnd?>" name="time_end" id="time_end" readonly/>
            <?php if($edit == 'cena') { ?>
                <input class="btn pright" type="submit" value="検索" name="search"><span class="pright">&nbsp;&nbsp;</span><input class="btn pright" type="button" value="クリア" onclick="clearSearchCondition();">

            <?php } ?>
        </div>
    </div>
    <?php if($edit == 'see') { ?>
    <div class="row">
        <div class="u-col txt-15">コンシェルジュを絞り込む</div>
        <div class="u-col txt-80">
            <?=$this->Form->select('concierges',$arrConcier,['id'=>'concierges','class' => "",'value' => $concierge]);?>
            <input class="btn pright" type="submit" value="検索" name="search"><span class="pright">&nbsp;&nbsp;</span><input class="btn pright" type="button" value="クリア" onclick="clearSearchCondition();">
        </div>
    </div>
    <?php } ?>
</div>
<?= $this->Form->end() ?>
<div class="flash-message flash-error msg_error" onclick="$(this).hide();" style="display:none;"></div>
<div class="show-time"><?= $this->Flash->render() ?></div>
<?= $this->Form->create(null,['class'=>'','id'=>'confrm','method'=>'post','url'=>'']) ?>
<?php if($edit == 'see') { ?>
<div class="row m-b15">
    <?php if(!empty($loginInfo) && $loginInfo['AUTH'] != 1 && $loginInfo['AUTH'] != 5): ?>
        <input type="button" value="CSVダウンロード" name="" class="btn" id='csv'>
        <a href="/management/importreserve" class="btn" id="csv-upload">CSVアップロード</a>
    <?php endif; ?>
    <div class="minfrm-right">

    </div>
</div>
<?php } ?>
<?= $this->Form->end() ?>

<?= $this->Form->create(null,['class'=>'','id'=>'csvfrm','method'=>'post','url'=>'']) ?>
<div class="row">
    <?php if($edit == 'see'): ?>
    <div class="min-frm-left h-checkbox w-20-p">
    	<input type="checkbox" class="check" value="" id="01-00" /><label for="01-00">全てチェックをつける／はずす</label>
    </div>
        <?php if(!empty($loginInfo) && $loginInfo['AUTH'] != 1 && $loginInfo['AUTH'] != 5): ?>
            <div class="min-frm-left h-checkbox w-20-p">
                <input type="checkbox" name="download_all" value="1" id="download_all" /><label for="download_all">検索結果をすべてダウンロード</label>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div class="pager">
        <p class="pagerlist">
        <?= $this->Html->shtCommonPaging($this->Paginator, $pagingConfig); ?></p>
    </div>
</div>
    <?= $this->Form->hidden('time_use', ['value' => json_encode($countUsers)]); ?>
<!-- Res Table -->
<div class="res-tbl fix-left">
    <table class="tbl" cellpadding="0" cellspacing="0" summary="新規登録">
        <thead>
            <tr>
                <?php if($edit == 'see') { ?>
                <th scope="col">チェック</th>
                <?php } ?>
                <th scope="col">予約日付</th>
                <th scope="col">時間</th>
                <th scope="col">予約区分</th>
                <th scope="col">コンシェルジュ</th>
                <th scope="col">利用者名</th>
                <th scope="col">性別</th>
                <th scope="col">年齢</th>
                <th scope="col">利用回数</th>
                <th scope="col">プレアントレ<br/> 更新希望</th>
                <th scope="col">ステータス</th>
                <th scope="col">カルテ</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($concierges as $item) { ?>
            <tr>
            <?php if($edit == 'see') { ?>
                <td scope="row">
                    <input class="lim-check"
                    data-uid="<?= $item->user_id ? $item->user_id : '' ?>"
                    data-rid="<?= $item->rid ? $item->rid : '' ?>"
                    data-date="<?= $item->work_date ? date("Y/m/d", strtotime($item->work_date)) : '' ?>"
                    data-name="<?php echo ($item->name_last || $item->name_first) ? $item->name_last . ' ' . $item->name_first : '';?>"
                    data-time="<?= $item->work_time_start ? substr_replace($item->work_time_start, ':', 2, 0) : '' ?>"
                    data-cname="<?=$item->name ? $item->name : ''?>"
                    data-stat="<?php
                                    $val = '';
                                    if ($item->r_status!=9) {
                                        $val = 'r_' . $item->r_status;
                                    } else {
                                        if ($item->c_status) {
                                            $val = 'c_' . $item->c_status;
                                        }
                                    }
                                    echo isset(Configure::read('QUESTION_STATUS')[$val])?Configure::read('QUESTION_STATUS')[$val]:'';
                                ?>"
                    type="checkbox" name="arrChk[]" value="<?= $item->rid ?>" id="<?= $item->user_id ?>"/></td>
            <?php } ?>
                <?php
                $userName = '';
                $userSex = '';
                $userAge = '';
                if($item->reserve_classification == Configure::read('RESERVE_CLASSIFICATION.FROM_RECEPTION') && (empty($item->csv_user_id) || !is_numeric($item->csv_user_id))){
                    $userName = !empty($item->csv_user_name) ? $item->csv_user_name : '';
                    $userSex = !empty($item->csv_user_sex) ? str_replace('性', '', $item->csv_user_sex) : '';
                    $userAge = !empty($item->csv_user_age) ? str_replace('歳代', '', $item->csv_user_age) : '';
                } else {
                    $userName = ($item->name_last && $item->name_first) ? ($item->name_last . ' ' . $item->name_first) : '' ;
                    $userSex = $item->sex ? Configure::read('SEX')[$item->sex] : '';
                    $userAge = $item->birthday ? date_diff(date_create($item->birthday), date_create('today'))->y : '' ;
                }
                ?>
                <td data-title="予約日付"><?= $item->work_date ? date("Y/m/d", strtotime($item->work_date)) : '' ?></td>
                <td data-title="時間"><?= $item->work_time_start ? substr_replace($item->work_time_start, ':', 2, 0) : '' ?></td>
                <td data-title="予約区分"><?= $item->reserve_classification ? Configure::read('RESERVE_CLASSIFICATION.CLASSIFICATION')[$item->reserve_classification] : '' ?></td>
                <td data-title="コンシェルジュ"><?= $item->name ? $item->name : '' ?></td>
                <td data-title="利用者名"><?= $userName ?></td>
                <td data-title="性別"><?= $userSex ?></td>
                <td data-title="年齢"><?= $userAge ?></td>
                <td data-title="利用回数"><?=$countUsers[$item->user_id]?></td>
                <td data-title="プレアントレ 更新希望"><?= $item->preentre_update==1?'希望':'' ?></td>
                <td data-title="ステータス">
                    <?php
                    $questionStatus = Configure::read('QUESTION_STATUS');
                    $questionStatus['c_1'] = 'キャンセル（ユーザキャンセル）';
                        $val = '';
                        if ($item->r_status!=9) {
                            /*if(empty($item->c_status))*/
                                $val = 'r_' . $item->r_status;
                        } else {
                            if ($item->c_status) {
                                $val = 'c_' . $item->c_status;
                            }
                        }
                        echo isset($questionStatus[$val])?$questionStatus[$val]:'';
                    ?>
                </td>
                <td data-title="カルテ" class="link" data-uid="<?= $item->user_id ?>"><a href="<?= $this->Url->build(['controller' => 'CounselNote', 'action' => 'note', '?' => ['id' => $item->user_id, 'rs' => $item->rid]])?>">表示</a></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- End Res Table -->

<div class="row m-b15">
    <?php if($edit == 'see') { ?>
    <div class="min-frm-left h-checkbox">
    	<input type="checkbox" class="check" value="" id="01-01" /><label for="01-01">全てチェックをつける／はずす</label>
    </div>
    <?php } ?>
    <div class="pager">
        <p class="pagerlist">
        <?= $this->Html->shtCommonPaging($this->Paginator, $pagingConfig); ?></p>
    </div>
</div>

<?php if($edit == 'see') { ?>
<div class="row m-b15">
    <div class="minfrm-left">
    	<label>チェックしたもののステータスを切替える</label>
        <?=$this->Form->select('status',
                    Configure::read('QUESTION_STATUS'),
                    [
                        'empty' => ['' => ''],
                        'default' => '',
                        'class' => "txt-20",
                        'name' => 'status',
                        'id' =>'status'
                    ]
                );?>
        <input type="hidden" value="0" name="hid_csv" class="" id="hid_csv">
        <input type="hidden" value="0" name="hid_importcsv" class="" id="hid_importcsv">
        <?=$this->Form->hidden('csvwhere',['value'=>$csvwhere])?>
    	<input type="button" value="実行" name="" class="btn m-l15" id="status_b">
    </div>
</div>
<?php } ?>
<?= $this->Form->end() ?>

<?= $this->Form->create(null,['class'=>'','id'=>'reafrm','method'=>'post','url'=>'']) ?>
<input type="hidden" value="0" name="reason" class="" id="reason">
<input type="hidden" value="0" name="stat_in" class="" id="stat-in">
<input type="hidden" value="0" name="rid_in" class="" id="rid-in">
<input type="hidden" value="0" name="name_in" class="" id="name-in">
<input type="hidden" value="0" name="time_in" class="" id="time-in">
<input type="hidden" value="0" name="date_in" class="" id="date-in">
<input type="hidden" value="0" name="con_in" class="" id="con-in">
<?= $this->Form->end() ?>

<?php } ?>

<div id="dialog-confirm" title="" style="display:none;">
    <p>
        ステータス変更を実行します。よろしいですか？
    </p>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        calendar.set("time_start");
        calendar.set("time_end");
        //$('#time_start').datepicker();
        //$('#time_end').datepicker();
        //$('#time_start').click(function(){
         //   $('.ui-datepicker').append('<input type="button" class="btn pright" value="Clear"/>');
        //});

        $("#msg").hide();
        $(".check").click(function () {
             $('input:checkbox').not(this).not('#download_all').prop('checked', this.checked);
        });
/*
        $('#concierges').on('change', function() {
            $('#confrm').submit();
        })
*/
        $("#csv").click(function () {
            $("#hid_csv").val(1);
            $('#csvfrm').submit();
        });
        $('#importcsv').click(function(){
            $("#hid_importcsv").val(1);
            $('#csvfrm').submit();
        })
        // $(".link").click(function () {
        //     var url = '<?= $this->Url->build(['controller' => 'CounselNote', 'action' => 'info'])?>' + '/';
        //     window.location.href = url + $(this).attr("data-uid");
        // });

        $("#status_b").click(function () {
            $("#hid_csv").val(0);
            $(".msg_error").html('');
            $(".msg_error").hide();
            $("div.show-time div.message").addClass('hidden');
            if($('#status').val() != '') {
                if($('input[name="arrChk[]"]:checked').length == 0) {
                    $(".msg_error").html('<button type="button" class="close" onclick="$(this).parent().hide();" aria-hidden="true">×</button><p><?= Configure::read('MESSAGE_NOTIFICATION.MSG_021') ?></p>');
                    $(".msg_error").show();
                    var body = $("html, body");
                    body.stop().animate({scrollTop:0}, 500, 'swing', function() {    });
                } else {
                    if($('#status').val() != 'c_9') { //3
                        submitOutForm();
                    } else {
                        if($('input[name="arrChk[]"]:checked').length > 1) {
                            $(".msg_error").html('<button type="button" class="close" onclick="$(this).parent().hide();" aria-hidden="true">×</button><p><?= Configure::read('MESSAGE_NOTIFICATION.MSG_022') ?></p>');
                            $(".msg_error").show();
                            var body = $("html, body");
                            body.stop().animate({scrollTop:0}, 500, 'swing', function() {    });
                        } else { //2
                            $("#reason").val(1);

                            $('input[name="arrChk[]"]:checked').each(function(){
                                $("#rid-in").val($(this).attr('data-rid'));
                                $("#stat-in").val($('#status').val());
                                $("#name-in").val($(this).attr('data-name'));
                                $("#time-in").val($(this).attr('data-time'));
                                $("#date-in").val($(this).attr('data-date'));
                                $("#con-in").val($(this).attr('data-cname'));
                            });
                            $('#reafrm').submit();
                        }
                    }
                }
            } else {
                $("#msg").text('<?= Configure::read('MESSAGE_NOTIFICATION.MSG_021') ?>');
                $("#msg").show();
            }
        });

        obj = '<?php echo json_encode($arrChkRe); ?>';
        var objchk = JSON.parse(obj);
        if(objchk.length > 0) {
            for(var i = 0; i < objchk.length; i++) {
                document.getElementById(objchk[i]).checked = true;
            }
        }

        $("#cancel").click(function () {
            var url = '<?= $this->Url->build(['controller' => 'reserve', 'action' => 'index'])?>';
            window.location.href = url;
        });

        var validator = $("#reasonfrm").validate({
            rules: {
                comment:{
                    required: true
                },
            },
            messages: {
                comment:{
                    required: "<?= Configure::read('MESSAGE_NOTIFICATION.MSG_004') ?>"
                }
            },
            errorPlacement: function(error, element)
            {
                  element.nextAll(".msg_error").html(error);
            },
        });


    });

    function submitUpdateForm() {
        if($( '#reasonfrm').valid()) {
            $("#dialog-confirm").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "OK": function () {
                        $('#reasonfrm').submit();
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
        }
    }

    function submitOutForm() {
        $("#dialog-confirm").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                "OK": function () {
                    $('#csvfrm').submit();
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
    }
    function clearSearchCondition(){
        $('#name1-0').val('');
        $('#time_start').val('');
        $('#time_end').val('');
        $('#concierges').val(-1);
    }

</script>

