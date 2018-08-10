<?php
use Cake\Core\Configure;

echo $this->Html->css('jquery-ui');
$arr_child_purpose = Configure::read('CHILD_PURPOSE');
$arr_child_approval = Configure::read('CHILD_APPROVAL');
$arr_day_of_week = Configure::read('DAYOFWEEK');
$AUTH=3;
$date =$currentTime;
$currentdate=new DateTime($currentTime);
$month_start = strtotime('first day of this month', strtotime($date));
$month_end = strtotime('last day of this month', strtotime($date));
$dayofweek_start = date('w', $month_start);
$date_start_month = date('j', $month_start);
$date_end_month = date('j', $month_end);


$d = new DateTime($currentTime);
$d->modify('first day of previous month');
$yearPrv = $d->format('Y'); //2012
$monthPrv = $d->format('m'); //12
$d = new DateTime($currentTime);
$d->modify('first day of next month');
$yearNext = $d->format('Y');
$monthNext = $d->format('m');
$now = new \DateTime();
?>
<?= $this->Html->script('jquery-ui')?>
<?= $this->Html->script('jquery.validate.min.js')?>
<style>
    .disabled {
        color: #d3d3d3;
    }
</style>
<h2 class="title-01">託児予約 実施日設定</h2>
<div class="show-time">
    <?= $this->Flash->render() ?>
    <div class="clearfix"></div>
    <ul class="fm-left fm-line">
        <li><span><?php echo  date('Y年m月', $month_start); ?></span></li>
        <li>
            <a href="<?php echo $this->Url->build(["controller" => "Nursery","action" => "setting",'?'=>[ 'dt'=> $yearPrv.'-'.$monthPrv.'-01']]);?>"><</a>
        </li>
        <li>
            <a href="<?php echo $this->Url->build(["controller" => "Nursery","action" => "setting",'?' =>['dt' => $yearNext.'-'.$monthNext.'-01']]);?>">></a>
        </li>
    </ul>
    <div class="clearfix"></div>
</div>
<?= $this->Form->create(null,['class'=>'','id'=>'nurseryfrm','method'=>'post','url'=>''.'?dt='.date('Y-m-d',strtotime($currentTime))]) ?>
<div class="content-calender">
    <table class="table-calendar ad-table">
        <tr>
            <th>月</th>
            <th>火</th>
            <th>水</th>
            <th>木</th>
            <th>金</th>
            <th>土</th>
            <th>日</th>
        </tr>
        <?php
        if ($dayofweek_start == 0)
            $dayofweek_start = 7;
        $html = '';
        $k = 0;
        for ($i = 0; $i < $dayofweek_start-1; $i++,$k++) {
            if($k%7==0) $html.='<tr>';
            $html.='<td></td>';
        }
        for ($j = $date_start_month; $j <= $date_end_month; $j++,$k++) {
            $_strday = ($j < 10) ? '0' . $j : $j;
            $datavalue=date('Y-m', strtotime('+'.$i.' days', strtotime($currentTime))).'-'. $_strday;
            $url=$this->Url->build([ "controller" => "Schedule","action" => "day",'?' =>['dt'=> $datavalue]]);
            if ($AUTH===0 || $AUTH===4 || $AUTH===5) {
                $d_ = '
          	<div class="fm-alias">
				<span>' . $j . '</span>
				<a href="' . $url . '">詳細 ／ 予約</a>
			</div>
			<p>予約枠：<span id="shift_day_R_'.$j.'">0</span>件</p><p>空き：<span id="shift_day_S_'.$j.'">0</span>件</p>';
            }
            else
            {
                $disable='';$disable_sf='';
                if (date_format($currentdate, 'Y') < $now->format('Y'))
                {$disable= 'disabled';$disable_sf='disabled';}
                else if (date_format($currentdate, 'Y') == $now->format('Y')) {
                    if (date_format($currentdate, 'm') < $now->format('m')){
                        $disable= 'disabled';
                        $disable_sf='disabled';
                    }
                    else {
                        if (date_format($currentdate, 'm') == $now->format('m') && $j<$now->format('d')){
                            $disable= 'disabled';
                            $disable_sf='disabled';
                        }
                    }

                }

                $day_= ($j<10)?'0'.$j:$j;
                $d_ = '
          	<div class="fm-alias">
				<span>' . $j . '</span>

			</div>
            <div class="form-inline">

                <label class="checkbox ad-checkbox '.$disable.'">実施する <input value="0" name="day_'.$day_.'" id="nur_day_s_'.$j.'" '.$disable.' type="checkbox">
                </label>
                  <label class="checkbox ad-checkbox '.$disable_sf.'">時間短縮 <input value="0" name="s_flg_'.$day_.'" id="s_flg_'.$j.'" '.$disable_sf.' type="checkbox">
                </label>
            </div>' ;
            }
            // $class_current = ($j == $day_current) ? 'current_date' : '';
            $class_current='';
            if($k%7==0) $html.='<tr>';
            $html.='<td >' . $d_ . '</td>';
            if($k%7==6) {
                $html.='</tr>';
                $k = -1;
            }
        }
        if($k>0) {
            for ($i = $k; $i < 7; $i++,$k++) {
                $html.='<td></td>';
                if($k%7==6) {
                    $html.='</tr>';
                    break;
                }
            }
        }
        echo $html;
        ?>

    </table>
</div>
<?= $this->Form->end() ?>
<div class="pright">
    <input type="button" value="戻る" name="" onclick="window.location.href='<?php echo $this->Url->build(["controller" => "Nursery","action" => "schedule"]);?>'"  class="btn">
    <input type="button" value="登録" name="" id="btn_submit" class="btn">
</div>
<div id="dialog-submit" style="display:none;">
    <p style="text-align: center; margin-top: 10px; font-weight: bold;">
        予約実施日を登録します。よろしいですか？
    </p>
</div>
<script>
    $(document).ready(function () {

        $('#btn_submit').click(function () {
            $("#dialog-submit").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "OK": function () {
                        $('#nurseryfrm').submit();
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
        });
    });
    function insert(main_string, ins_string, pos) {
        if(typeof(pos) == "undefined") {
            pos = 0;
        }
        if(typeof(ins_string) == "undefined") {
            ins_string = '';
        }
        return main_string.slice(0, pos) + ins_string + main_string.slice(pos);
    }
    function formatDate(date) {
        var str_=  d.getFullYear() +'-' +( (d.getMonth()+1).toString().length==1?'0'+(d.getMonth()+1):(d.getMonth()+1)) +'-'+( (d.getDate()).toString().length==1?'0'+(d.getDate()):(d.getDate()));
        return str_;
    }
    var objNursery = JSON.parse('<?php echo json_encode($nurseryschedule); ?>');

    for (var i = 0; i < objNursery.length; i++) {
        var obj = objNursery[i];
        var d = new Date(obj.reserve_date);
        var day = d.getDate();
        var str_date=formatDate(d);

        var vari = '_day_s_' + day;
        checkbox= $('#nur' + vari);
        var str_day    = day < 10?'0' + day:day;
        if (typeof checkbox !==undefined)
        {
            $(checkbox).val(obj.id);
            $(checkbox).prop("checked", true);

            if (obj.rid !==null && (obj.approval == 1 || obj.approval==0)){
                $(checkbox).parent().html("予約有り").append($(checkbox).attr("disabled", true))
                    .append("<input type='hidden' name='day_"+str_day+"' value='"+obj.id+"'  >");
            }
        }
        var checkbox_sf= $('#s_flg_' + day);
        if (typeof checkbox_sf !==undefined)
        {
           // $(checkbox_sf).val(obj.id);
            if (obj.shorttime_flg==1)
            $(checkbox_sf).prop("checked", true);
            if (obj.rid !==null && (obj.approval == 1 || obj.approval==0)){
                /*if (obj.shorttime_flg==1)*/
                $(checkbox_sf).parent().html("時間短縮").append(  $(checkbox_sf).attr("disabled", true)).append("<input type='hidden' name='s_flg_"+str_day+"' value='"+obj.id+"'  >");
            }
        }
    }
</script>
