<?php
use Cake\Core\Configure;
echo $this->Html->css('jquery-ui');
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
$dt = ($this->request->getQuery('dt'))?$this->request->getQuery('dt'):$currentTime;
$now = new \DateTime();
$NextMonth = new \DateTime();
$NextMonth ->modify('first day of next month');
?>

<ul class="left-art-cont left-art-fix view-table">
<?php
    $cnt = 1;
    foreach($concierges as $key => $concierge):  ?>
    <li>
        <?php if($concierge->image_name): ?>
        <div class="art-pic">

            <a href="#item<?php echo $concierge->id  ?>" title="詳細／予約" class="openmodal">
                <img src="<?php echo $this->Url->build('/').$filePath.$concierge->image_name.'?'.date('Ymdhis',strtotime($concierge->modified_date)) ?>" alt="<?php echo $concierge->name ?>" />
                <!--<span>詳細／予約</span>  -->
            </a>

        </div>
            <div class="art-name">
                <p><?php echo ($concierge->name)?$concierge->name."<br>":"" ?><?php echo ($concierge->name_e)?$concierge->name_e:""; ?></p>
            </div>
        <?php endif; ?>

      <div class="modal-area">
            <div class="modal-content" id="item<?php echo $concierge->id ?>">
                <div class="concierge-modal">
                    <?php if($concierge->image_name): ?>
                    <div class="wrap-image">
                        <div class="image" style="background-image: url(<?php echo $this->Url->build('/').$filePath.$concierge->image_name.'?'.date('Ymdhis',strtotime($concierge->modified_date)) ?>)" ><img src="<?php echo $this->Url->build('/').$filePath.$concierge->image_name.'?'.date('Ymdhis',strtotime($concierge->modified_date)) ?>" style="visibility: hidden;"></div>
                    </div>
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
                        <input class="button pop-btn105" type="button" name="" onclick="schedule('<?php echo $this->Url->build(['controller' => 'Schedule','action' => 'month','?'=>[ 'id'=>$concierge->id,'dt'=> $now->format('Y-m-d')]]);?>')" value="今月の出勤日（予約）" />
                        <?php endif; ?>
                        <?php
                         $nextMonth =  $yearNext.'-'.$monthNext.'-01';
                        if( (isset($reservesstatusNextMonth) && $reservesstatusNextMonth == 1 )): ?>
                        <input class="button pop-btn105" type="button" name="" onclick="schedule('<?php echo $this->Url->build(['controller' => 'Schedule','action' => 'month','?'=>[ 'id'=>$concierge->id,'dt'=> $NextMonth->format('Y-m-d')]]);?>')" value="来月の出勤日（予約）" />
                        <?php endif; ?>
                    </div>
                    <div class="modal-number pc"><?php echo $cnt; ?>/<?php echo count($concierges); ?></div>
                </div>
            </div>
        </div>
    </li>
    <?php
     $cnt++ ;
     endforeach; ?>
     </ul>
    <script src="<?= $this->Html->getVersion('/js/pop-up.js') ?>"></script>
    <?= $this->Html->script('/assets-sht2/js/masonry.pkgd.min.js')?>
    <script type="text/javascript">
        $(function(){

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

    </script>
