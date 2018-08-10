<?php
use Cake\Core\Configure;

$childApproval = Configure::read('CHILD_APPROVAL');
$childPerpose = Configure::read('CHILD_PURPOSE');
$months = Configure::read('MONTHS');
$arr_day_of_week = Configure::read('DAYOFWEEK');
?>
<link rel="stylesheet" href="<?= $this->Html->getVersion('/css/jquery-ui.css'); ?>" />
<link rel="stylesheet" href="<?= $this->Html->getVersion('/assets-sht2/css/main.css'); ?>" />
<style>
    .sex_2_cb {
        width: 100% !important;
    }
    .sex_2_cb label{
        width: 100% !important;
        font-weight: bold;
    }
    #sex_2_div {
        margin-top: 20px;
    }
    label.error,
    label.error-time{
        color: #e84c3d;
        display: block;
    }
    label.error-time
    {
        display:none;
    }
    input.error
    {
        background-color: #f3f6f8 !important;
    }
    input::placeholder{
        color: #bababa;
    }
    .fm-privacy-container{
        height: auto;
        overflow: hidden;
        overflow-y:none ;
    }

</style>
<h2 id="page-title" class="clearfix no-margin-bottom">
    <div class="page-title-inner">
        <span class="en">MYPAGE</span>
        <span class="jp">ナビゲーション</span>
    </div>
</h2>
<div class="clearfix"></div>
<div id="user-registration" class="section-container">
    <div id="mypage" class="clearfix">
        <div class="mypage-navi">
            <div class="inner">
                <ul>
                    <li><a href="<?= Configure::read('FUEL_ADMIN_URL') .'mypage/index' ?>">予約・開催済みイベント</a></li>
                    <li><a href="<?= Configure::read('FUEL_ADMIN_URL') .'mypage/profile' ?>">登録情報</a></li>
                    <li><a href="<?= $this->Url->build(['controller' => 'Reserve', 'action'=>'index']) ?>">コンシェルジュ相談申込履歴</a></li>
                    <li class="active"><a href="<?= $this->Url->build(['action'=>'index']) ?>">一時保育サービス予約状況</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="section-inner" style="padding-top: 45px;">
        <div class="section-contents">
            <h2 class="title-01">一時保育サービス仮申し込み</h2>
            <div id ="" class="show-time" >
                <div class="flash-message flash-error" id="error_limit">
                    <button type="button" class="close" onclick="$(this).parent().hide();" aria-hidden="true">×</button>
                    <p>一時保育サービスは1ヶ月最大４件まで予約登録できます</p>
                </div>
            </div>
            <?= $this->Form->create(null, ['id' => 'submit-form']) ?>
                <div class="fm-privacy-container">
                    <h3 class="title">一時保育サービス　概要
                    </h3>
                    <ul class="fm-infotext">
                        <li>保育形態：
                            <ul>
                                <ol>一時保育（定員：４名）</ol>
                            </ul>
                        </li>
                        <li>
                            保育対象：
                            <ul>
                                <ol>満１歳～満６歳（未就学児のみ）
                                </ol>
                            </ul>
                        </li>
                        <li>保育利用単位：
                            <ul>
                                <ol>連続3時間、一世帯あたり１回につき2名様まで</ol>
                            </ul>
                        </li>
                        <li>
                            保育利用回数制限：
                            <ul>
                                <ol>週２回、月４回 まで
                                </ol>
                            </ul>
                        </li>
                        <li>
                            保育日時：
                            <ul>
                                <ol>火曜日・木曜日10：00～21：00 / 土曜日10：00～17：00</ol>
                                <ol>※ただし、年末年始、当施設の休業日はご利用いただけません</ol>
                            </ul>
                        </li>
                        <li>保育料：無料</li>
                        <li>
                            当日のお持ち物：
                            <ul>
                                <ol>一つの紙袋に下記持ち物をまとめて、すべての持ち物には氏名記入をお願いいたします。</ol>
                                <ol>①食事・おやつ　②飲み物　③着替え　④ハンカチ・タオル　⑤バスタオル（お昼寝用）</ol>
                                <ol>⑥オムツ・お尻拭き　⑦だっこひも（必要な場合のみ）</ol>
                                <ol>⑧ビニール袋（使用したオムツを入れます。ごみ等はお持ち帰りいただきます）</ol>
                                <ol>※①②は、お子様がすぐに食べたり、飲むことができる状態でご用意ください。</ol>
                                <ol>キッズスペースには電子レンジや冷蔵庫、お湯・コップ類等はございません。	</ol>
                            </ul>
                        </li>

                    </ul>
                </div>

                <div class="form-wrap fm-content-swap custom-list wid-radio clearfix">
                    <dl class="clearfix">
                        <dt class="required cs-wid">利用目的</dt>
                        <dd>
                            <input type="radio" name="purpose" value="1" id="purpose1" <?= (!empty($this->request->data['purpose']) && $this->request->data['purpose'] == '1') ? 'checked' : '' ?> >
                            <label for="purpose1" class="radio foucus_t" tabindex="23"><?=$childPerpose['1']?></label>
                            <input type="radio" name="purpose" value="2" id="purpose2" <?= (!empty($this->request->data['purpose']) && $this->request->data['purpose'] == '2') ? 'checked' : '' ?> >
                            <label for="purpose2" class="radio foucus_t" tabindex="24"><?=$childPerpose['2']?></label>
                            <input type="radio" name="purpose" value="3" id="purpose3" <?= (!empty($this->request->data['purpose']) && $this->request->data['purpose'] == '3') ? 'checked' : '' ?> >
                            <label for="purpose3" class="radio foucus_t" tabindex="25"><?=$childPerpose['3']?></label>
                            <input type="radio" name="purpose" value="4" id="purpose4" <?= (!empty($this->request->data['purpose']) && $this->request->data['purpose'] == '4') ? 'checked' : '' ?> >
                            <label for="purpose4" class="radio foucus_t" tabindex="26"><?=$childPerpose['4']?></label>
                            <input type="radio" name="purpose" value="5" id="purpose5" <?= (!empty($this->request->data['purpose']) && $this->request->data['purpose'] == '5') ? 'checked' : '' ?> >
                            <label for="purpose5" class="radio foucus_t" tabindex="27" style="width: auto;"><?=$childPerpose['5']?></label>
                            <?= $this->Form->hidden('purpose_validate'); ?>
                        </dd>
                    </dl>
                    <dl class="clearfix">
                        <dt class="required cs-wid">希望日付</dt>
                        <dd class="clearfix">
                            <div class="select smp-half w440 wid-100">
                                <select name="reserve_date" class="foucus_t" id="reserve_date">
                                    <?php
                                    if(!empty($nurserySchedule)){
                                        foreach($nurserySchedule as $reserveDate){
                                            $cd = date_format($reserveDate->reserve_date, 'Y-m-d');
                                            $week = '（' . $arr_day_of_week[date_format($reserveDate->reserve_date, 'w')] . '）';
                                            $selected = (!empty($this->request->data['reserve_date']) && $this->request->data['reserve_date'] == $cd)?'selected':'';
                                            echo '<option data-id="'.$reserveDate->id.'" value="' . $cd . '" ' . $selected . '>' . date_format($reserveDate->reserve_date, 'Y年m月d日') . $week .'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </dd>
                    </dl>

                    <dl class="clearfix">
                        <dt class="required cs-wid">希望時間</dt>
                        <dd>
                            <ul class="fm-cusrow fm-custom-day">
                                <li><div class="select w194 smp-half foucus_t">
                                    <select name="reserve_time_start" id="reserve_time_start" onchange="return changeStartTime();">
                                        <?php
                                        $reserveTimeDefault = empty($this->request->data['reserve_time_start']) ? '1000' : $this->request->data['reserve_time_start'];
                                        for ($y = 10; $y < 21; $y++) {
                                            $val = (string)$y . '00';
                                            $selected = ($reserveTimeDefault == $val )?'selected':'';
                                            echo '<option value="' . $val . '" ' . $selected . ' >' . (string)$y . ':00' . '</option>';
                                            $val = (string)$y . '30';
                                            $selected = ($reserveTimeDefault == $val )?'selected':'';
                                            echo '<option value="' . $val . '" ' . $selected . ' >' . (string)$y . ':30' . '</option>';
                                        }
                                        $selected = ($reserveTimeDefault == '2100' )?'selected':'';
                                        echo '<option value="2100" ' . $selected . ' >21:00</option>';
                                        ?>
                                    </select>
                                    </div></li>
                                <li><span class="fm-space">～</span></li>
                                <li><div class="select w194 smp-half smp-float smp-ml-none smp-mt foucus_t">
                                    <select name="reserve_time_end" id="reserve_time_end" >
                                        <?php
                                        $reserveTimeDefault = empty($this->request->data['reserve_time_end']) ? '1300' : $this->request->data['reserve_time_end'];
                                        for ($y = 10; $y < 21; $y++) {
                                            $val = (string)$y . '00';
                                            $selected = ($reserveTimeDefault == $val )?'selected':'';
                                            echo '<option value="' . $val . '" ' . $selected . ' >' . (string)$y . ':00' . '</option>';
                                            $val = (string)$y . '30';
                                            $selected = ($reserveTimeDefault == $val )?'selected':'';
                                            echo '<option value="' . $val . '" ' . $selected . ' >' . (string)$y . ':30' . '</option>';
                                        }
                                        $selected = ($reserveTimeDefault == '2100' )?'selected':'';
                                        echo '<option value="2100" ' . $selected . ' >21:00</option>';
                                        ?>
                                    </select>
                                    </div></li>
                                <?= $this->Form->hidden('reserve_time_validate'); ?>
                                <label id="time_purpose_error" class="error-time" for="reserve_time_validate"></label>
                            </ul>
                        </dd>
                    </dl>

                    <!-- 登録の場合にはメール確認欄を表示 -->
                    <dl class="clearfix">
                        <dt class="required cs-wid">連絡可能電話番号</dt>
                        <dd class="clearfix">
                            <input type="text" onchange="inputPhoneNumber();" class="foucus_t text w210" name="phone" id="phone" placeholder="例）000-0000-0000" value="<?=!empty($this->request->data['phone'])?$this->request->data['phone']:''?>">
                        </dd>
                    </dl>
                    <dl class="clearfix"><hr></dl>
                    <dl class="clearfix">
                        <dt class="cs-wid">お子様の情報①</dt>
                    </dl>

                    <dl class="clearfix">
                        <dt class="required cs-wid pl-45">年齢</dt>
                        <dd>
                            <div class="select w160 smp-half foucus_t">
                                <select name="age_year1">
                                    <?php
                                    $selected = (empty($this->request->data['age_year1']) || $this->request->data['age_year1'] == 1)?'selected':'';
                                    echo '<option value="1" ' . $selected . ' >1歳</option>';
                                    for ($y = 2; $y <= 6; $y++) {
                                        $selected = (!empty($this->request->data['age_year1']) && $this->request->data['age_year1'] == $y )?'selected':'';
                                        echo '<option value="' . $y . '" ' . $selected . ' >' . $y . '歳</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="select w160 smp-half smp-float smp-ml-none smp-mt foucus_t">
                                <select name="age_month1">
                                    <?php
                                    $selected = (empty($this->request->data['age_month1']) || $this->request->data['age_month1'] == 1)?'selected':'';
                                    echo '<option value="0" ' . $selected . ' >0ヶ月</option>';
                                    for ($y = 1; $y <= 11; $y++) {
                                        $selected = (!empty($this->request->data['age_month1']) && $this->request->data['age_month1'] == $y )?'selected':'';
                                        echo '<option value="' . $y . '" ' . $selected . ' >' . $y . 'ヶ月</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </dd>
                    </dl>

                    <dl class="clearfix">
                        <dt class="required cs-wid pl-45">性別</dt>
                        <dd>
                            <input type="radio" name="sex1" value="1" id="sex11" <?= (!empty($this->request->data['sex1']) && $this->request->data['sex1'] == '1') ? 'checked="checked"' : '' ?>>
                            <label for="sex11" class=" radio foucus_t" tabindex="23">男の子</label>
                            <input type="radio" name="sex1" value="2" id="sex12" <?= (!empty($this->request->data['sex1']) && $this->request->data['sex1'] == '2') ? 'checked="checked"' : '' ?>>
                            <label for="sex12" class=" radio foucus_t" tabindex="24">女の子</label>
                            <?= $this->Form->hidden('sex1_validate'); ?>
                        </dd>
                    </dl>

                    <dl class="clearfix">
                        <dt class="cs-wid sex_2_cb">

                            <?= $this->Html->shtFrontCheckBox([
                                'name' => 'cb_sex_2',
                                'id' => 'cb_sex_2',
                                'value' => '2',
                                'checked' => '',
                                'label' => 'お子様の情報を追加する②',
                            ]); ?>
                        </dt>
                        <?= $this->Form->hidden('cb_sex_2_validate'); ?>
                    </dl>

                    <div id="sex_2_div" style="display: none">
                    <dl class="clearfix">
                        <dt class="cs-wid">お子様の情報②</dt>
                    </dl>

                    <dl class="clearfix">
                        <dt class="cs-wid pl-45">年齢</dt>
                        <dd>
                            <div class="select w160 smp-half foucus_t">
                                <select name="age_year2">
                                    <?php
                                    $selected = (empty($this->request->data['age_year2']) )?'selected':'';
                                    echo '<option value="" ' . $selected . ' ></option>';
                                    for ($y = 1; $y <= 6; $y++) {
                                        $selected = (!empty($this->request->data['age_year2']) && $this->request->data['age_year2'] == $y )?'selected':'';
                                        echo '<option value="' . $y . '" ' . $selected . ' >' . $y . '歳</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="select w160 smp-half smp-float smp-ml-none smp-mt foucus_t">
                                <select name="age_month2">
                                    <?php
                                    $selected = (empty($this->request->data['age_month2']))?'selected':'';
                                    echo '<option value="" ' . $selected . ' ></option>';
                                    for ($y = 0; $y <= 11; $y++) {
                                        $selected = (!empty($this->request->data['age_month2']) && $this->request->data['age_month2'] == $y )?'selected':'';
                                        echo '<option value="' . $y . '" ' . $selected . ' >' . $y . 'ヶ月</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <?= $this->Form->hidden('age2_validate'); ?>
                        </dd>
                    </dl>

                    <dl class="clearfix">
                        <dt class="cs-wid pl-45">性別</dt>
                        <dd>
                            <input type="radio" name="sex2" value="1" id="sex21" <?= (!empty($this->request->data['sex2']) && $this->request->data['sex2'] == '1') ? 'checked="checked"' : '' ?>>
                            <label for="sex21" class="radio foucus_t" tabindex="23">男の子</label>
                            <input type="radio" name="sex2" value="2" id="sex22" <?= (!empty($this->request->data['sex2']) && $this->request->data['sex2'] == '2') ? 'checked="checked"' : '' ?>>
                            <label for="sex22" class="radio foucus_t" tabindex="24">女の子</label>
                            <?= $this->Form->hidden('sex2_validate'); ?>
                        </dd>
                    </dl>
                    </div>
                    <dl class="clearfix">
                        <dt class="cs-wid">その他留意事項</dt>
                        <dd>
                            <textarea name="remarks" class="foucus_t text w480 custom-wd" placeholder="" value="<?=!empty($this->request->data['remarks'])?h($this->request->data['remarks']):''?>" rows="5"><?=!empty($this->request->data['remarks'])?h($this->request->data['remarks']):''?></textarea>
                        </dd>
                    </dl>
                </div>
                <div class="btn-list clearfix">
                    <div class="btn w160 h60 icon-none back btn-medium">
                        <div class="btn-inner clear">
                            <a class="overlay-text" id="reset-btn" href="<?= $this->Url->build(['action'=>'index']) ?>">
                                <span class="text en">戻る</span>
                            </a>
                            <div class="line"></div>
                            <div class="line2"></div>
                        </div>
                    </div>
                    <div id="submit-btn" class="btn">
                        <div class="btn-inner black">
                            <a role="button" id="submit-btn" onclick="submitForm();">
                                <span class="text">申し込む</span>
                            </a>
                            <div class="line"></div>
                            <div class="line2"></div>
                        </div>
                    </div>
                </div>
                <!--btn_list-->
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<div id="dialog-confirm" style="display:none;">
    <p style="text-align: center; margin-top: 10px; font-weight: bold;">
        予約します。よろしいですか？
    </p>
</div>

<script src="<?= $this->Html->getVersion('/js/jquery-ui.js'); ?>"></script>
<script src="<?= $this->Html->getVersion('/js/jquery.validate.min.js'); ?>"></script>
<script>
    var max_time=21;
    var isPost=false;
    function inputPhoneNumber(){
        var st = $('#phone').val().replace(/[^0-9]/g, "");
        var pl = st.length;
        var res = '';
        if(pl > 0){
            var fp = pl%4;
            var op = Math.floor(pl/4);
            if(fp>0) res = st.substr(0,fp) + '-';
            for(var i=0; i<op; i++){
                res = res + st.substr(fp+i*4,4) + '-';
            }
        }
        $('#phone').val(res.substr(0,res.length-1));
        return false;
    }
    function changeStartTime(){
        var st = $('#reserve_time_start').val();
        var et = parseInt(st.substr(0,2));
        var em = (st.length >= 4) ? st.substr(2,2) : '00';
        if(et + 3 >= max_time){
            et = max_time+'00';
        } else {
            et = (et+3).toString() + em;
        }
        $('#reserve_time_end').val(et);
        return true;
    }
    function submitForm(){
        $('#time_purpose_error').html('');
        $('#time_purpose_error').hide();
        if($('#submit-form').valid()){
            $("#dialog-confirm").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "OK": function () {
                        $(this).dialog("close");
                        $('#submit-form').submit();
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

        } else {
            var st = $(".btn-list").offset().top;
            var l = $("label.error").length;
            for(var i=0; i<l; i++){
                if($("label.error:eq("+i+")").offset().top < st) st = $("label.error:eq("+i+")").offset().top;
            }
            st = st - 100;
            $('html, body').animate({
                scrollTop: st
            }, 600);
        }
        return false;
    }
    $(document).ready(function () {


        $('#cb_sex_2').change(function() {
            // this will contain a reference to the checkbox
            if (this.checked) {
                $('#sex_2_div').show();
            } else {
                $('select[name="age_year2"]').val($('select[name="age_year2"] option:first').val())
                $('select[name="age_month2"]').val($('select[name="age_month2"] option:first').val())
                $('#sex2_validate-error,#age2_validate-error').html('');
                $('#sex21').prop('checked', false);
                $('#sex22').prop('checked', false);
                $('#sex_2_div').hide();

            }
        });

        var requiredMessage = "※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_004')?>";

        $.validator.addMethod("purpose_validate_required", function (value, element) {
            return $('input[name="purpose"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("sex1_required", function (value, element) {
            return $('input[name="sex1"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("sex2_required", function (value, element) {
            return $('input[name="sex2"]:checked').length > 0;
        },requiredMessage);
        $.validator.addMethod("age2_required", function (value, element) {
            return $('select[name="age_year2"]').val().length > 0;
        },requiredMessage);
        $.validator.addMethod("cb_sex_2_required", function (value, element) {
            return $('input[name="cb_sex_2"]:checked').length > 0;

        },requiredMessage);
        $.validator.addMethod("time_start_smaller", function (value, element) {
            var st = $('#reserve_time_start').val();
            var et = $('#reserve_time_end').val();

            return parseInt(st) < parseInt(et);
        },'※<?= str_replace(['{0}','{1}'],['希望時間（開始）', '希望時間（終了）'],Configure::read('MESSAGE_NOTIFICATION.MSG_008')) ?>');
        $.validator.addMethod("reserve_time_perpose", function (value, element) {
            /*
            var pp = 0;
            if($('input[name="purpose"]:checked').length > 0)
                pp = $('input[name="purpose"]:checked').val();
            */
            var st = $('#reserve_time_start').val();
            var et = $('#reserve_time_end').val();
            var sh = parseInt(st.substr(0,2));
            var eh = et.substr(0,2);
            var sm = st.substr(2,2);
            var em = et.substr(2,2);

            var maxNurseyTime = 3;
            /*
            if(pp === '1'){
                maxNurseyTime = 4;
            }
            */
            if( parseInt(eh + em) <= parseInt((sh + (maxNurseyTime)).toString() + sm)){
                return true;
            }else{
                $('#time_purpose_error').html('※一時保育予約は' + maxNurseyTime.toString() + '時間までです。');
                $('#time_purpose_error').css('display','block');
                return false;
            }
        },'');

        $("#submit-form").validate({
            ignore: [],
            rules: {
                purpose_validate: {
                    purpose_validate_required: true
                },
                reserve_date: {
                    required: true,
                },
                reserve_time_validate: {
                    time_start_smaller: true,
                    reserve_time_perpose: true
                },
                phone: {
                    required: true,
                    maxlength: 20
                },

                sex1_validate: {
                    sex1_required: true
                },
                sex2_validate: {
                    sex2_required: function(){return $('input[name="cb_sex_2"]:checked').length > 0},
                },
                age2_validate: {
                    age2_required:   function(){return $('input[name="cb_sex_2"]:checked').length > 0},
                },
                /*
                cb_sex_2_validate: {
                    cb_sex_2_required: function(){
                        return isSecondCBRequired()
                    },
                }
                */
            },
            messages: {
                reserve_date: {
                    required: requiredMessage
                },
                phone: {
                    required: requiredMessage
                },
                mailaddress: {
                    required: requiredMessage
                },
            }
        });

        setTimeout(function() {
            ajaxshorttime();
        }, 500);

        $('#reserve_date').change(function (e) {
            ajaxshorttime();
        })
    });
    function isSecondChildRequired(){
        if($('input[name="sex2"]:checked').length > 0) return true;
        if($('select[name="age_year2"]').val().length > 0) return true;
        if($('select[name="age_month2"]').val().length > 0) return true;
        return false;
    }

    function  isSecondCBRequired (){

        if($('input[name="cb_sex_2"]:checked').length > 0){
            if($('select[name="age_year2"]').val().length = 0) return true;
            if($('input[name="sex2"]:checked').length > 0) return true;
            if($('select[name="age_year2"]').val().length > 0) return true;
            if($('select[name="age_month2"]').val().length > 0) return true;
        }


        return false;
    }
    function ajaxshorttime() {
        var _id=$('#reserve_date :selected').attr('data-id');
        $('#reserve_time_end').empty();
        $('#reserve_time_start').empty();

        if (!isPost){
            $.ajax({
                url: '<?= $this->Url->build(['prefix' => 'mypage', "controller" => "nursery", 'action' => 'ajaxshorttime']);?>',
                type: "GET",
                data: { id: _id},
                cache: false,
                dataType: "json",
                xhrFields: {
                    withCredentials: true
                },
                beforeSend: function() {
                    isPost=true;
                },
                success: function (data) {
                },
            }).done(function (data) {
                $('#reserve_time_end').val("");
                isPost=false
                if (data.LIMIT==true){
                    $('#submit-btn').hide();
                    $('#error_limit p').html(data.MSGLIMIT);
                    $('#error_limit').show();

                    var st = $(".title-01").offset().top;
                    $('html, body').animate({
                        scrollTop: st-50
                    }, 600);
                }
                else {
                    $('#submit-btn').show();
                    $('#error_limit').hide();
                }
                max_time=data.max_time;
                if(data.status==1){
                    $(data.reserve_time_start).appendTo('#reserve_time_start');
                    $(data.reserve_time_end).appendTo('#reserve_time_end');
                }
            });
        }
    }
</script>
