<style>
    label.error{
        color:#e84c3d;
        margin-left: 10px;
    }
    .item-lst label{
        margin-left: 5px;
    }
    .invalid-msg{
        font-weight: bold;
        color:#e84c3d;
    }
</style>
<?php
use Cake\Core\Configure;

echo $this->Html->css('jquery-ui');
?>
<h2 class="title-01">コンシェルジュ登録編集</h2>
<div class="show-time">
    <?= $this->Flash->render('flash') ?>
</div>
<p class="txt-01">※必須入力項目</p>
<!-- Form -->
<?= $this->Form->create(null, ['type' => 'file', 'id' => 'submit-update-form']) ?>
<?php $this->Form->setTemplates(['inputContainer' => '{{content}}']); ?>
<?=$this->Form->hidden('id',['value'=>!empty($id)?$id:0])?>
<?=$this->Form->hidden('delete_list',['value'=>'0', 'id' => 'delete-list'])?>
<div class="frm">
    <div class="row">
        <div class="col col-name">氏名（※）</div>
        <div class="col col-input">
            <?=$this->Form->text('name',[
                'label' => false,
                'value' => !empty(!empty($concierge) && $concierge->name)?h($concierge->name):'',
                "class" => "txt-40",
                'maxlength' => 50
            ])?>
            <span>※全角50文字以下</span>
        </div>
    </div>
    <div class="row">
        <div class="col col-name">氏名ローマ字（※）</div>
        <div class="col col-input">
            <?=$this->Form->text('name_e',[
                'label' => false,
                'value' => !empty(!empty($concierge) && $concierge->name_e)?h($concierge->name_e):'',
                "class" => "txt-40",
                'maxlength' => 50
            ])?>
            <span>※半角50文字以下</span>
        </div>
    </div>

    <div class="row">
        <div class="col col-name">経歴（※）</div>
        <div class="col col-input">
            <?=$this->Form->textarea('career',[
                'label' => false,
                'value' => !empty(!empty($concierge) && $concierge->career)?h($concierge->career):'',
                "class" => "txt-60",
                "escape" => false
            ])?>
        </div>
    </div>

    <div class="row">
        <div class="col col-name">写真（※）</div>
        <div class="col col-input up-file">
            <?php
                $thumbnail = '/img/no-img-02.jpg';
                if(!empty($concierge) && !empty($concierge->image_name)) {
                    $thumbnail = '/' . Configure::read('thumbnail_img_path') . $concierge->image_name . '?' . date('ymdHis');
                }
            ?>
            <p><img src="<?=$thumbnail?>" alt="" id="image-display"/></p>
            <div class="up-btn">
                <?=$this->Form->file('image_name', ['id' => 'image-name', 'accept' => "image/*"])?>
                <label for="image-name">ファイルを選択</label>
            </div>
            <?php
                $defaultImageName = !empty(!empty($concierge) && $concierge->image_name) ? h($concierge->image_name) : '';
            ?>
            <p id="image-name-display"><?=$defaultImageName ?></p>
            <p  id="image-msg" style="max-width: 250px; width:50%;">※アップロード可能なファイル<br>種類：JPG,PNG,GIF  サイズ：<?=Configure::read('max_img_upload_size')?>MB以下</p>
        </div>
    </div>
    <div class="row">
        <div class="col col-name">メールアドレス（※）</div>
        <div class="col col-input">
            <?=$this->Form->text('mailaddress',[
                'label' => false,
                'value' => !empty(!empty($concierge) && $concierge->mailaddress)?h($concierge->mailaddress):'',
                "class" => "txt-40",
                'maxlength' => 255
            ])?>
        </div>
    </div>
    <div class="row">
        <div class="col col-name">ソートNO（※）</div>
        <div class="col col-input">
            <?=$this->Form->text('sort_no',[
                'label' => false,
                'value' => !empty(!empty($concierge) && $concierge->sort_no)?h($concierge->sort_no):'',
                "class" => "txt-20",
                'maxlength' => '3',
            ])?>
        </div>
    </div>

    <div class="row">
        <div class="col col-name">キーワード（※）</div>
        <div class="col col-input" id="keyword-parent-element">
            <input class="txt-40" type="text" value="" id="input-keyword" placeholder="" />
            <input class="btn btn-right" type="" readonly value="追加" onclick="addKeyword();"/>
            <span id="keyword-msg">※全角50文字以下</span>
            <?php
                if (!empty($concierge)) {
                    foreach ($concierge->concierge_informations as $ci) {
                        if ($ci->info_type == 1) {
                            echo '<div class="item-lst" id="keyword-element-' . $ci->id . '">';
                            echo '<input class="keyword-required" type="button" value="X" onclick="deleteKeyword(' . $ci->id . ');"/>';
                            echo '<label>' . h($ci->info_text) . '</label>';
                            echo '</div>';
                        }
                    }
                }
            ?>
            <div class="invalid-msg" id="keyword-required" style="display:none;"></div>
        </div>
    </div>

    <div class="row">
        <div class="col col-name">対応外国語</div>
        <div class="col col-input" id="language-parent-element">
            <input class="txt-40" type="text" value="" id="input-language" placeholder=""/>
            <input class="btn btn-right" type="" value="追加" readonly onclick="addLanguage();"/>
            <span id="language-msg">※全角50文字以下</span>
            <?php
            if (!empty($concierge)) {
                foreach ($concierge->concierge_informations as $ci) {
                    if ($ci->info_type == 2) {
                        echo '<div class="item-lst" id="language-element-' . $ci->id . '">';
                        echo '<input type="button" value="X" onclick="deleteLanguage(' . $ci->id . ');"/>';
                        echo '<label>' . h($ci->info_text) . '</label>';
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
    </div>

</div>

<div class="frm">
    <div class="row">
        <div class="col col-name">ログインID（※）</div>
        <div class="col col-input">
            <?=$this->Form->text('login_id',[
                'label' => false,
                'value' => (!empty($concierge) && !empty($concierge->acount) && !empty($concierge->acount->login_id))?h($concierge->acount->login_id):'',
                "class" => "txt-40",
                'maxlength' => 16,
                'id' => 'login-id',
                'style' => 'ime-mode: disabled'
            ])?>
            <span>※半角16文字以下</span>
        </div>
    </div>

    <div class="row">
        <div class="col col-name">パスワード（※）</div>
        <div class="col col-input">
            <?=$this->Form->input('password',[
                'label' => false,
                'type' => 'password',
                'class' => 'txt-40',
                'placeholder' => '********',
                'value' => (!empty($concierge) && !empty($concierge->acount) && !empty($concierge->acount->password))?h($concierge->acount->password):''
            ]);?>
            <span>※半角英数字8文字以上20文字以下</span>
        </div>
    </div>

    <div class="row">
        <div class="col col-name">パスワード確認（※）</div>
        <div class="col col-input">
            <?=$this->Form->input('confirm_password',[
                'label' => false,
                'type'=>'password',
                'class'=>'txt-40',
                'placeholder'=>'********',
                'value' => (!empty($concierge) && !empty($concierge->acount) && !empty($concierge->acount->password))?h($concierge->acount->password):''
            ]);?>
            <span>※パスワードをもう一度入力してください</span>
        </div>
    </div>
</div>
<!-- End Form -->

<div class="btn-block btn-frm">
    <?=$this->Html->link('戻る',['action'=>'index'], ["class" => "btn"])?>
    <input class="btn" type="button" value="<?= !empty($id)?'更新':'登録'?>" onclick="submitUpdateForm(); return false;"/>
</div>
<?= $this->Form->end() ?>
<!-- End Form -->
<?= $this->Html->script('jquery.validate.min')?>
<?= $this->Html->script('jquery-ui')?>
<div id="dialog-confirm" style="display:none;">
    <p style="text-align: center; margin-top: 10px; font-weight: bold;">
        <?= !empty($id)?'更新します。よろしいですか？':'登録します。よろしいですか？' ?>
    </p>
</div>
<script>
    function validateKeywordRequired(){
        $('#keyword-required').html('');
        $('#keyword-required').hide();
        if($("input.keyword-required") && $("input.keyword-required").length > 0) return true;

        $('#keyword-required').show();
        $('#keyword-required').html("※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_004')?>");
        return false;
    }
    function addKeyword()
    {
        $('#keyword-required').html('');
        $('#keyword-required').hide();
        $('#keyword-msg').html("※全角50文字以下");
        $('#keyword-msg').removeClass('invalid-msg');
        var newKeyword = $('#input-keyword').val().trim();
        if(newKeyword.length < 1 || newKeyword.length > 100){
            if(newKeyword.length > 100){
                $('#keyword-msg').html("※全角50文字以下");
            }else{
                $('#keyword-msg').html("※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_004')?>");
            }
            $('#keyword-msg').addClass('invalid-msg');
            return false;
        }

        var d = (new Date()).getTime();
        var newKeywordElement = '<div class="item-lst" id="keyword-element-0-' + d + '">';
        newKeywordElement = newKeywordElement + '<input type="button" value="X" onclick="$(\'#keyword-element-0-'+d+'\').remove(); return false;"/>';
        newKeywordElement = newKeywordElement + '<label>' + newKeyword + '</label>';
        newKeywordElement = newKeywordElement + '<input class="keyword-required" type="hidden" name="add_keyword_value_0_' + d + '" value="' + newKeyword + '"/>';
        newKeywordElement = newKeywordElement + '</div>';
        $('#keyword-parent-element').append(newKeywordElement);
        $('#input-keyword').val('');
    }
    function addLanguage()
    {
        $('#language-msg').html('※全角50文字以下');
        $('#language-msg').removeClass('invalid-msg');
        var newLanguage = $('#input-language').val().trim();
        if(newLanguage.length < 1 || newLanguage.length > 100){
            if(newLanguage.length > 100){
                $('#language-msg').html("※全角50文字以下");
            }else{
                $('#language-msg').html("※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_004')?>");
            }
            $('#language-msg').addClass('invalid-msg');
            return false;
        }

        var d = (new Date()).getTime();
        var newLanguageElement = '<div class="item-lst" id="language-element-0-' + d + '">';
        newLanguageElement = newLanguageElement + '<input type="button" value="X" onclick="$(\'#language-element-0-'+d+'\').remove(); return false;"/>';
        newLanguageElement = newLanguageElement + '<label>' + newLanguage + '</label>';
        newLanguageElement = newLanguageElement + '<input type="hidden" name="add_language_value_0_' + d + '" value="' + newLanguage + '"/>';
        newLanguageElement = newLanguageElement + '</div>';
        $('#language-parent-element').append(newLanguageElement);
        $('#input-language').val('');
    }
    function deleteKeyword(id)
    {
        $('#keyword-element-'+id).remove();
        $('#delete-list').val($('#delete-list').val() + ','+id);
    }
    function deleteLanguage(id)
    {
        $('#language-element-'+id).remove();
        $('#delete-list').val($('#delete-list').val() + ','+id);
    }
    function validUploadImage(){
        var ImageName = $('#image-name-display').text().trim();
        var maxFileSize = <?=Configure::read('max_img_upload_size')?>;
        var defaultMessage = "※アップロード可能なファイル<br>種類：JPG,PNG,GIF  サイズ：" + maxFileSize + "MB以下";
        $('#image-msg').html(defaultMessage);
        $('#image-msg').removeClass('invalid-msg');
        if(ImageName.length < 1 || ImageName.length > 100){
            if(ImageName.length > 256){
                $('#image-msg').html("※半角256文字以下");
            }else{
                $('#image-msg').html("※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_004')?>");
            }
            $('#image-msg').addClass('invalid-msg');
            return false;
        }
        if(ImageName.length > 0 && $('#image-name')[0] && $('#image-name')[0].files[0] &&
            (
                ($('#image-name')[0].files[0].size && $('#image-name')[0].files[0].size > maxFileSize*1024*1024) ||
                !checkFileType($('#image-name')[0].files[0].name,[".jpg", ".gif", ".png"])
            )
        ){
            $('#image-msg').html(defaultMessage);
            $('#image-msg').addClass('invalid-msg');
            return false;
        }
        return true;
    }
    $( document ).ready(function() {
        $('#image-name').change( function(event) {
            var defaultMessage = "※アップロード可能なファイル<br>種類：JPG,PNG,GIF  サイズ：" + <?=Configure::read('max_img_upload_size')?> + "MB以下";
            $('#image-msg').html(defaultMessage);
            $('#image-msg').removeClass('invalid-msg');
            var imageUpload = event.target.files[0];
            if (! imageUpload.type.match('image.*')) {
                $("#image-display").fadeIn("fast").attr('src','<?=$thumbnail?>');
                $('#image-name-display').text('<?= $defaultImageName ?>');
                $('#image-msg').html(defaultMessage);
                $('#image-name').val('');
                $('#image-msg').addClass('invalid-msg');
                return;
            }
            var tmpPath = URL.createObjectURL(imageUpload);
            if(!tmpPath || tmpPath.length < 1) tmpPath = '<?=$thumbnail?>';
            $("#image-display").fadeIn("fast").attr('src',tmpPath);

            var theSplit = $(this).val().split('\\');
            var tmp = theSplit[theSplit.length-1];
            if(!tmp) tmp = "";
            $('#image-name-display').text(tmp);
            validUploadImage();
        });
        jQuery.validator.addMethod("alphanum", function(value, element) {
                return this.optional(element) || /^([a-zA-Z0-9]+)$/.test(value);
            }
        );
        jQuery.validator.addMethod("alphanum_pass", function(value, element) {
                return this.optional(element) || checkPasswordValid(value);
            }
        );
        jQuery.validator.addMethod("double_bytes", function(value, element) {
                return this.optional(element) || checkDoubleBytes(value);
            }
        );
        var requiredMessage = "※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_004')?>";
        $("#submit-update-form").validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 50,
                },
                name_e: {
                    required: true,
                    maxlength: 50
                },
                career: {
                    required: true
                },
                mailaddress: {
                    required: true,
                    email:true,
                    maxlength: 255
                },
                sort_no: {
                    required: true,
                    number: true,
                    maxlength: 3
                },
                login_id: {
                    required: true,
                    maxlength: 16,
                    alphanum: true
                },
                password: {
                    <?=!empty($id)?'':'required: true,'?>
                    minlength: 8,
                    maxlength: 20,
                    alphanum_pass: true
                },
                confirm_password: {
                    <?=!empty($id)?'':'required: true,'?>
                    equalTo: "#password"
                }
            },
            messages: {
                name:{
                    required: requiredMessage,
                    maxlength: '※全角25文字以下',
                },
                name_e:{
                    required: requiredMessage,
                    maxlength: '※半角50文字以下'
                },
                career:{
                    required: requiredMessage
                },
                mailaddress: {
                    required: requiredMessage,
                    maxlength: "※半角255文字以下",
                    email: "※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_005')?>"
                },
                sort_no: {
                    required: requiredMessage,
                    number: "※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_059')?>",
                    maxlength: "※半角3文字以下"
                },
                login_id: {
                    required: requiredMessage,
                    maxlength: "※半角16文字以下",
                    alphanum: "※半角英数字16文字以下"
                },
                password: {
                    <?=!empty($id) ? '' : 'required: "※' . Configure::read('MESSAGE_NOTIFICATION.MSG_004') . '",'?>
                    minlength: "※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_020')?>",
                    maxlength: "※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_020')?>",
                    alphanum_pass: "※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_019')?>"
                },
                confirm_password: {
                    <?=!empty($id) ? '' : 'required: "※' . Configure::read('MESSAGE_NOTIFICATION.MSG_004') . '",'?>
                    equalTo: "※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_016')?>"
                }
            }
        });
    });
    function submitUpdateForm() {
        $('#login_id-duplicated-error').remove();
        $('#image-msg').html('');
        var isKeywordValid = validateKeywordRequired();
        var isImageValid = validUploadImage();
        var isFormValid = $('#submit-update-form').valid();
        if( isImageValid && isFormValid && isKeywordValid) {
            $.when( checkUserExist(<?= (!empty($accountId) && !empty($id))?$accountId:0; ?>, $('#login-id').val()) ).done(function(data){
                if(data.status){
                    $("#dialog-confirm").dialog({
                        resizable: false,
                        height: "auto",
                        width: 400,
                        modal: true,
                        buttons: {
                            "OK": function () {
                                $(this).dialog("close");
                                $('#submit-update-form').submit();
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
                    $('.show-time').append('<div class="flash-message flash-error" id="login_id-duplicated-error"><button type="button" class="close" onclick="$(this).parent().remove();" aria-hidden="true">×</button><p><?=Configure::read('MESSAGE_NOTIFICATION.MSG_045')?></p></div>');
                    $('html, body').animate({
                        scrollTop: $("#login_id-duplicated-error").offset().top
                    }, 500);
                }
            });
        }else{
            $('html, body').animate({
                scrollTop: $("#submit-update-form").offset().top
            }, 500);
        }
    }
    function checkUserExist(id,loginId){
        return $.ajax({
            type: 'post',
            url: '<?php echo $this->Url->build(["controller" => "Concierge", "action" => "ajax"]); ?>',
            data: {'type':'loginIdExist','id':id,'login_id':loginId},
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', <?= json_encode($this->request->param('_csrfToken')); ?>);
            },
            dataType:"json",
        });
    }
    function checkDoubleBytes(str){
        for (var i = 0; i < str.length; i++) {
            if (str.charCodeAt( i ) <= 255) { return false; }
        }
        return true;
    }
    /**
     *
     * @param string sFileName
     * @param list [".jpg", ".jpeg", ".png"]
     * @returns {boolean}
     */
    function checkFileType(sFileName,list){
        for (var j = 0; j < list.length; j++) {
            var sCurExtension = list[j]
            if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                return true
            }
        }
        return false
    }
    /**
     * 半角数字・半角英字（大文字）・半角英字（小文字）のいずれか２種類が含まれているか
     * @param string pass  Password to be checked
     * @returns {boolean}
     */
    function checkPasswordValid(pass){
        var countCase = 0;
        if(/[a-z]/.test(pass)) countCase++;
        if(/[A-Z]/.test(pass)) countCase++;
        if(/[0-9]/.test(pass)) countCase++;

        if(countCase >= 2) return true;
        return false;
    }
</script>
