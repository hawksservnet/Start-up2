<?php
$currentdate=new DateTime($initsdate);
$prvdate = new DateTime($initsdate);
$prvdate = $prvdate->modify('-7 day');
$nextdate = new DateTime($initsdate);
$nextdate = $nextdate->modify('+7 day');
$now = new \DateTime();
$istoday=false;
if ($now->format('Y') == $currentdate->format('Y') && $now->format('m') == $currentdate->format('m'))
{
    $istoday=true;
}
?>
<?= $this->Html->css('responsive-tables') ?>

<style>
    table.responsive th a
    {
        color: #fff;
    }
</style>
<!-- CSS Calendar -->
<link rel="stylesheet" href="<?= $this->Html->getVersion('/css/fullcalendar.css') ?>" >
<link rel="stylesheet" href="<?= $this->Html->getVersion('/css/scheduler.min.css') ?>" />
<link rel="stylesheet" href="<?= $this->Html->getVersion('/css/new_style.css') ?>" />
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
                <h2 class="title-01">スケジュール（週）</h2>
                <p class="title-pop">
                    <a class="btn-open-popup" href="javascript:void(0);" title="Popup">予約・利用方法</a>
                </p>
                <div class="show-time">
                    <?= $this->Flash->render('flash') ?>

                </div>
                <div class="ibox-content">
                    <div class="fc-calendar-default fc-calendar-classic fc fc-unthemed fc-ltr">
                        <div class="fc-toolbar show fc-header-toolbar">
                            <div class="fc-left">
                                <div class="fc-button-group">
                                    <button type="button" onclick="window.location.href='<?php echo $this->Url->build(["controller" => "Schedule","action" => "week",'?' =>['id'=>$id,'dt' =>
                                        $prvdate->format('Y').'-'.($prvdate->format('m')).'-'.$prvdate->format('d') ]]);?>'"  class="fc-prev-button fc-button fc-state-default fc-corner-left">
                                        <span class="fc-icon fc-icon-left-single-arrow"></span>
                                    </button>
                                    <button type="button" onclick="window.location.href='<?php echo $this->Url->build(["controller" => "Schedule","action" => "week",'?' =>['id' =>$id,'dt' =>
                                        $nextdate->format('Y').'-'.($nextdate->format('m')).'-'.$nextdate->format('d') ]]);?>'"    class="fc-next-button fc-button fc-state-default fc-corner-right">
                                        <span class="fc-icon fc-icon-right-single-arrow"></span>
                                    </button>
                                </div>
                                <?php if (!$istoday) :?>
                                    <button type="button"
                                            class="fc-today-button fc-button fc-state-default fc-corner-left fc-corner-right fc-state"
                                            onclick="window.location.href='<?php echo $this->Url->build([ "controller" => "Schedule","action" => "month",'?' => ['id' => $id,'dt' =>$now->format('Y-m-d')]]);?>'"
                                    >今日</button>
                                <?php else:?>
                                    <button type="button"
                                            class="fc-today-button fc-button fc-state-default fc-corner-left fc-corner-right fc-state-disabled" disabled="" >今日</button>
                                <?php endif;?>
                            </div>

                            <div class="fc-right">
                                <div class="fc-button-group">
                                    <button type="button" onclick="window.location.href='<?php echo $this->Url->build([ "controller" => "Schedule","action" => "month",'?' => ['id' => $id,'dt' => date('Y-m-d', strtotime($currentTime ))]]);?>'" class="fc-month-button fc-button fc-state-default fc-corner-left fc-state">月</button>
                                    <button type="button" class="fc-agendaWeek-button fc-button fc-state-active"
                                            onclick="window.location.href='<?php echo $this->Url->build([ "controller" => "Schedule","action" => "week",'?' => ['id' => $id,'dt' => date('Y-m-d', strtotime($currentTime ))]]);?>'"
                                    >週</button>
                                    <button type="button" onclick="window.location.href='<?php echo $this->Url->build([ "controller" => "Schedule","action" => "day",'?' => ['id' => $id,'dt' => date('Y-m-d', strtotime($currentTime ))]]);?>'" class="fc-agendaDay-button fc-button fc-state-default fc-corner-right ">日</button>
                                </div>
                            </div>
                            <div class="fc-center"><h2><?php echo date('Y年m月d日', strtotime($initsdate ))  ; ?>～<?php echo date('Y年m月d日', strtotime($initedate ))  ; ?></h2></div>
                            <div class="fc-right fc-reponsive">
                                <div class="fc-button-group">
                                    <?php
                                    echo $this->Form->select('concierges', $arrconcierges, [
                                        'default' => $id,
                                        'empty' => ['0'=>'すべて'],
                                        'id' => 'concierges',
                                        'style' => 'margin-right:5px',
                                        'label' => false]);
                                    ?>
                                </div>
                            </div>

                            <div class="fc-clear">

                            </div>
                        </div>
                    </div>
                    <div class="fc-calendar-default fc-calendar-classic" id="calendar"></div>
                </div>
            </section>
        </div>
    </div>
</section>
<?=$this->element('reservation') ?>
<?= $this->Html->script('/js/pop-up.js')?>
<script src="<?= $this->Html->getVersion('/js/moment.min.js') ?>"></script>
<script src="<?= $this->Html->getVersion('/js/fullcalendar.min.js') ?>"></script>
<script src="<?= $this->Html->getVersion('/js/scheduler.min.js') ?>"></script>
<script src="<?= $this->Html->getVersion('/js/ja.js') ?>"></script>
<script>
    function insert(main_string, ins_string, pos) {
        main_string=main_string.toString();
        if(typeof(pos) == "undefined") {
            pos = 0;
        }
        if(typeof(ins_string) == "undefined") {
            ins_string = '';
        }
        return main_string.slice(0, pos) + ins_string + main_string.slice(pos);
    }
    var resources =JSON.parse('<?php echo json_encode($queryconcierges ); ?>');

    var date_time_=[];
    var events=[];
    var objShift = JSON.parse('<?php echo json_encode($queryShift); ?>');
    var positionShiftWorks = JSON.parse('<?php echo json_encode($positionShiftWorks); ?>');
    console.log(positionShiftWorks );
    var  current_date= new Date('<?=$currentTime ?>');
    console.log(current_date);
    $(document).ready(function() {
        var objShift = JSON.parse('<?php echo json_encode($queryShift); ?>');
        var status='<?php echo $reservesstatus ; ?>';
        var status2='<?php echo $reservesstatus2 ; ?>';	//月替わりチェック用
        var smonth=<?php echo $initsmonth ; ?>;	//月替わりチェック用
        var emonth=<?php echo $initemonth ; ?>;	//月替わりチェック用

        var index=0;
        $(document).ready(function() {
            var objShift = JSON.parse('<?php echo json_encode($queryShift); ?>');
            var status='<?php echo $reservesstatus ; ?>';
            for (var i = 0; i < objShift.length; i++) {
                var obj = objShift[i];
                var date = new Date(obj.work_date);
                var d = date.getDate();

                var m = date.getMonth();
                var y = date.getFullYear();
                var h= obj.work_time_start.slice(0,2);
                index = positionShiftWorks[obj.sid];
                var chk_m = date.getMonth() + 1;	//月替わりチェック用
                var url='',title ;
                <?php if ($user_id > 0) { ?>
                shift_info=' '+ insert(obj.work_time_start,':',2) ;//+ ' ~ ' + insert((work_time_start +40),':',2);

//                if (status == 1) {
                if ((status == 1)&&(chk_m == smonth)) {
                    var link;
                    if (obj.status==0 && (obj.tentative_reservation_flag ==0 || !obj.tmp_reserve_id)){

                        //   url='<?php echo $this->Url->build(['prefix' => 'mypage', "controller" => "reserve", 'action' => 'form']);?>';
                        //  link = '<a href="' + url + '?id=' + obj.sid + '&his=104">○ ' +obj.name  + '</a><span>'+shift_info+'</span>';
                        title= '○ ' +obj.name ;
                        url = '<?php echo $this->Url->build(['prefix' => 'mypage', "controller" => "reserve", 'action' => 'form']);?>' +'?id=' + obj.sid +'&his=104&cn='+<?=$id?>;
                    }else {
                        title ='☓ ' +obj.name ;
                    }
                    name = link;
                }else if ((status2 ==1)&&(chk_m == emonth)) {
                    var link;
                    if (obj.status==0 && (obj.tentative_reservation_flag ==0 || !obj.tmp_reserve_id)){

                        //   url='<?php echo $this->Url->build(['prefix' => 'mypage', "controller" => "reserve", 'action' => 'form']);?>';
                        //  link = '<a href="' + url + '?id=' + obj.sid + '&his=104">○ ' +obj.name  + '</a><span>'+shift_info+'</span>';
                        title= '○ ' +obj.name ;
                        url = '<?php echo $this->Url->build(['prefix' => 'mypage', "controller" => "reserve", 'action' => 'form']);?>' +'?id=' + obj.sid +'&his=104&cn='+<?=$id?>;
                    }else {
                        title ='☓ ' +obj.name ;
                    }
                    name = link;

                } else {
                    if (obj.status==0 && (obj.tentative_reservation_flag ==0 || !obj.tmp_reserve_id)){
                        name = '○ ' + obj.name  + ' ' + shift_info;
                        title = '○ ' + obj.name;
                    }
                    else
                    {
                        name = '☓ ' + obj.name  + ' ' + shift_info;
                        title = '☓ ' + obj.name;
                    }
                }
                <?php }else{ ?>
                shift_info=' '+ insert(obj.work_time_start,':',2) + ' ~ ' + insert((work_time_start +40),':',2);
                name= obj.name +' '+shift_info;
                title =obj.name;

                <?php } ?>


                var item =   {
                    id:obj.sid,
                    resourceId: obj.cid,
                    title:title,
                    start: new Date(y, m, d, h,(index*20)-20),
                    end:new Date(y, m, d, h, index*20),
                    url: url,
                    allDay: false
                }
                events.push(item);

            }
            /* initialize the calendar
             -----------------------------------------------------------------*/
            $(function() { // document ready
                var date = new Date();
                var d = date.getDate();
                var m = date.getMonth();
                var y = date.getFullYear();
                $('#calendar').fullCalendar({
                    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                    lang: 'ja',
                    defaultView: 'agendaWeek',
                    defaultDate: current_date,
                    contentHeight: "auto",
                    firstDay:1,
                    editable: false,
                    selectable: false,
                    eventLimit: true, // allow "more" link when too many events
                    allDaySlot:false,
                    fixedWeekCount:false,
                    showNonCurrentDates:false,
                    columnHeader :false,
                    eventColor: '#fff',
                    eventBorderColor:"#e7eaec",
                    eventOrder:'title',
                    timeFormat: 'HH(:mm)',
                    slotLabelFormat:"HH:mm",
                    slotDuration: "00:20:00",
                    minTime:"10:00:00",
                    maxTime:"21:00:00",
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay'
                    },
                    select: function(start, end, jsEvent, view){
                        //wrtie your redirection code here
                        var root_url="http://localhost/";
                        //window.location = root_url+"test1.html"
                        console.log(root_url);
                    },
                    dayRender: function (date, cell) {

                        //console.log(date);
                        //console.log(cell);

                    },
                    resourceRender: function(resource, cellEls) {
                        cellEls.on('click', function() {
                            console.log('click title',resource);
                        });
                    },
                    viewRender: function(view, element) {
                        $("#calendar").fullCalendar('option', 'contentHeight', 955);  //change id and height if needed

                        if (view.name === "month") {
                        } else {
                            $("#calendar").fullCalendar('option', 'contentHeight', "auto"); //change id and height if needed

                        }
                        $("#calendar .fc-header-toolbar").remove();

                        $("#calendar .fc-agenda-view").css('max-height','100%');

                    },
                    resources: resources,
                    events: events,

                    /* eventAfterAllRender: function (event, element, view) {
                     $('#calendar').find('.fc-more').text(function(i, t) {
                     return t.replace('more', 'もっと見る')
                     });
                     },
                     */
                });

            });

        });

    });
    $('#concierges').change(function () {
        var url='<?php echo $this->Url->build([ "controller" => "Schedule","action" => "week"]);?>';
        window.location.href = url+'?id='+$(this).val()+'&dt=<?php echo $currentdate->format('Y').'-'.($currentdate->format('m')).'-'.$currentdate->format('d') ?>';
    })
</script>

