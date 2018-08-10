<?=$this->Html->css('simple.calendar.css') ?>
<?=$this->Html->script('simple.calendar') ?>
<?php
use Cake\Core\Configure;
echo $this->Html->css('jquery-ui');
$arr_child_purpose = Configure::read('CHILD_PURPOSE');
$arr_child_approval = Configure::read('CHILD_APPROVAL');
$arr_day_of_week = Configure::read('DAYOFWEEK');
$now = new \DateTime();
?>
<?= $this->Html->script('jquery-ui')?>
<?= $this->Html->script('jquery.validate.min.js')?>
<?= $this->Html->script('detectmobilebrowser')?>
<h2 class="title-01">託児予約　申込予約一覧</h2>
<style>
    .fm-submit{
        border: none;
    }
    ul.ul-lager li span:nth-child(1){
        width: auto !important;
        padding-right: 20px;
    }
</style>
<style>
    .remark_popup{
        cursor: pointer;

    }
    #mypopup .modal-dialog {
        max-width: 500px;
         min-height: 600px;
    }
    #mypopup .center
    {
        text-align: center;
    }
    /*
    td.show-tooltip {
        position: inherit;
    }
    .show-tooltip:hover .tooltip {
        z-index: 999999;
    }

    .show-tooltip:hover .tooltip {
        width: 640px;
    }

    .show-tooltip:hover .tooltip p {
        width: auto;
    }

    .interview {
        border: 1px solid #808080;
    }

    .tooltip {
        position: absolute;
        left: -2000px;
        background-color: #dedede;
        padding: 5px;
        border: 1px solid #fff;
        width: 640px;
        z-index: 9999;
    }
    .tooltip p {
        margin: 0;
        padding: 0;
        color: #fff;
        background-color: #222;
        padding: 10px 10px;
        width: 100%;
    }
    .tooltip-contenct{
        min-height: 50px;
    }
    .tooltip .title, .tooltip-contenct {
        margin: 0;
        padding: 0;
        color: #fff;
        background-color: #222;
        padding: 10px 10px;
        width: 100%;
        border-bottom: 1px solid #808080;


    }


    .data-tooltip {
        display: none;
    }

    @media only screen and (min-width: 320px) and (max-width: 767px) {


        .tooltip div {
            width: auto;
            overflow-x: scroll;
        }

        p.datetime {
            font-size: 12px;
        }

        .show-tooltip:hover .tooltip {
            width: 85%;
        }
    }
    */
</style>
<div class="row m-b15">
    <!--
    <input type="button" value="実施日一覧" id="bnt_close" name="" onclick="window.location.href='<?php echo $this->Url->build(["controller" => "Nursery","action" => "schedule"]);?>'" class="btn">
-->
</div>

<!-- Nursery-search -->
<div class="user_search">
    <?=$this->Form->create(null, ['url'=>['action'=>'reserve'], 'id' => 'search-form'])?>
    <?=$this->Form->hidden('search',['value'=>'1'])?>
    <?=$this->Form->hidden('orderby',['value'=>'1','id' =>'orderby'])?>
    <div class="row">
        <div class="col-md-6">
            <div class="row ">
                <div class="form-group col-md-12">
                    <div class="u-col txt-10">ユーザID</div>
                    <div class="u-col txt-20">
                        <input class="txt-60" type="text" value="<?=$user_id?>" id="search_user_id" name="user_id" />
                    </div>
                </div>
            </div>
            <div class="row ">
                <div class="form-group col-md-12">
                    <div class="u-col txt-10">氏名かな</div>
                    <div class="u-col txt-20">
                        <input class="txt-60" type="text" value="<?=$user_name?>" id="search_user_name" name="user_name" />
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-2">ステータス</div>
                <div class="col-md-10">

                    <div class="row">
                        <input type="radio" id="search_check" name="ck_approval"  value="-1" <?= $ck_approval==-1?'checked':'' ?> >
                        <label for="search_check" >　全て</label>
                    </div>
                    <div class="row">
                        <input type="radio" id="search_check_0" name="ck_approval"  value="0" <?= $ck_approval==0?'checked':'' ?> >
                        <label for="search_check_0" >　仮予約</label>
                    </div>
                    <!--
                    <div class="row">
                        <input type="radio" id="search_check_0" name="ck_approval"  value="0" <?= $ck_approval==0?'checked':'' ?> >
                        <label for="search_check_0" >　受領連絡</label>
                    </div>
                    -->
                    <div class="row">
                        <input type="radio" id="search_check_1" name="ck_approval"  value="1" <?= $ck_approval==1?'checked':'' ?> >
                        <label for="search_check_1" >　承認</label>
                    </div>
                    <div class="row">
                        <input type="radio" id="search_check_2" name="ck_approval"  value="2" <?= $ck_approval==2?'checked':'' ?> >
                        <label for="search_check_2" >  　非承認</label>
                    </div>
                    <div class="row">
                        <input type="radio" id="search_check_3" name="ck_approval"  value="3"  <?= $ck_approval==3?'checked':'' ?>>
                        <label for="search_check_3" >  　キャンセル
                        </label>
                    </div>
                    <div class="row">
                        <input type="radio" id="search_check_4" name="ck_approval"  value="4"  <?= $ck_approval==4?'checked':'' ?>>
                        <label for="search_check_4" >  　キャンセル（承認後）

                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row m-b15">
        <div class="u-col txt-15">予約日付</div>
        <div class="u-col txt-80">
            <input class="txt-15 text-date" type="text" value="<?=$timeStart?>" name="time_start" id="search_time_start" readonly/>
            &nbsp;～&nbsp;
            <input class="txt-15 text-date" type="text" value="<?=$timeEnd?>" name="time_end" id="search_time_end" readonly/>

        </div>
    </div>
    <div class="row">
        <div class="u-col txt-80">

            <input class="btn pright" type="submit" value="検索"><span class="pright">&nbsp;&nbsp;</span><input class="btn pright" type="button" value="クリア" onclick="clearSearchCondition();">
        </div>
    </div>
    <?=$this->Form->end()?>
</div>
<!-- End Nursery-search -->
<div class="show-time">
    <?= $this->Flash->render() ?>
</div>
<?= $this->Form->create(null,['class'=>'','id'=>'csvfrm','method'=>'post','url'=>'']) ?>
<div class="row m-b15">
    <!--- Show All authority -->
    <?php if(!empty($loginInfo) ): ?>
        <input type="button" value="CSVダウンロード" name="" class="btn" id='csv'>
    <?php endif; ?>
    <div class="minfrm-right">
    </div>
</div>
<div class="row">

        <div class="min-frm-left h-checkbox w-20-p">
            <input type="checkbox" class="check" value="" id="01-00" /><label for="01-00">全てチェックをつける／はずす</label>
        </div>

            <div class="min-frm-left h-checkbox w-20-p">
                <input type="checkbox" name="download_all" value="1" id="download_all" /><label for="download_all">検索結果をすべてダウンロード</label>
            </div>
    <div class="pager">
        <p class="pagerlist">
            <?= $this->Html->shtCommonPagingCus($this->Paginator, $pagingConfig); ?></p>
        </p>
    </div>
</div>
<!-- Res Table -->
<div class="res-tbl">
    <table class="tbl" cellpadding="0" cellspacing="0" summary="新規登録">
        <thead>
        <tr>
            <th scope="col">チェック	</th>
            <th scope="col">ユーザID
                <?=$this->Paginator->sort('user_id',
                    '<img src="/img/icon01.png" alt="down"  id="user_id_asc" />',
                    ['escape' => false]
                )?>
                <?=$this->Paginator->sort('user_id',
                    '<img src="/img/icon02.png" alt="up" id="user_id_desc" />',
                    ['escape' => false]
                )?>
            </th>
            <th scope="col">メンバー名
                <?=$this->Paginator->sort('user_name',
                    '<img src="/img/icon01.png" alt="down" id="user_name_asc" />',
                    ['escape' => false]
                )?>
                <?=$this->Paginator->sort('user_name',
                    '<img src="/img/icon02.png" alt="up" id="user_name_desc" />',
                    ['escape' => false]
                )?>
            </th>
            <!--<th scope="col">緊急連絡先電話番号</th> -->

            <th scope="col">予約日付
                <?=$this->Paginator->sort('reserve_date',
                    '<img src="/img/icon01.png" alt="down" id="reserve_date_asc" />',
                    ['escape' => false]
                )?>
                <?=$this->Paginator->sort('reserve_date',
                    '<img src="/img/icon02.png" alt="up" id="reserve_date_desc" />',
                    ['escape' => false]
                )?>
            </th>

            <th scope="col">時間帯</th>

            <th scope="col">申込日時
                <?=$this->Paginator->sort('created_date',
                    '<img src="/img/icon01.png" alt="down" id="created_date_asc"  />',
                    ['escape' => false]
                )?>
                <?=$this->Paginator->sort('created_date',
                    '<img src="/img/icon02.png" alt="up" id="created_date_desc" />',
                    ['escape' => false]
                )?>
            </th>
            <th scope="col">最終更新日時
                <?=$this->Paginator->sort('modified_date',
                    '<img src="/img/icon01.png" alt="down" id="modified_date_asc" />',
                    ['escape' => false]
                )?>
                <?=$this->Paginator->sort('modified_date',
                    '<img src="/img/icon02.png" alt="up" id="modified_date_desc" />',
                    ['escape' => false]
                )?>
            </th>
            <th scope="col">回数／月
                <?=$this->Paginator->sort('total',
                    '<img src="/img/icon01.png" alt="down" id="total_asc" />',
                    ['escape' => false]
                )?>
                <?=$this->Paginator->sort('total',
                    '<img src="/img/icon02.png" alt="up" id="total_desc" />',
                    ['escape' => false]
                )?>
            </th>


            <th scope="col">お子さんの性別</th>

            <th scope="col">お子さんの月齢</th>
            <th scope="col">利用目的</th>
            <th scope="col" style="width: 170px">その他</th>
            <th scope="col">承認</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($nurseryreserves as $item) : ?>
        <tr>
            <td><input class="lim-check" type="checkbox" name="arrChk[]" value="<?= $item->id ?>" id="<?= $item->id ?>"/>	</td>
            <td data-title="ユーザID"><?= $item->user_id?></td>
            <td data-title="メンバー名"><a href="<?=Configure::read('FUEL_ADMIN_URL') . 'admin/users/show/'.$item->user_id?>" title="<?= $item->user_name?>"><?= $item->user_name?></a></td>
            <!--<td data-title="緊急連絡先電話番号"><?= $item->phone ?></td>-->

            <td data-title="予約日付"><?= $item->reserve_date ? date("Y年m月d日", strtotime($item->reserve_date)) : '' ?> （<?=$arr_day_of_week[date("D", strtotime($item->reserve_date))]?>）</td>
            <td data-title="希望時間"><?= substr_replace($item->reserve_time_start,':',2,0)?>～<?= substr_replace($item->reserve_time_end,':',2,0)?></td>
            <td data-title="メンバー名"><?= $item->created_date ? date("Y年m月d日", strtotime($item->created_date)) : '' ?> （<?=$arr_day_of_week[date("D", strtotime($item->created_date))]?>）<?= date_format($item->created_date, 'H:i') ?></td>
            <td data-title="最終更新日" >
                <?= $item->modified_date ? date("Y年m月d日", strtotime($item->modified_date)) : '' ?> （<?=$arr_day_of_week[date("D", strtotime($item->modified_date))]?>） <?= date_format($item->modified_date, 'H:i') ?>
            </td>

            <td><?= $item->total ? $item->total:'0' ?></td>
            <td data-title="お子さんの性別"><?=($item->sex1==1)?'男の子': (($item->sex1==2) ?'女の子':'')?><?php if ($item->sex2) :?><br/><?=($item->sex2==1)?'男の子':(($item->sex2==2) ?'女の子':'')?><?php endif;?></td>
            <td data-title="お子さんの月齢"><?php
                $age_year1= (!isset($item->age_year1)?'':(($item->age_year1==0)?'':$item->age_year1.'歳'));
                $age_month1= (!isset($item->age_month1)?'':(($item->age_month1==0)?'':$item->age_month1.'ヶ月'));
                    echo $age_year1.$age_month1;
                ?><br/><?php
                $age_year2= (!isset($item->age_year2)?'':(($item->age_year2==0)?'':$item->age_year2.'歳'));
                $age_month2= (!isset($item->age_month2)?'':(($item->age_month2==0)?'':$item->age_month2.'ヶ月'));
                echo $age_year2.$age_month2;
                ?></td>
            <td data-title="利用目的"><?=$arr_child_purpose[$item->purpose] ?></td>
            <td data-title="その他">
                <span>電話番号：
                    <?php if (isset($item->phone) && !empty($item->phone)) { ?>
                        <a class="remark_popup" data-title="電話番号" data-value="<?= (isset($item->phone) && !empty($item->phone))?$item->phone:'なし' ?>">  <?= (isset($item->phone) && !empty($item->phone))?'あり':'なし' ?> </a>
                    <?php }else {?>
                        なし
                    <?php } ?>

                </span>
              <br/>
                <span>留意事項：
                    <?php if (isset($item->remarks) && !empty($item->remarks)) { ?>
                        <a class="remark_popup" data-title="その他留意事項" data-value="<?= (isset($item->remarks) && !empty($item->remarks))?$item->remarks:'なし' ?>">  <?= (isset($item->remarks) && !empty($item->remarks))?'あり':'なし' ?> </a>
                    <?php }else {?>
                        なし
                    <?php } ?>
                </span><br/>
              <span> 連絡時追加文言：
                   <?php if ((isset($item->approval_text) && !empty($item->approval_text)) && in_array($item->approval,[1,2]))  {
                       $title='';
                       if ($item->approval==1)
                           $title ='承認メッセージ';
                       elseif ($item->approval==2)
                           $title ='非承認メッセージ';
                       ?>
                       <a class="remark_popup" data-title="<?= $title ?>" data-value="<?= (isset($item->approval_text) && !empty($item->approval_text))?$item->approval_text:'なし' ?>"> <?= (isset($item->approval_text) && !empty($item->approval_text))?'あり':'なし' ?></a>
                   <?php } else { ?>
                       なし
                   <?php }?>

              </span>
            </td>
            <td data-title="承認" class="show-tooltip" data-id="<?= $item->id?>" >

            <?php if ($item->approval==0) { ?>
                    <select class="ad-select select-1 approval"
                    data-id="<?=$item->id?>" data-approval="<?= $item->approval?>" data-status="<?= $item->status?>"
                    data-user_name="<?= $item->user_name?>" data-reserve_date="<?= $item->reserve_date ? date("Y年m月d日", strtotime($item->reserve_date)) : '' ?>"
                    data-day_of_week=" (<?=$arr_day_of_week[date("D", strtotime($item->reserve_date))]?>)"
                    data-reserve_time="<?= substr_replace($item->reserve_time_start,':',2,0)?>～<?= substr_replace($item->reserve_time_end,':',2,0)?>"
                    data-phone="<?=$item->phone?>"
                    data-remark="<?= $item->remarks ? nl2br($item->remarks) : '' ?>"
                    data-purpose="<?=$arr_child_purpose[$item->purpose] ?>" data-name_k1="<?= $item->name_k1   ?>"
                    data-sex1="<?=$item->sex1==1?'(男の子)':($item->sex1==2?'(女の子)':'') ?>"
                    data-name_k2="<?= $item->name_k2 ?>" data-sex2="<?=$item->sex2==1?'(男の子)':($item->sex2==2?'(女の子)':'') ?>"
                    data-age1="<?= (!isset($item->age_year1)?'':(($item->age_year1==0)?'':$item->age_year1.'歳')) ?><?=(!isset($item->age_month1)?'':(($item->age_month1==0)?'':$item->age_month1.'ヶ月')) ?>"
                    data-age2="<?= (!isset($item->age_year2)?'':(($item->age_year2==0)?'':$item->age_year2.'歳')) ?><?= (!isset($item->age_month2)?'':(($item->age_month2==0)?'':$item->age_month2.'ヶ月')) ?>"
                    id="approval_<?=$item->id?>" name="approval" data-childs="<?= (isset($item->sex2) && !empty(isset($item->sex2)))?'2':'1' ?>">
                    <option value="0" <?= $item->approval==0?'selected="selected"':''?> ><?= $arr_child_approval[0]?></option>
                    <option value="1" <?= $item->approval==1?'selected="selected"':''?> ><?= $arr_child_approval[1]?></option>
                    <option value="2" <?= $item->approval==2?'selected="selected"':''?>><?= $arr_child_approval[2]?></option>
                    <option value="3" <?= $item->approval==3?'selected="selected"':''?>><?= $arr_child_approval[3]?></option>
                </select>
                    <?php
                    } elseif($item->approval==1) {
                        ?>
                        <select class="ad-select select-1 approval"
                            data-id="<?=$item->id?>" data-approval="<?= $item->approval?>" data-status="<?= $item->status?>"
                            data-user_name="<?= $item->user_name?>" data-reserve_date="<?= $item->reserve_date ? date("Y年m月d日", strtotime($item->reserve_date)) : '' ?>"
                            data-day_of_week=" (<?=$arr_day_of_week[date("D", strtotime($item->reserve_date))]?>)"
                            data-reserve_time="<?= substr_replace($item->reserve_time_start,':',2,0)?>～<?= substr_replace($item->reserve_time_end,':',2,0)?>"
                            data-phone="<?=$item->phone?>"
                            data-remark="<?= $item->remarks ? nl2br($item->remarks) : '' ?>"
                            data-purpose="<?=$arr_child_purpose[$item->purpose] ?>" data-name_k1="<?= $item->name_k1   ?>"
                            data-sex1="<?=$item->sex1==1?'(男の子)':($item->sex1==2?'(女の子)':'') ?>"
                            data-name_k2="<?= $item->name_k2 ?>" data-sex2="<?=$item->sex2==1?'(男の子)':($item->sex2==2?'(女の子)':'') ?>"
                            data-age1="<?= (!isset($item->age_year1)?'':(($item->age_year1==0)?'':$item->age_year1.'歳')) ?><?=(!isset($item->age_month1)?'':(($item->age_month1==0)?'':$item->age_month1.'ヶ月')) ?>"
                            data-age2="<?= (!isset($item->age_year2)?'':(($item->age_year2==0)?'':$item->age_year2.'歳')) ?><?= (!isset($item->age_month2)?'':(($item->age_month2==0)?'':$item->age_month2.'ヶ月')) ?>"
                            id="approval_<?=$item->id?>" name="approval" data-childs="<?= (isset($item->sex2) && !empty(isset($item->sex2)))?'2':'1' ?>">
                            <option value="1" <?= $item->approval==1?'selected="selected"':''?> ><?= $arr_child_approval[1]?></option>
                            <option value="4" <?= $item->approval==4?'selected="selected"':''?>><?= $arr_child_approval[4]?></option>
                        </select>
                        <?php
                    } else {
                    echo $arr_child_approval[$item->approval];
                    } ?>
                <!--
                <select class="ad-select select-1 approval"
                        data-id="<?=$item->id?>" data-approval="<?= $item->approval?>" data-status="<?= $item->status?>"
                        data-user_name="<?= $item->user_name?>" data-reserve_date="<?= $item->reserve_date ? date("Y年m月d日", strtotime($item->reserve_date)) : '' ?>"
                        data-day_of_week=" (<?=$arr_day_of_week[date("D", strtotime($item->reserve_date))]?>)"
                        data-reserve_time="<?= substr_replace($item->reserve_time_start,':',2,0)?>～<?= substr_replace($item->reserve_time_end,':',2,0)?>"
                        data-phone="<?=$item->phone?>"
                        data-remark="<?= $item->remarks ? nl2br($item->remarks) : '' ?>"
                        data-purpose="<?=$arr_child_purpose[$item->purpose] ?>" data-name_k1="<?= $item->name_k1   ?>"
                        data-sex1="<?=$item->sex1==1?'(男の子)':($item->sex1==2?'(女の子)':'') ?>"
                        data-name_k2="<?= $item->name_k2 ?>" data-sex2="<?=$item->sex2==1?'(男の子)':($item->sex2==2?'(女の子)':'') ?>"
                        data-age1="<?= (!isset($item->age_year1)?'':(($item->age_year1==0)?'':$item->age_year1.'歳')) ?><?=(!isset($item->age_month1)?'':(($item->age_month1==0)?'':$item->age_month1.'ヶ月')) ?>"
                        data-age2="<?= (!isset($item->age_year2)?'':(($item->age_year2==0)?'':$item->age_year2.'歳')) ?><?= (!isset($item->age_month2)?'':(($item->age_month2==0)?'':$item->age_month2.'ヶ月')) ?>"
                        id="approval_<?=$item->id?>" name="approval" data-childs="<?= (isset($item->sex2) && !empty(isset($item->sex2)))?'2':'1' ?>">
                    <option value="0" <?= $item->approval==0?'selected="selected"':''?> ><?= $arr_child_approval[0]?></option>
                    <option value="1" <?= $item->approval==1?'selected="selected"':''?> ><?= $arr_child_approval[1]?></option>
                    <option value="2" <?= $item->approval==2?'selected="selected"':''?>><?= $arr_child_approval[2]?></option>
                    <option value="3" <?= $item->approval==3?'selected="selected"':''?>><?= $arr_child_approval[3]?></option>
                    <option value="4" <?= $item->approval==4?'selected="selected"':''?>><?= $arr_child_approval[4]?></option>
                </select>

                <?php if ($item->reserve_date <$now):?>
                    <?php if ($item->status==2) {?>
                         <span>キャンセル</span>
                    <?php } elseif($item->status==1 || $item->status==0) {
                         ?>
                         <span><?= $arr_child_approval[$item->approval] ?></span>
                        <?php
                    } ?>
                <?php elseif ($item->status == 2 && $item->approval ==4):?>
                    <?= $arr_child_approval[4]?>
                <?php elseif ($item->status == 2):?>
                    <?= $arr_child_approval[3]?>
                <?php else:?>
                <?php if ($item->approval==0): ?>
                    <select class="ad-select select-1 approval"
                            data-id="<?=$item->id?>" data-approval="<?= $item->approval?>" data-status="<?= $item->status?>"
                            data-user_name="<?= $item->user_name?>" data-reserve_date="<?= $item->reserve_date ? date("Y年m月d日", strtotime($item->reserve_date)) : '' ?>"
                            data-day_of_week=" (<?=$arr_day_of_week[date("D", strtotime($item->reserve_date))]?>)"
                            data-reserve_time="<?= substr_replace($item->reserve_time_start,':',2,0)?>～<?= substr_replace($item->reserve_time_end,':',2,0)?>"
                            data-phone="<?=$item->phone?>"
                            data-remark="<?= $item->remarks ? nl2br($item->remarks) : '' ?>"
                            data-purpose="<?=$arr_child_purpose[$item->purpose] ?>" data-name_k1="<?= $item->name_k1   ?>"
                            data-sex1="<?=$item->sex1==1?'(男の子)':($item->sex1==2?'(女の子)':'') ?>"
                            data-name_k2="<?= $item->name_k2 ?>" data-sex2="<?=$item->sex2==1?'(男の子)':($item->sex2==2?'(女の子)':'') ?>"
                            data-age1="<?= (!isset($item->age_year1)?'':(($item->age_year1==0)?'':$item->age_year1.'歳')) ?><?=(!isset($item->age_month1)?'':(($item->age_month1==0)?'':$item->age_month1.'ヶ月')) ?>"
                            data-age2="<?= (!isset($item->age_year2)?'':(($item->age_year2==0)?'':$item->age_year2.'歳')) ?><?= (!isset($item->age_month2)?'':(($item->age_month2==0)?'':$item->age_month2.'ヶ月')) ?>"
                            id="approval_<?=$item->id?>" name="approval" data-childs="<?= (isset($item->sex2) && !empty(isset($item->sex2)))?'2':'1' ?>">
                        <option value="0" selected="selected"><?= $arr_child_approval[0]?></option>
                        <option value="1" ><?= $arr_child_approval[1]?></option>
                        <option value="2"><?= $arr_child_approval[2]?></option>
                        <option value="3"><?= $arr_child_approval[3]?></option>
                    </select>
                    <?php elseif($item->approval==1):?>
                    <select class="ad-select select-1 approval"
                            data-id="<?=$item->id?>" data-approval="<?= $item->approval?>" data-status="<?= $item->status?>"
                            data-user_name="<?= $item->user_name?>" data-reserve_date="<?= $item->reserve_date ? date("Y年m月d日", strtotime($item->reserve_date)) : '' ?>"
                            data-day_of_week=" (<?=$arr_day_of_week[date("D", strtotime($item->reserve_date))]?>)"
                            data-reserve_time="<?= substr_replace($item->reserve_time_start,':',2,0)?>～<?= substr_replace($item->reserve_time_end,':',2,0)?>"
                            data-phone="<?=$item->phone?>"
                            data-remark="<?= $item->remarks ? nl2br($item->remarks) : '' ?>"
                            data-purpose="<?=$arr_child_purpose[$item->purpose] ?>" data-name_k1="<?= $item->name_k1   ?>"
                            data-sex1="<?=$item->sex1==1?'(男の子)':($item->sex1==2?'(女の子)':'') ?>"
                            data-name_k2="<?= $item->name_k2 ?>" data-sex2="<?=$item->sex2==1?'(男の子)':($item->sex2==2?'(女の子)':'') ?>"
                            data-age1="<?= (!isset($item->age_year1)?'':(($item->age_year1==0)?'':$item->age_year1.'歳')) ?><?=(!isset($item->age_month1)?'':(($item->age_month1==0)?'':$item->age_month1.'ヶ月')) ?>"
                            data-age2="<?= (!isset($item->age_year2)?'':(($item->age_year2==0)?'':$item->age_year2.'歳')) ?><?= (!isset($item->age_month2)?'':(($item->age_month2==0)?'':$item->age_month2.'ヶ月')) ?>"
                            id="approval_<?=$item->id?>" name="approval" data-childs="<?= (isset($item->sex2) && !empty(isset($item->sex2)))?'2':'1' ?> ">
                        <option value="1" selected="selected"><?= $arr_child_approval[1]?></option>
                        <option value="4"><?= $arr_child_approval[4]?></option>
                    </select>
                    <?php elseif($item->approval==2 ):?>
                    <?= $arr_child_approval[$item->approval] ?>

                <?php endif;?>

                <?php endif;?>
                -->
            </td>
        </tr>
        <?php endforeach;?>

        </tbody>
    </table>
</div>
<!-- End Res Table -->
<div class="row">
    <div class="pager">
        <p class="pagerlist">
            <?= $this->Html->shtCommonPagingCus($this->Paginator, $pagingConfig); ?></p>
        </p>
    </div>
</div>
<input type="hidden" value="0" name="hid_csv" class="" id="hid_csv">
<?=$this->Form->hidden('csvwhere',['value'=>$csvwhere])?>
<?=$this->Form->hidden('csvsort',['value'=>$csvsort ])?>
<?= $this->Form->end() ?>
<!-- Modal -->
<div id="myModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="btn_close" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span id="md_title"></span></h4>

            </div>
            <div class="modal-body">
                <div class="fm-text">

                    <div class="show-time">
                        <span class="fm-submit" id="md_msg"  href="javascript:void(0)">メッセージ表示エリア</span>
                    </div>
                    <ul class="p-none m-b15">
                        <li><span>ユーザ名</span>:<span id="md_username"></span></li>
                        <li><span>利用目的</span>:<span id="md_purpose"></span></li>
                        <li><span>利用日</span>:<span id="md_reserve_date"></span></li>
                        <li><span>利用時間</span>:<span id="md_reserve_time"></span></li>
                        <li><span>連絡可能電話番号</span>:<span id="md_phone"></span></li>
                        <li><span>留意事項</span>:<div id="md_remark"></div></li>
                    </ul>

                    <p class="m-none">お子さんの情報</p>
                    <ul class="ul-lager">
                        <li><span id="md_name_k1"></span><span id="md_age1"></span></li>
                        <li><span id="md_name_k2"></span><span id="md_age2"></span></li>
                    </ul>
                    <p>メール追加連絡</p>
                </div>
                <div class="fm-form clearfix">
                    <form action="">
                        <textarea name="" id="md_mail_contact" cols="5" rows="5"></textarea>
                        <div class="pright">
                            <input type="button" id="md_submit" value="YES" name="" class="btn">
                            <input type="button" value="NO" id="md_cancel" name="" class="btn" data-dismiss="modal">
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<!--end Modal -->
<!-- Modal -->
<div id="mypopup" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="btn_close" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span id="mypopup_md_title"></span></h4>
            </div>
            <div class="modal-body">

                <div class="fm-form clearfix">
                    <form action="">
                        <textarea name="" readonly="readonly" id="mypopup_md_value" cols="5" rows="10"></textarea>
                        <div class="center">
                            <input type="button" value="閉じる" id="md_cancel" name="" class="btn" data-dismiss="modal">
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<!--end Modal -->



<!--end Modal -->
<div id="dialog-cancel" style="display:none;">
    <p style="text-align: center; margin-top: 10px; font-weight: bold;">
        予約をキャンセルします。よろしいですか？
    </p>
</div>
<?= $this->Form->create(null,['class'=>'','id'=>'cancelfrm','method'=>'post','url'=>''.'?page='.$page]) ?>
<input type="hidden" value="0" name="nursery_reserves_id" class="" id="nursery_reserves_id">

<input type="hidden" value="1" name="cancel" class="" id="cancel">
<input type="hidden" value="0" name="approval" class="" id="approval">
<input type="hidden" value="0" name="status" class="" id="status">
<input type="hidden" value="" name="mail_contact" class="" id="mail_contact">
<input type="hidden" value="1" name="childs" class="" id="childs">
<?= $this->Form->end() ?>

<script>
    var click =false;
    $(document).ready(function () {
        $('.remark_popup').click(function () {
            $('#mypopup_md_title').html('');
            $('#mypopup_md_value').html('');
            $('#mypopup_md_title').html($(this).attr('data-title'));
            $('#mypopup_md_value').html($(this).attr('data-value'));
            $('#mypopup').modal('show') ;
        })
        $(".check").click(function () {
            $('input:checkbox').not(this).not('#download_all').prop('checked', this.checked);
        });
        $("#csv").click(function () {
            $("#hid_csv").val(1);
            $('#csvfrm').submit();
        });
        calendar.set("search_time_start");
        calendar.set("search_time_end");
        var value_curent;
        var value_old;
        var select;
        $('.approval').on('click',function () {
            value_curent = $(this).val();

            $('#nursery_reserves_id').val($(this).attr('data-id'));
            select=$(this);
            value_old=$(this).attr('data-status');
        }).change(function () {
            $(this).find(":selected").each(function () {

                var value_change = $(this).val();
                if(value_change === '3')
                {
                    $("#dialog-cancel").dialog({
                        resizable: false,
                        height: "auto",
                        width: 400,
                        modal: true,
                        buttons: {
                            "OK": function () {
                                $(this).dialog("close");
                                $('#approval').val(3);
                                $('#status').val(2);
                                $('#cancelfrm').submit();
                            },
                            //Cancel
                            'キャンセル':function () {
                                $(select).val($(select).attr('data-approval'));
                                $(this).dialog("close");
                            }
                        },
                        closeOnEscape: false,
                        open: function (event, ui) {
                            $(".ui-dialog-titlebar", ui.dialog | ui).hide();
                        }
                    });
                }
                else if (value_change === '1' && $(select).attr('data-approval') !=='1')
                {
                    $('#approval').val(1);
                    $('#childs').val($(select).attr('data-childs'));
                    $('#status').val(1);
                    $('#md_age2').html($(select).attr('data-age2'));
                    $('#md_age1').html($(select).attr('data-age1'));
                    $('#md_name_k2').html($(select).attr('data-name_k2') +' ' +$(select).attr('data-sex2')  );
                    $('#md_name_k1').html($(select).attr('data-name_k1')  +' ' +$(select).attr('data-sex1'));
                    $('#md_purpose').html($(select).attr('data-purpose'));
                    $('#md_reserve_time').html($(select).attr('data-reserve_time'));
                    $('#md_reserve_date').html($(select).attr('data-reserve_date') + $(select).attr('data-day_of_week'));
                    $('#md_phone').html($(select).attr('data-phone'));
                    $('#md_remark').html($(select).attr('data-remark'));
                    $('#md_username').html($(select).attr('data-user_name'));
                    $('#md_title').html('承認フォーム');
                    $('#md_msg').html('このユーザに承認の連絡をします。よろしいですか？');
                    $('#myModal').modal('show') ;
                }
                else if (value_change=== '2')
                {
                    $('#approval').val(2);
                    $('#childs').val($(select).attr('data-childs'));
                    $('#status').val(1);
                    $('#md_age2').html($(select).attr('data-age2'));
                    $('#md_age1').html($(select).attr('data-age1'));
                    $('#md_name_k2').html($(select).attr('data-name_k2') +' ' +$(select).attr('data-sex2')  );
                    $('#md_name_k1').html($(select).attr('data-name_k1')  +' ' +$(select).attr('data-sex1'));
                    $('#md_purpose').html($(select).attr('data-purpose'));
                    $('#md_reserve_time').html($(select).attr('data-reserve_time'));
                    $('#md_reserve_date').html($(select).attr('data-reserve_date') + $(select).attr('data-day_of_week'));
                    $('#md_phone').html($(select).attr('data-phone'));
                    $('#md_remark').html($(select).attr('data-remark'));
                    $('#md_username').html($(select).attr('data-user_name'));
                    $('#md_msg').html('このユーザに非承認の連絡をします。よろしいですか？');
                    $('#md_title').html('非承認フォーム');
                    $('#myModal').modal('show') ;
                }
                else if (value_change === '4'){
                    $("#dialog-cancel").dialog({
                        resizable: false,
                        height: "auto",
                        width: 400,
                        modal: true,
                        buttons: {
                            "OK": function () {
                                $(this).dialog("close");
                                $('#approval').val(4);
                                $('#status').val(2);
                                $('#cancelfrm').submit();
                            },
                            //Cancel
                            'キャンセル':function () {
                                $(select).val($(select).attr('data-approval'));
                                $(this).dialog("close");
                            }
                        },
                        closeOnEscape: false,
                        open: function (event, ui) {
                            $(".ui-dialog-titlebar", ui.dialog | ui).hide();
                        }
                    });
                }
            });
        });


        $('#md_submit').click(function () {

            $('#mail_contact').val($('#md_mail_contact').val());
            $('#myModal').modal('hide') ;
            $('#cancelfrm').submit();
        });
        $('#btn_close,#md_cancel').click(function () {
            $(select).val($(select).attr('data-approval'));
        });

        $("#myModal").on("hidden.bs.modal", function () {
            $(select).val($(select).attr('data-approval'));
        });
        /*
        var value_curent;
        $('.select-1').on('click', function () {
            value_curent = $(this).val();
        }).change(function () {
            $(this).find(":selected").each(function () {
                var value_change = $(this).val();
                if (value_curent === '1' && value_change === '2')
                    $('#myModal').modal('show');
            });
        });
        */
    });

    function clearSearchCondition(){
        $('#search_user_id').val('');
        $('#search_user_name').val('');
        $('#search_time_start').val('');
        $('#search_time_end').val('');
        $('#orderby').val('0');

        $('#search_check').attr('checked', false);
        $('#search_check_0').attr('checked', false);
        $('#search_check_1').attr('checked', false);
        $('#search_check_2').attr('checked', false);
        $('#search_check_3').attr('checked', false);
        $('#search_check_4').attr('checked', false);
        $('#search-form').submit();
    }
</script>

<!-- Tooltip-->
<script>
    var isMobile = false;
    if (jQuery.browser.mobile) {
        isMobile = true;
    }
    function simple_tooltip(target_items, name) {
        $(target_items).each(function (i) {
            var my_tooltip = $("#tooltip_" + $(this).attr('data-id'));
            var top = $(this).position().top;
            var left = $(this).position().left;
            var linkWidth = $(this).outerWidth();
            var marginTop = 150;
            if ($(this).attr("data-id") != "" && $(this).attr("data-id") != "undefined") {
                if (!isMobile) {
                    $(this).mouseover(function () {
                        my_tooltip.css({ opacity: 1, display: "none" }).fadeIn(400);
                    }).mousemove(function (kmouse) {
                        var border_top = $(window).scrollTop();

                        var border_right = $(window).width();
                        var left_pos;
                        var top_pos;
                        var offset = 50;
                        if (border_right - (350) >= my_tooltip.width() + kmouse.pageX) {
                            left_pos = (kmouse.pageX + offset) - (left / 2);
                        } else {
                            left_pos = my_tooltip.width() + offset;
                        }
                        if (border_top + (offset * 2) >= kmouse.pageY - my_tooltip.height()) {

                            top_pos = border_top + offset;
                            if (top_pos > border_top) {
                                top_pos = border_top;
                            }

                        } else {

                            top_pos = kmouse.pageY - my_tooltip.height() - offset;
                        }
                        var toptooltip = 0;
                        if (top_pos > (top + marginTop)) {
                            toptooltip = (top + marginTop);

                        }
                        else {
                            toptooltip = top_pos;

                        }
                        toptooltip = top_pos;
                        my_tooltip.css({ left: left_pos, top: toptooltip+offset + "px", 'z-index': 99999999, 'display': 'block' });
                    }).mouseout(function () {
                      my_tooltip.css({ left: "-9999px" });
                    });
                }
                else {
                    $(this).click(function (e) {

                        var offset = 5;
                        my_tooltip.css({ opacity: 1, left: 20, top: e.target.offsetTop + e.target.clientHeight + offset + "px", 'z-index': 99999,'position':'fixed' }).fadeIn(400);
                    }).mouseout(function () {
                        my_tooltip.hide();
                    });
                }


            }

        });
    }
    var sort_active= '<?= $pagingConfig['sort'].'_'.$pagingConfig['direction']  ?>';
    var direction = '<?= $pagingConfig['direction']  ?>';
    $(document).ready(function () {
      //  simple_tooltip(".show-tooltip");
        $('#'+sort_active).attr("src","/img/sort_"+direction+"_active.png");
        $('*').click(function (e) {

            if (!$(e.target).hasClass("show-tooltip")) {
               // $('.data-tooltip').hide();
            }
        });

    });

</script>
