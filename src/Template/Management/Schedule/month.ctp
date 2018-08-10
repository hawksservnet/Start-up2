<?php
use Cake\Core\Configure;
echo $this->Html->css('jquery-ui');
$AUTH =(int) $loginInfo['AUTH'];
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
//echo date('Y-m-d', strtotime('first monday of this month', strtotime($currentTime)));
?>

<h2 class="title-01">スケジュール（月）</h2>

<div class="show-time">
    <?= $this->Flash->render('flash') ?>
    <div class="clearfix"></div>
    <ul class="fm-left fm-line">
        <li><span><?php echo  date('Y/m', $month_start); ?></span></li>
        <li>
            <a href="<?php echo $this->Url->build(["controller" => "Schedule","action" => "month",'?'=>[ 'id'=>$id,'dt'=> $yearPrv.'-'.$monthPrv.'-01']]);?>"><</a>
        </li>
        <li>
            <a href="<?php echo $this->Url->build(["controller" => "Schedule","action" => "month",'?' =>['id'=>$id,'dt' => $yearNext.'-'.$monthNext.'-01']]);?>">></a>
        </li>
    </ul>
    <?php if ($AUTH== 0 || $AUTH==4 || $AUTH==5) { ?>

    <ul class="show-rights fm-right">
        <li><span>コンシェルジュを絞り込む</span></li>
        <li>
            <?php
            echo $this->Form->select('concierges', $arrconcierges, [
                'default' => $id,
                'empty' => ['0'=>'すべて'],
                'id' => 'concierges',
                'label' => false]);
            ?>
        </li>
    </ul>

    <?php } ?>
    <div class="clearfix"></div>
    <ul class="show-rights pa-rights fm-right">
        <li><span>表示を切り替える</span></li>
        <li>
            <a href="<?php echo $this->Url->build([ "controller" => "Schedule","action" => "week",'?' => ['id' => $id, 'dt'=>date('Y-m-d',  strtotime($currentDate)) ]]);?>">週</a>
        </li>
        ／

        <li>
            <a href="<?php echo $this->Url->build([ "controller" => "Schedule","action" => "day",'?' => ['id' => $id, 'dt'=>date('Y-m-d', strtotime($currentDate)) ]]);?>">日</a>
        </li>
    </ul>
</div>
<div class="content-calender">
    <table class="table-calendar">
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
			<p>予約済み：<span id="shift_day_R_'.$j.'">0</span>件</p><p>空き：<span id="shift_day_S_'.$j.'">0</span>件</p>';
            }
            else
            {

                $d_ = '
          	<div class="fm-alias">
				<span>' . $j . '</span>
                <a href="' . $url . '">詳細 ／ 予約</a>
			</div>
			<div class="cont-col" id="shift_day_' . $j . '"></div>';
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
<script>
    function insert(main_string, ins_string, pos) {
        if(typeof(pos) == "undefined") {
            pos = 0;
        }
        if(typeof(ins_string) == "undefined") {
            ins_string = '';
        }
        return main_string.slice(0, pos) + ins_string + main_string.slice(pos);
    }
    var Auth='<?php echo $loginInfo['AUTH']; ?>';
    <?php if ($AUTH ==0 || $AUTH==4 || $AUTH==5) {
        ?>
            var objShift = JSON.parse('<?php echo json_encode($queryShift); ?>');
            var objReserves = JSON.parse('<?php echo json_encode($queryReserves); ?>');
            var objShiftTemp = JSON.parse('<?php echo json_encode($queryShiftReseerverTemp); ?>');
            console.log(objShiftTemp);
            for (var i = 0; i < objShift.length; i++) {
                var obj = objShift[i];

                var d = new Date(obj.work_date);
                var day = d.getDate();
                var vari = '_day_S_' + day;
                $('#shift' + vari).html(obj.totalS);
            }
            for (var i = 0; i < objReserves.length; i++) {
                var obj = objReserves[i];
                var d = new Date(obj.work_date);
                var day = d.getDate();
                var vari = '_day_R_' + day;
                $('#shift' + vari).html(obj.totalR);
            }
            for (var i = 0; i < objShiftTemp.length; i++) {
                var obj = objShiftTemp[i];
                var d = new Date(obj.work_date);
                var day = d.getDate();
                var vari = '_day_R_' + day;
                var TotalR= parseInt($('#shift' + vari).html());
                TotalR = TotalR + parseInt(obj.totalST);
                $('#shift' + vari).html(TotalR);
            }
    <?php
    } else { ?>
    var objShift = JSON.parse('<?php echo json_encode($queryShift); ?>');
    for (var i = 0; i < objShift.length; i++) {

        var obj = objShift[i];
        var d = new Date(obj.work_date);
        var day = d.getDate();
        var str_date=formatDate(d);
        var vari = '_day_' + day;
        var value = $('#shift' + vari).attr('value');
        if (value === undefined)
            value = '';
        var name = '';
        if (obj.user_id !== null) {
            var url='<?php echo $this->Url->build([ "controller" => "CounselNote","action" => "note"]);?>';
            var  link ='<a href="'+url+'?id='+obj.user_id+'&rs='+obj.cid+'">' + obj.user_name+'</a>';
            name = link;
        }else if ( obj.tentative_reservation_flag ==1 && obj.tmp_reserve_id ){
            name = '☓';
        }
        else {
            name = '空き';
        }
        str=insert(obj.work_time_start,':',2);
        value += '<p>' + str +' '  + name + '</p>'

        $('#shift' + vari).html(value);
        $('#shift' + vari).attr('value', value);
    }
    <?php } ?>
    $('#concierges').change(function () {
        var url='<?php echo $this->Url->build([ "controller" => "Schedule","action" => "month"]);?>';
        var para='?id='+$(this).val()+'&dt=<?php echo $currentdate->format('Y').'-'.($currentdate->format('m')).'-'.($currentdate->format('d'));?>';
        window.location.href =url+para;
    })
    function formatDate(date) {
        var str_=  d.getFullYear() +'-' +( (d.getMonth()+1).toString().length==1?'0'+(d.getMonth()+1):(d.getMonth()+1)) +'-'+( (d.getDate()).toString().length==1?'0'+(d.getDate()):(d.getDate()));
        return str_;
    }
</script>
