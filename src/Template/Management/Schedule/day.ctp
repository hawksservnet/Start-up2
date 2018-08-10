<?php
$AUTH =(int) $loginInfo['AUTH'];
$currentdate=new DateTime($initsdate);
$prvdate = new DateTime($initsdate);
$prvdate = $prvdate->modify('-1 day');
$nextdate = new DateTime($initsdate);
$nextdate = $nextdate->modify('+1 day');

?>
<?php
    if ($AUTH == 0 || $AUTH==4 || $AUTH==5) {
        echo $this->Html->css('responsive-tables-01');
        $class_table='responsive respon-tbl';
    }
    else {
       echo  $this->Html->css('responsive-tables');
        $class_table='responsive min-col';
    }
?>
<h2 class="title-01">スケジュール（日）</h2>
<div class="show-time">
    <?= $this->Flash->render('flash') ?>
    <div class="clearfix"></div>
    <ul class="fm-left fm-line">
        <li><span><?php echo $initsdate ?></span></li>
        <li><a href="<?php echo $this->Url->build(["controller" => "Schedule","action" => "day",'?'=>['id'=>$id,'dt'=>
                $prvdate->format('Y').'-'.($prvdate->format('m')).'-'.$prvdate->format('d') ]]);?>"><</a></li>
        <li><a href="<?php echo $this->Url->build(["controller" => "Schedule","action" => "day",'?'=> ['id'=>$id,'dt' =>
                $nextdate->format('Y').'-'.($nextdate->format('m')).'-'.$nextdate->format('d') ]]);?>">></a></li>
    </ul>
    <?php if ($AUTH == 0 || $AUTH==4 || $AUTH==5 ) { ?>
    <ul class="show-rights fm-right">
        <li><span>コンシェルジュを絞り込む</span></li>
        <li>
            <?php
            echo $this->Form->select('concierges', $arrconcierges, [
                'default' => $id,
                'id'=>'concierges',
                'empty' => ['0'=>'すべて'],
                'label' => false]);
            ?>
        </li>
    </ul>
    <?php } ?>
    <div class="clearfix"></div>
    <ul class="show-rights pa-rights fm-right">
        <li><span>表示を切り替える</span></li>
        <li>
            <a href="<?php echo $this->Url->build([ "controller" => "Schedule","action" => "month",'?' => ['id' => $id,'dt' => date('Y-m-d', strtotime($initsdate))]    ]);?>">月</a>
        </li>
        ／
        <li>
            <a href="<?php echo $this->Url->build([ "controller" => "Schedule","action" => "week",'?' => ['id' => $id,'dt' => date('Y-m-d', strtotime($initsdate))]]);?>">週</a>
        </li>
    </ul>
</div>
<table id="demoTable" class="<?php echo $class_table; ?>">

        <th class="black-item" style="height: 40px"></th>
        <?php
        if (count($arrconcierges_workshiff)>0)
            foreach ($arrconcierges_workshiff as $key => $row) {
                ?>
                <th>  <?php  echo $row?> </th>
                <?php
            }
        else { ?>
            <th></th>
        <?php }
        ?>
    </tr>
    <?php
    $key_arrconcierges=array_keys($arrconcierges_workshiff);
    for($hour=0; $hour<11; $hour++){
        echo '<tr>';
        echo 	'<td class="black-item" >'.('10'+$hour).':00</td>';
        if (count($arrconcierges_workshiff)>0)
        for($day=0; $day<count($arrconcierges_workshiff); $day++){
            $name = 'c_'.$key_arrconcierges[$day].'_hour_'.('10'+$hour);
            $shiftname = 'shift_'.$name;
            $cname = 'change_'.$name;
            $dname = 'date_'.$name;
            $tname = 'time_'.$name;
            $sname = 'sid_'.$name;
            echo 	'<td id="'.$shiftname.'"></td>';
        }
        else echo 	'<td ></td>';
        echo '</tr>';
    }
    ?>
    </table>
<?= $this->Html->script('responsive-tables')?>
<script>
    var AUTH='<?php echo $AUTH ?>';
    var objShift = JSON.parse('<?php echo json_encode($queryShift); ?>');

    for(var i = 0; i < objShift.length; i++) {
        var obj = objShift[i];
        console.log(obj);
        var d = new Date(obj.work_date);
        var day = d.getDay();
        var hour = obj.work_time_start.substring(0, 2);
        var str_date=formatDate(d);
        var vari = '_c_'+obj.cid+'_hour_'+hour;

        if (AUTH==0 || AUTH==4 || AUTH==5 )
        {
            if (obj.user_id != null){
                var url='<?php echo $this->Url->build([ "controller" => "CounselNote","action" => "note"]);?>';
                var value ='<a href="'+url+'?id='+obj.user_id+'&rs='+obj.rid+'">' + obj.user_name+'</a>';
            }else if ( obj.tentative_reservation_flag ==1 && obj.tmp_reserve_id ) {
                var value='予約中';
            }
            else {
                var value='空き';
            }
        }
        else
        {
            if (obj.user_id != null){
                var url='<?php echo $this->Url->build([ "controller" => "CounselNote","action" => "note"]);?>';
                var value ='<a href="'+url+'?id='+obj.user_id+'&rs='+obj.rid+'">' + obj.user_name+'</a>';
            }else if ( obj.tentative_reservation_flag ==1 && obj.tmp_reserve_id ) {
                var value='予約中';
            }
            else
            {
                var value ='空き';
            }
        }

        $('#shift'+vari).attr('value',value);
        $('#shift'+vari).html(value);
    }
    $('#concierges').change(function () {
        var url='<?php echo $this->Url->build([ "controller" => "Schedule","action" => "day"]);?>';
        window.location.href = url+'?id='+$(this).val()+'&dt=<?php echo $currentdate->format('Y').'-'.($currentdate->format('m')).'-'.$currentdate->format('d') ?>';
    })
    function formatDate(date) {
        var str_=  d.getFullYear() +'-' +( (d.getMonth()+1).toString().length==1?'0'+(d.getMonth()+1):(d.getMonth()+1)) +'-'+( (d.getDate()).toString().length==1?'0'+(d.getDate()):(d.getDate()));
        return str_;
    }
</script>
