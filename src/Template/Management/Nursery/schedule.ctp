<?php
use Cake\Core\Configure;

echo $this->Html->css('jquery-ui');
$arr_child_purpose = Configure::read('CHILD_PURPOSE');
$arr_child_approval = Configure::read('CHILD_APPROVAL');
$arr_day_of_week = Configure::read('DAYOFWEEK');
?>
<style>
    a.limit_date, a.cancel_limit_date {
        cursor: pointer;
        text-decoration: none;
    }
    a.limit_date:hover,a.limit_date:focus, a.cancel_limit_date:hover,a.cancel_limit_date:focus {
        text-decoration: none;
    }
</style>
<?= $this->Html->script('jquery-ui')?>
<?= $this->Html->script('jquery.validate.min.js')?>
<?=$this->Html->css('simple.calendar.css') ?>
<?=$this->Html->script('simple.calendar.nursery') ?>
<h2 class="title-01">託児予約 実施日一覧</h2>
<div class="show-time">
    <?= $this->Flash->render() ?>
</div>
<div class="row row-center">
    <!--
    <input type="button" value="申込予約一覧"  onclick="window.location.href='<?php echo $this->Url->build(["controller" => "Nursery","action" => "reserve"]);?>'" name="" class="btn">
-->
    <input type="button" style="float: right" value="実施日設定" name="" onclick="window.location.href='<?php echo $this->Url->build(["controller" => "Nursery","action" => "setting"]);?>'" class="btn">
</div>

<!-- Res Table -->
<div class="res-tbl">
    <table class="tbl tbl-two-col" cellpadding="0" cellspacing="0" summary="新規登録">
        <thead>
        <tr>
            <th scope="col">実施予定日</th>
            <th scope="col">予約期限日</th>
            <th scope="col">キャンセル期限日</th>
            <th scope="col"></th>

        </tr>
        </thead>
        <tbody>
        <?php foreach ($nurseryschedule as $item) : ?>
        <tr>
            <td data-title="実施予定日"><?= $item->reserve_date ? date("Y年m月d日", strtotime($item->reserve_date)) : '' ?>（<?=$arr_day_of_week[date("D", strtotime($item->reserve_date))]?>）</td>
            <td> <a class="limit_date" data-id="<?=$item->id?>" > <?= $item->limit_date ? date("Y年m月d日", strtotime($item->limit_date)) : '' ?>（<?=$arr_day_of_week[date("D", strtotime($item->limit_date))]?>）</a>
                <div id="dev_limit_date_<?=$item->id?>"  >
                <input style="border: none;display: none" data-func="change_date" data-id="<?=$item->id?>" readonly="readonly" id="limit_date_<?=$item->id?>" name="limit_date" value="<?=date_format( $item->limit_date, 'Y/m/d') ?>">
                </div >
            </td>
            <td data-title="キャンセル期限日">
                <a class="cancel_limit_date"  data-id="<?=$item->id?>" >   <?= $item->cancel_limit_date ? date("Y年m月d日", strtotime($item->cancel_limit_date)) : '' ?>（<?=$arr_day_of_week[date("D", strtotime($item->cancel_limit_date))]?>）</a>
                <div id="dev_cancel_limit_date_<?=$item->id?>"  >
                    <input style="border: none;display: none" data-func="change_cancel_limit"  data-id="<?=$item->id?>" readonly="readonly" id="cancel_limit_date_<?=$item->id?>" name="cancel_limit_date" value="<?=date_format( $item->cancel_limit_date, 'Y/m/d') ?>">
                </div >
            </td>
            <td data-title="">
                <?php if (!$item->rid): ?>

                    <button type="button" data-id="<?=$item->id?>" class="fm-smit btn_delete" onclick="cancelStatus(1);">削除</button>
                <?php else:?>
                    <span>予約有り</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach;?>


        </tbody>
    </table>

</div>
<!-- End Res Table -->

<div id="dialog-cancel" style="display:none;">
    <p style="text-align: center; margin-top: 10px; font-weight: bold;">
        予約実施日を削除します。よろしいですか？
    </p>
</div>


<div id="dialog-change" style="display:none;">
    <p style="text-align: center; margin-top: 10px; font-weight: bold;">予約期限日を変更します。よろしいですか？</p>
</div>

<div id="dialog-limit_date" style="display:none;">
    <p style="text-align: center; margin-top: 10px; font-weight: bold;">キャンセル期限日を変更します。よろしいですか？</p>
</div>

<?= $this->Form->create(null,['class'=>'','id'=>'nurseryfrm','method'=>'post','url'=>'']) ?>
<input type="hidden" value="0" name="nursery_schedule_id" class="" id="nursery_schedule_id">
<input type="hidden" value="1" name="cancel_nursery" id="cancel_nursery" />
<?= $this->Form->end() ?>

<?= $this->Form->create(null,['class'=>'','id'=>'change_date','method'=>'post','url'=>'']) ?>
<input type="hidden" value="0" name="nursery_schedule_id" class="" id="nursery_schedule_id">
<input type="hidden" value="0" name="limit_date" class="" id="limit_date">
<input type="hidden" value="1" name="change_date" id="change_date" />
<?= $this->Form->end() ?>

<?= $this->Form->create(null,['class'=>'','id'=>'cancel_limit_datefrm','method'=>'post','url'=>'']) ?>
<input type="hidden" value="0" name="nursery_schedule_id" class="" id="nursery_schedule_id">
<input type="hidden" value="0" name="cancel_limit_date" class="" id="cancel_limit_date">
<input type="hidden" value="1" name="cancel_date" id="cancel_date" />
<?= $this->Form->end() ?>


<script >

    $(document).ready(function () {
        <?php foreach ($nurseryschedule as $item) : ?>
        calendar.set("limit_date_<?=$item->id?>");
        calendar.set('cancel_limit_date_<?=$item->id?>');
        <?php endforeach;?>
        $('a.limit_date').click(function () {
            $('#limit_date_'+ $(this).attr('data-id')).trigger('click');
        });
        $('a.cancel_limit_date').click(function () {

            $('#cancel_limit_date_'+ $(this).attr('data-id')).trigger('click');
        });

        $('.btn_delete').click(function () {
            $('#nurseryfrm #nursery_schedule_id').val($(this).attr('data-id'));
            $("#dialog-cancel").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "OK": function () {

                        $('#nurseryfrm').submit();
                    },
                    //Cancel
                    'Cancel':function () {

                        $(this).dialog("close");
                    }
                },
                closeOnEscape: false,
                open: function (event, ui) {
                    $(".ui-dialog-titlebar", ui.dialog | ui).hide();
                }
            });
        });
    });

    function change_date(id,value) {

        $('#change_date #nursery_schedule_id').val(id);
        $('#limit_date').val(value);
        $("#dialog-change").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                "OK": function () {
                    $(this).dialog("close");

                    $('#change_date').submit();
                },
                //Cancel
                'キャンセル':function () {

                    $(this).dialog("close");
                }
            },
            closeOnEscape: false,
            open: function (event, ui) {
                $(".ui-dialog-titlebar", ui.dialog | ui).hide();
            }
        });

    }
    function change_cancel_limit(id,value) {
        $('#cancel_limit_datefrm #nursery_schedule_id').val(id);
        $('#cancel_limit_date').val(value);
        $("#dialog-limit_date").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                "OK": function () {
                    $(this).dialog("close");

                    $('#cancel_limit_datefrm').submit();
                },
                //Cancel
                'キャンセル':function () {

                    $(this).dialog("close");
                }
            },
            closeOnEscape: false,
            open: function (event, ui) {
                $(".ui-dialog-titlebar", ui.dialog | ui).hide();
            }
        });
    }
</script>
