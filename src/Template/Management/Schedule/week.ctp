<?php
$AUTH =(int) $loginInfo['AUTH'];
$currentdate=new DateTime($initsdate);
$prvdate = new DateTime($initsdate);
$prvdate = $prvdate->modify('-7 day');
$nextdate = new DateTime($initsdate);
$nextdate = $nextdate->modify('+7 day');
//echo date('Y-m-d', strtotime($initsdate));
?>
<?= $this->Html->css('responsive-tables') ?>
<style>
    table.responsive th a
    {
        color: #fff;
    }
</style>
<h2 class="title-01">スケジュール（週）</h2>
<div class="show-time">
    <?= $this->Flash->render('flash') ?>
    <ul>
        <li class="fm-left">
            <a href="<?php echo $this->Url->build(["controller" => "Schedule","action" => "week",'?' =>['id'=>$id,'dt' =>
                $prvdate->format('Y').'-'.($prvdate->format('m')).'-'.$prvdate->format('d') ]]);?>"><< 前週へ</a>
        </li>
        <li>
            <span><?php echo $initsdate; ?>～<?php echo $initedate; ?></span>
        </li>
        <li class="fm-right">
            <a href="<?php echo $this->Url->build(["controller" => "Schedule","action" => "week",'?' =>['id' =>$id,'dt' =>
                $nextdate->format('Y').'-'.($nextdate->format('m')).'-'.$nextdate->format('d') ]]);?>">次週へ >></a>
        </li>
    </ul>
    <?php if ($AUTH === 0 || $AUTH===4 || $AUTH===5) { ?>
    <ul class="show-rights fm-right">
        <li><span>コンシェルジュを絞り込む</span></li>
        <li>
            <?php
            echo $this->Form->select('concierges', $arrconcierges, [
                'default' => $id,
                'id'=>'concierges',
                'empty' =>['0'=>'すべて'],
                'label' => false]);
            ?>
        </li>
    </ul>
    <?php } ?>
    <div class="clearfix"></div>
    <ul class="show-rights pa-rights fm-right">
        <li><span>表示を切り替える</span></li>
        <li>
            <a href="<?php echo $this->Url->build([ "controller" => "Schedule","action" => "month",'?' => ['id' => $id,'dt' => date('Y-m-d', strtotime($initsdate))]]);?>">月</a>
        </li>
        ／
        <li>
            <a href="<?php echo $this->Url->build([ "controller" => "Schedule","action" => "day",'?' => ['id' => $id, 'dt' => date('Y-m-d', strtotime($initsdate)       )]]);?>">日</a>
        </li>
    </ul>
</div>
<table id="demoTable" class="responsive">
    <tr>
        <th class="black-item"></th>
        <?php
        $datename =  array(0 => '(月）', 1 => '(火）', 2 => '(水）', 3 => '(木）', 4 => '(金）', 5 => '(土）', 6 => '(日）');
        for($i=0;$i<7;$i++)
        {
            $datavalue=date('Y-m-d', strtotime('+'.$i.' days', strtotime($initsdate)));
            $url=$this->Url->build([ "controller" => "Schedule","action" => "day",'?'=>['id'=>'0','dt'=>$datavalue]]);
            $label=date('m/d', strtotime('+'.$i.' days', strtotime($initsdate))).$datename[$i];
            echo '<th><a href="'.$url.'" >'.$label.'</a></th>';
        }
        ?>
    </tr>
    <?php
    for($hour=0; $hour<11; $hour++){
            echo '<tr>';
                echo 	'<td class="black-item" >'.('10'+$hour).':00</td>';
            for($day=0; $day<7; $day++){
                $name = 'day_'.($day+1).'_hour_'.('10'+$hour);
                $shiftname = 'shift_'.$name;
                $cname = 'change_'.$name;
                $dname = 'date_'.$name;
                $tname = 'time_'.$name;
                $sname = 'sid_'.$name;
                echo 	'<td stype="white-space: nowrap;"" id="'.$shiftname.'"></td>'; 		  // cid
            }
            echo '</tr>';

    }
    ?>
</table>
<script>
    var AUTH='<?php echo $loginInfo['AUTH']; ?>';
    var objShift = JSON.parse('<?php echo json_encode($queryShift); ?>');
    for(var i = 0; i < objShift.length; i++) {
        var obj = objShift[i];
        console.log(obj);
        var d = new Date(obj.work_date);
        var day = d.getDay();
        if (day==0)
            day = 7;
        var str_date=formatDate(d);
        var hour = obj.work_time_start.substring(0, 2);
        var vari = '_day_'+day+'_hour_'+hour;
        var value = $('#shift'+vari).attr('value');

        if(value === undefined)
            value='';
        if (value.length>0)
            value +=' <br/> ';
        if (AUTH === '0' || AUTH === '4'  || AUTH === '5' )
        {

            if (obj.user_id !==null) {
                var url='<?php echo $this->Url->build([ "controller" => "CounselNote","action" => "note",]);?>';
                value += '<a style="font-weight: bold;color: #000;" href="'+url+'?id='+obj.user_id+'&rs='+obj.rid +'">'+obj.name + '('+obj.user_name+')' + '</a>';
            } else if ( obj.tentative_reservation_flag ==1 && obj.tmp_reserve_id ){
                value +=obj.name + '(予約中)';
            }
            else {
                value += '<span style="color:grey;">' + obj.name + '(空き)</span>';
            }
        }
        else
        {
            if (obj.user_id !=null) {
                var url='<?php echo $this->Url->build([ "controller" => "CounselNote","action" => "note",]);?>';
                value += '<a  style="font-weight: bold;color: #000;" href="'+url+'?id='+obj.user_id+'&rs='+obj.rid+'">'+obj.user_name + '</a>';
            }else if ( obj.tentative_reservation_flag ==1 && obj.tmp_reserve_id ){
                value +='予約中';
            }
            else {
                value +='空き';
            }
        }
        $('#shift'+vari).attr('value',value);
        $('#shift'+vari).html(value);
    }
    $('#concierges').change(function () {
        var url='<?php echo $this->Url->build([ "controller" => "Schedule","action" => "week"]);?>';
        window.location.href = url+'?id='+$(this).val()+'&dt=<?php echo $currentdate->format('Y').'-'.($currentdate->format('m')).'-'.$currentdate->format('d') ?>';
    })
    function formatDate(date) {
       var str_=  d.getFullYear() +'-' +( (d.getMonth()+1).toString().length==1?'0'+(d.getMonth()+1):(d.getMonth()+1)) +'-'+( (d.getDate()).toString().length==1?'0'+(d.getDate()):(d.getDate()));
        return str_;
    }
</script>
