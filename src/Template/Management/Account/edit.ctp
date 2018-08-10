<style>
    label.error{
        color:#e84c3d;
        margin-left: 10px;
    }
</style>
<?php
use Cake\Core\Configure;

echo $this->Html->css('jquery-ui');
?>
<h2 class="title-01">アカウント　追加・更新</h2>
<div class="show-time">
    <?= $this->Flash->render('flash') ?>
</div>
<p class="txt-01">※必須入力項目</p>
<!-- Form -->
<?= $this->Form->create(null, ['id' => 'submit-update-form']) ?>
    <?php $this->Form->setTemplates(['inputContainer' => '{{content}}']); ?>
    <?=$this->Form->hidden('id',['value'=>!empty($id)?$id:0])?>
    <div class="frm">
        <div class="row">
            <div class="col col-name">名称※</div>
            <div class="col col-input">
                <?=$this->Form->text('account_name',[
                    'label' => false,
                    'value' => !empty($account_name)?h($account_name):'',
                    "class" => "txt-40",
                    'maxlength' => 50,
                    'style' => 'ime-mode: disabled'
                ])?>
                <span>※全角50文字以下</span>
            </div>
        </div>
        <div class="row">
            <div class="col col-name">ログインID※</div>
            <div class="col col-input">
                <?=$this->Form->text('login_id',[
                    'label' => false,
                    'value' => !empty($login_id)?h($login_id):'',
                    "class" => "txt-40",
                    'maxlength' => 16,
                    'id' => 'login-id',
                    'style' => 'ime-mode: disabled'
                ])?>
                <span>※半角16文字以下</span>
            </div>
        </div>

        <div class="row">
            <div class="col col-name">パスワード※</div>
            <div class="col col-input">
                <?=$this->Form->input('password',[
                    'label' => false,
                    'type' => 'password',
                    'class' => 'txt-40',
                    'placeholder' => '********',
                    'value' => !empty($password)?h($password):''
                ]);?>
                <span>※半角英数字8文字以上20文字以下</span>
            </div>
        </div>

        <div class="row">
            <div class="col col-name">パスワード確認※</div>
            <div class="col col-input">
                <?=$this->Form->input('confirm_password',[
                    'label' => false,
                    'type'=>'password',
                    'class'=>'txt-40',
                    'placeholder'=>'********',
                    'value' => !empty($password)?h($password):''
                ]);?>
                <span>※パスワードをもう一度入力してください</span>
            </div>
        </div>

        <?php
            $authoritySelection = Configure::read('USER_AUTHORITY');
            if($loginInfo && $loginInfo['AUTH'] == 4){
                $authoritySelection = [3 => 'コンシェルジュ'];
            }
        ?>
        <div class="row">
            <div class="col col-name">ユーザ権限※</div>
            <div class="col col-input">
                <?=$this->Form->select('authority',
                    $authoritySelection,
                    [
                        'value' => !empty($authority)?$authority:0,
                        'default' => '0',
                        'class' => "txt-40"
                    ]
                );?>
            </div>
        </div>
    </div>

    <div class="btn-block btn-frm">
        <?=$this->Html->link('戻る',['action'=>'index'], ["class" => "btn"])?>
        <input class="btn" type="button" name="" value="<?= !empty($id)?'更新':'登録'?>" onclick="submitUpdateForm(); return false;"/>
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
    $( document ).ready(function() {
        jQuery.validator.addMethod("alphanum", function(value, element) {
                return this.optional(element) || /^([a-zA-Z0-9]+)$/.test(value);
            }
        );
        jQuery.validator.addMethod("alphanum_pass", function(value, element) {
                return this.optional(element) || checkPasswordValid(value);
            }
        );
        jQuery.validator.addMethod("double_bytes", function(value, element) {
                for (var i = 0; i < value.length; i++) {
                    if (value.charCodeAt( i ) <= 255) { return false; }
                }
                return true;
            }
        );
        $("#submit-update-form").validate({
            rules: {
                account_name: {
                    required: true,
                    maxlength: 50,
                },
                login_id: {
                    required: true,
                    maxlength: 16,
                    alphanum: true,
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
                },
            },
            messages: {
                account_name: {
                    required: "※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_004')?>",
                    maxlength: "※全角25文字以下",
                },
                login_id: {
                    required: "※<?=Configure::read('MESSAGE_NOTIFICATION.MSG_004')?>",
                    maxlength: "※半角16文字以下",
                    alphanum: "※半角英数字16文字以下",
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
                },
            }
        });
    });
    function submitUpdateForm() {
        $('#login_id-duplicated-error').remove();
        if($( '#submit-update-form').valid()) {
            $.when( checkUserExist(<?= !empty($id)?$id:0; ?>, $('#login-id').val()) ).done(function(data){
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
                    $('.show-time').append('<div class="flash-message flash-error"><button type="button" class="close" onclick="$(this).parent().remove();" aria-hidden="true">×</button><p><?=Configure::read('MESSAGE_NOTIFICATION.MSG_045')?></p></div>');
                }
            });
        }
    }
    function checkUserExist(id,loginId){
        return $.ajax({
            type: 'post',
            url: '<?php echo $this->Url->build(["controller" => "Account", "action" => "ajax"]); ?>',
            data: {'type':'loginIdExist','id':id,'login_id':loginId},
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-CSRF-Token', <?= json_encode($this->request->param('_csrfToken')); ?>);
            },
            dataType:"json",
        });
    }
    /**
     * 半角数字・半角英字（大文字）・半角英字（小文字）のいずれか２種類が含まれているか
     * @param string pass Password to be checked
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
