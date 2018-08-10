<style>
    .no-data-message
    {
        padding-top:35px;
        margin-bottom: 100px;
    }
</style>
<?php
	use Cake\Core\Configure;

?>
<link rel="stylesheet" href="<?= $this->Html->getVersion('/css/jquery-ui.css'); ?>" />
<link rel="stylesheet" href="<?= $this->Html->getVersion('/assets-sht2/css/main.css'); ?>" />
<script src="<?= $this->Html->getVersion('/js/jquery-ui.js'); ?>"></script>

<h2 id="page-title" class="clearfix no-margin-bottom">
    <div class="page-title-inner">
      <span class="en">MYPAGE</span>
      <span class="jp">マイページ</span>
    </div>
</h2>

<section id="concierge" class="section-container">
    <div id="mypage">
        <div class="mypage-navi">
            <div class="inner">
                <ul>
                    <li><a href="<?= Configure::read('FUEL_ADMIN_URL') .'mypage/index' ?>">予約・開催済みイベント</a></li>
                    <li><a href="<?= Configure::read('FUEL_ADMIN_URL') .'mypage/profile' ?>">登録情報</a></li>
                    <li class="active"><a href="<?= $this->Url->build(['action'=>'index']) ?>">コンシェルジュ相談申込履歴</a></li>
                    <li><a href="<?= $this->Url->build(['controller' => 'Nursery', 'action'=>'index']) ?>">一時保育サービス予約状況</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="section-inner" style="margin-top: 30px;">
      <div class="section-contents">
        <section class="main-cont">
            <h2 class="title-01"  style="margin-bottom: 0px;">予約状況一覧</h2>
            <div class="show-time"><?= $this->Flash->render() ?></div>
            <?php if ($reserves->count() > 0) { ?>
            <div class="row">
                <div class="pager">
                    <p class="pagerlist">
                      	<?= $this->Html->shtCommonPaging($this->Paginator, $pagingConfig); ?></p>
                    </p>
                </div>
            </div>

            <!-- Res Table -->
            <div class="res-tbl">
                <table class="tbl" cellpadding="0" cellspacing="0" summary="新規登録">
                    <thead>
                        <tr>
                            <th scope="col">予約日付</th>
                            <th scope="col">時間</th>
                            <th scope="col">コンシェルジュ</th>
                            <th scope="col">状態</th>
                            <th scope="col">詳細</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($reserves as $item) { ?>
                        <tr>
                            <?php
                            $workDate = $item->work_date ? date("Y/m/d", strtotime($item->work_date)) : '';
                            $workTimeStart = $item->work_time_start ? substr($item->work_time_start, 0, 2) . ':' . substr($item->work_time_start, 2, 2) : '';
                            $workDateTine = strtotime($workDate . ' ' . $workTimeStart );
                            ?>
                            <td scope="row"><?= $workDate ?></td>
                            <td data-title="時間"><?= $workTimeStart ?></td>
                            <td data-title="コンシェルジュ"><?= $item->cname ? $item->cname : '' ?></td>
                            <td data-title="状態">
                                <?php if(time() > $workDateTine): ?>
                                    <?php
                                    $questionStatus = Configure::read('QUESTION_STATUS');
                                    $questionStatus['cancel'] = 'キャンセル';
                                    $questionStatus['end']    = '済';
                                    $val = '';
                                    if ($item->r_status==9) {
                                        $val = 'cancel';
                                    } else {
                                        $val = 'end';
                                    }
                                    echo isset($questionStatus[$val])?$questionStatus[$val]:'';
                                    ?>
                                <?php else: ?>
                                    <?php
                                    $questionStatus = Configure::read('QUESTION_STATUS');
                                    $questionStatus['cancel'] = 'キャンセル';
                                    $questionStatus['reserve'] = '予約済';		//add Y.Chiba
                                    $val = '';
                                    if ($item->r_status!=9) {
                                        //edit Y.Chiba
                                        //$val = 'r_' . $item->r_status;
                                        $val = 'reserve';
                                    } else {
                                        $val = 'cancel';
                                    }
                                    echo isset($questionStatus[$val])?$questionStatus[$val]:'';
                                    //edit Y.Chiba
                                    //if($item->r_status == 1){
                                    if ($item->r_status!=9) {
                                        echo '&nbsp;&nbsp;<input type="button" value="キャンセル" class="status" 
                                            data-rid="' . ($item->rid ? $item->rid : '') . '" data-sid="' . ($item->sid ? $item->sid : '') . '">';
                                    }
                                    ?>
                                <?php endif; ?>
                            </td>
                            <td data-title="詳細" class="link">
                                <a href="<?= $this->Url->build(['controller' => 'Reserve', 'action' => 'form', '?' => ['id' => $item->sid, 'his' => '111' ,'rs' => $item->rid]])?>">表示</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- End Res Table -->

            <!-- Pager -->
            <div class="pager">
                <p class="pagerlist">
                  	<?= $this->Html->shtCommonPaging($this->Paginator, $pagingConfig); ?></p>
                </p>
            </div>
            <?php }elseif($this->request->session()->check('MYPAGE.ID')){ ?>
                <div class="res-tbl">
                    <table class="tbl" cellpadding="0" cellspacing="0" summary="新規登録">
                        <thead>
                        <tr>
                            <th scope="col">予約日付</th>
                            <th scope="col">時間</th>
                            <th scope="col">コンシェルジュ</th>
                            <th scope="col">状態</th>
                            <th scope="col">詳細</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="show-time"><div class="message no-data-message">予約データはありません</div></div>
            <?php } ?>
        </section>
      </div>
    </div>
</section>

<?= $this->Form->create(null,['class'=>'','id'=>'statusfrm','method'=>'post','url'=>'']) ?>
<div id="dialog-confirm" title="" style="display:none;"><p>キャンセルします。よろしいですか？</p></div>
<input type="hidden" value="" name="hid_rid" class="" id="hid_rid">
<input type="hidden" value="" name="hid_sid" class="" id="hid_sid">
<?= $this->Form->end() ?>

<script type="text/javascript">
    $(document).ready(function() {
        $(".status").click(function () {
             var this_ = $(this);
             $('#hid_rid').val(this_.attr('data-rid'));
             $('#hid_sid').val(this_.attr('data-sid'));
             $("#dialog-confirm").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "OK": function () {
                        $('#statusfrm').submit();
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
        });
    });
</script>
