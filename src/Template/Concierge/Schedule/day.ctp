<?php
$currentdate=new DateTime($initsdate);
$prvdate = new DateTime($initsdate);
$prvdate = $prvdate->modify('-1 day');
$nextdate = new DateTime($initsdate);
$nextdate = $nextdate->modify('+1 day');
//echo $this->Html->css('style.css');
echo $this->Html->css('responsive-tables-01');
$class_table='responsive respon-tbl';
$d = new DateTime($initsdate);
$d->modify('first day of next month');
$yearNext = $d->format('Y');
$monthNext = $d->format('m');
$now = new \DateTime();
$istoday=false;
$NextMonth = new \DateTime();
$NextMonth ->modify('first day of next month');
if ($now->format('Y') == $currentdate->format('Y') && $now->format('m') == $currentdate->format('m') && $now->format('d') == $currentdate->format('d'))
{
    $istoday=true;
}
?>
<style>
    table.responsive td
    {
        text-align: center;
    }
    table.responsive th a
    {
        color: #fff;
        text-decoration: none!important;
    }
    .marg-15{
        margin: 15px 0 15px 0;
    }
    @media only all and (max-width: 767px) {
        .concierge-modal .wrap-image {
            height: auto;
        }
    }
</style>
<!-- CSS Calendar -->
<link rel="stylesheet" href="<?= $this->Html->getVersion('/css/fullcalendar.css') ?>" >
<link rel="stylesheet" href="<?= $this->Html->getVersion('/css/scheduler.min.css') ?>" />
<link rel="stylesheet" href="<?= $this->Html->getVersion('/css/new_style.css') ?>" />

<h2 id="page-title" class="clearfix">

        <div class="section-inner">
            <div class="section-contents">
                <section class="main-cont"> <div class="page-title-inner">
        <span class="en">CONCIERGE</span>
        <span class="jp">コンシェルジュ</span>

</h2>
<section id="concierge" class="section-container">
    <div class="section-inner">
      <div class="section-contents">
        <section class="main-cont">
        <h2 class="title-01">スケジュール（日）</h2>
            <p class="title-pop">
                <a class="btn-open-popup" href="javascript:void(0);" title="Popup">予約・利用方法</a>
            </p>
        <div class="show-time">
            <?= $this->Flash->render('flash') ?>
            <div class="clearfix"></div>
        </div>
            <div class="ibox-content">
                <div class="fc-calendar-default fc-calendar-classic fc fc-unthemed fc-ltr">
                    <div class="fc-toolbar show fc-header-toolbar">
                        <div class="fc-left">
                            <div class="fc-button-group">
                                <button type="button"  onclick="window.location.href='<?php echo $this->Url->build(["controller" => "Schedule","action" => "day",'?'=>['id'=>$id,'dt'=>$prvdate->format('Y').'-'.($prvdate->format('m')).'-'.$prvdate->format('d') ]]);?>'"
                                        class="fc-prev-button fc-button fc-state-default fc-corner-left">
                                    <span class="fc-icon fc-icon-left-single-arrow"></span>
                                </button>
                                <button type="button"  onclick="window.location.href='<?php echo $this->Url->build(["controller" => "Schedule","action" => "day",'?'=> ['id'=>$id,'dt' =>
                                    $nextdate->format('Y').'-'.($nextdate->format('m')).'-'.$nextdate->format('d') ]]);?>'"  class="fc-next-button fc-button fc-state-default fc-corner-right">
                                    <span class="fc-icon fc-icon-right-single-arrow"></span>
                                </button>
                            </div>
                            <?php if (!$istoday) :?>
                                <button type="button"
                                        class="fc-today-button fc-button fc-state-default fc-corner-left fc-corner-right fc-state"
                                        onclick="window.location.href='<?php echo $this->Url->build([ "controller" => "Schedule","action" => "day",'?' => ['id' => $id,'dt' =>$now->format('Y-m-d')]]);?>'"
                                >今日</button>
                            <?php else:?>
                                <button type="button"
                                        class="fc-today-button fc-button fc-state-default fc-corner-left fc-corner-right fc-state-disabled" disabled="" >今日</button>
                            <?php endif;?>
                        </div>

                        <div class="fc-right">
                            <div class="fc-button-group">
                                <button type="button" onclick="window.location.href='<?php echo $this->Url->build([ "controller" => "Schedule","action" => "month",'?' => ['id' => $id,'dt' => date('Y-m-d', strtotime($initsdate))]    ]);?>'" class="fc-month-button fc-button fc-state-default fc-corner-left fc-state">月</button>
                                <button type="button" onclick="window.location.href='<?php echo $this->Url->build([ "controller" => "Schedule","action" => "week",'?' => ['id' => $id,'dt' => date('Y-m-d', strtotime($initsdate))]]);?>'" class="fc-agendaWeek-button fc-button fc-state-default"  >週</button>
                                <button type="button"   class="fc-agendaDay-button fc-button fc-state-active fc-corner-right ">日</button>
                            </div>
                        </div>
                        <div class="fc-center"><h2> <?php echo  date('Y年m月d日',strtotime( $initsdate)); ?></h2></div>
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
    <?=$this->element('reservation') ?>
</section>
<script src="<?= $this->Html->getVersion('/js/responsive-tables.js')?>"></script>
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

    var events=[];
    var  current_date= new Date('<?=$initsdate  ?>')
    $(document).ready(function() {
        var objShift = JSON.parse('<?php echo json_encode($queryShift); ?>');
        var status='<?php echo $reservesstatus ; ?>';
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
                var url='',title ;
                <?php if ($user_id > 0) { ?>
                shift_info=' '+ insert(obj.work_time_start,':',2) ;//+ ' ~ ' + insert((work_time_start +40),':',2);
                if (status == 1) {
                    var link;
                    if (obj.status==0 && (obj.tentative_reservation_flag ==0 || !obj.tmp_reserve_id)){
                        title= '空き' ;
                        url = '<?php echo $this->Url->build(['prefix' => 'mypage', "controller" => "reserve", 'action' => 'form']);?>' +'?id=' + obj.sid +'&his=104&cn='+<?=$id?>;
                    }else {
                        title ='予約済';
                    }
                    name = link;
                }
                else {
                    if (obj.status==0 && (obj.tentative_reservation_flag ==0 || !obj.tmp_reserve_id)){
                        name = '○ ' + obj.name  + ' ' + shift_info;
                        title = '空き';
                    }
                    else
                    {
                        name = '☓ ' + obj.name  + ' ' + shift_info;
                        title = '予約済';
                    }
                }
                <?php }else{ ?>
                if (obj.status==0 && (obj.tentative_reservation_flag ==0 || !obj.tmp_reserve_id))
                    title='空き';
                else
                    title='予約済';
                <?php } ?>

                var item =   {
                    id:obj.sid,
                    resourceId: obj.cid,
                    title:title,
                    start: new Date(y, m, d, h, 20),
                    end:new Date(y, m, d,h, 40),
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
                    defaultView: 'agendaDay',
                    defaultDate: current_date,
                    contentHeight: "auto",
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
                        console.log(resource);
                        var title = cellEls.find( '.fc-title' );
                        title.html( title.text() );
                        cellEls.on('click', function() {
                            $('#res'+resource.id).click();
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
        var url = '<?php echo $this->Url->build([ "controller" => "Schedule","action" => "day"]);?>';
        window.location.href = url + '?id=' + $(this).val() + '&dt=<?php echo $currentdate->format('Y').'-'.($currentdate->format('m')).'-'.$currentdate->format('d') ?>';
    })
</script>


<?php
$cnt=1;
if (count($arrayConierges)>0)
foreach($arrayConierges  as $key => $concierge):  ?>
    <a  class="openmodal cboxElement"  href="#item<?=$concierge->id?>" id="res<?=$concierge->id?>" ></a>
    <div class="modal-area" style="display:none">
        <div class="modal-content" id="item<?php echo $concierge->id  ?>">
            <div class="concierge-modal">
                <?php if($concierge->image_name): ?>
                    <div class="image" style="background-image: url(<?php echo $this->Url->build('/').$filePath.$concierge->image_name.'?'.date('Ymdhis', strtotime($concierge->modified_date)) ?>)" ><img src="<?php echo $this->Url->build('/').$filePath.$concierge->image_name.'?'.date('Ymdhis', strtotime($concierge->modified_date)) ?>" style="visibility: hidden;"></div>
                <?php endif; ?>
                <!-- <div class="weekday"></div> -->
                <?php if($concierge->name || $concierge->name_e): ?>
                    <div class="name">
                            <span class="name-wrap">
                                <span class="name-text"><?php echo $concierge->name ?></span>
                                <span class="kana"><?php echo $concierge->name_e ?></span>
                            </span>
                    </div>
                <?php endif ?>
                <div class="type">
                    <?php
                    $keyword = $language = "";
                    foreach ($concierge->concierge_informations as $ci) {

                        if ($ci->info_type == 1) {
                            $keyword .= "<li>".$ci->info_text ."</li>" ;
                        }
                        if ($ci->info_type == 2) {
                            $language .= "<li>".$ci->info_text ."</li>" ;
                        }
                    } ?>
                    <?php echo ($keyword)?"<ul>".$keyword."</ul>":"";  ?>
                    <?php echo ($language)?"<ul>".$language."</ul>":"";  ?>

                </div>
                <div class="text">
                    <p><?php echo $concierge->career ?></p>
                </div>
                <div class="btn-block">

                    <?php if( (isset($reservesstatusCurentMonth) && $reservesstatusCurentMonth == 1 )): ?>
                        <input class="button pop-btn105" type="button" name="" onclick="schedule('<?php echo $this->Url->build(['controller' => 'Schedule','action' => 'month','?'=>[ 'id'=>$concierge->id,'dt'=>  $now->format('Y-m-d')]]);?>')" value="今月の出勤日（予約）" />
                    <?php endif; ?>
                    <?php

                    if( (isset($reservesstatusCurentMonthToNextMonth) && $reservesstatusCurentMonthToNextMonth == 1 )): ?>
                        <input class="button pop-btn105" type="button" name="" onclick="schedule('<?php echo $this->Url->build(['controller' => 'Schedule','action' => 'month','?'=>[ 'id'=>$concierge->id,'dt'=> $NextMonth->format('Y-m-d') ]]);?>')" value="来月の出勤日（予約）" />
                    <?php endif; ?>
                </div>
                <div class="modal-number pc"><?php echo $cnt; ?>/<?php echo count($concierges); ?></div>
            </div>
        </div>
    </div>

    <?php
    $cnt++ ;
endforeach; ?>



<?= $this->Html->script('/js/pop-up.js')?>
<?= $this->Html->script('/assets-sht2/js/masonry.pkgd.min.js')?>
<script type="text/javascript">

    function clickMo() {
        $('#item1').dialog();

    }
    $(function(){
        $(document).on('click', 'a.test', function() {
            alert('aa');
        });

        $('.sort-link a').on('click',function(e){
            e.preventDefault();
            var selected = $(this).attr('class');
            $(this).parent('li').addClass('active').siblings().removeClass('active');
            $('.item-all').hide();
            $('.item-' + selected).show();
            $('.concierge-list > ul').masonry({
                // options
                itemSelector: '.concierge-item',
                columnWidth: 430
            });
        });
        $('.sort-select select').on('change',function(e){
            e.preventDefault();
            var selected = $(this).val();
            $('.item-all').hide();
            $('.item-' + selected).show();
        });
        var winWstat, winWstatB;
        $(window).on('resize load',function(){
            var winW = $(window).width();
            winWstatB = winWstat;
            if(winW > 750){
                winWstat = "PC";
                if(winWstat != winWstatB){
                    $('.concierge-list > ul').masonry({
                        // options
                        itemSelector: '.concierge-item',
                        columnWidth: 430
                    });
                }
            }else{
                winWstat = "mobile";
                if(winWstat != winWstatB){
                    $('.concierge-list > ul').masonry({
                        // options
                        itemSelector: '.concierge-item',
                        columnWidth: 430
                    });
                    $('.concierge-list > ul').masonry('destroy');
                }
            }

            $.colorbox.remove();
            if(winW < 750){
                $('.openmodal').colorbox({rel:'person',inline: true, width: 305, maxWidth: winW-40, maxHeight: "90%"});
            }else{
                $('.openmodal').colorbox({rel:'person',inline: true});
            }
        });

    });

    function schedule(url){
        if(url){
            window.location.href =url;
        }
    }
</script>
