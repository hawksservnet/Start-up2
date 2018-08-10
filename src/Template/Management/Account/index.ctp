<?php
use Cake\Core\Configure;

echo $this->Html->css('jquery-ui');
?>
<h2 class="title-01">アカウント　一覧・検索</h2>
<!-- User-search -->
<div class="user_search">
    <?=$this->Form->create(null, ['url'=>['action'=>'index'], 'id' => 'search-form'])?>
        <?=$this->Form->hidden('type',['value'=>'1'])?>
        <div class="row m-b15">
            <div class="u-col txt-15">ログインID</div>
            <div class="u-col txt-20">
                <?=$this->Form->text('search_login_id',[
                    'label' => false,
                    'value' => !empty($searchLoginId)?h($searchLoginId):'',
                    "class" => "txt-80",
                    'id' => 'search-login-id',
                ])?>
            </div>
            <div class="u-col txt-20 tcenter">アカウント名称</div>
            <div class="u-col txt-40">
                <?=$this->Form->text('search_acount_name',[
                    'label' => false,
                    'value' => !empty($searchAccountName)?h($searchAccountName):'',
                    "class" => "txt-40",
                    'id' => 'search-acount-name',
                ])?>
            </div>
        </div>
    <?php if($loginInfo && $loginInfo['AUTH'] != 4): ?>
        <div class="row">
            <div class="u-col txt-15">ユーザ権限</div>
            <div class="u-col txt-80">
                <?=$this->Form->select('search_authority',
                    Configure::read('USER_AUTHORITY'),
                    [
                        'empty' => [-1 => 'すべて'],
                        'value' => $searchAuthority,
                        'default' => '',
                        'class' => "txt-20",
                        'id' => 'search-authority',
                    ]
                );?>
                <input class="btn pright" type="submit" value="検索"><span class="pright">&nbsp;&nbsp;</span><input class="btn pright" type="button" value="クリア" onclick="clearSearchCondition();">
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="u-col txt-15"></div>
            <div class="u-col txt-80">
                <input class="btn pright" type="submit" value="検索">
            </div>
        </div>
    <?php endif; ?>
    <?=$this->Form->end()?>
</div>
<!-- End User-search -->
<div class="show-time">
    <?= $this->Flash->render('flash') ?>
</div>
<div class="row">
    <?=$this->Html->link('新規登録',['action'=>'edit'], ["class" => "btn"])?>
    <?= $this->Html->shtCommonPaging($this->Paginator, $pagingConfig); ?>
</div>

<!-- Res Table -->
<div class="res-tbl">
    <table class="tbl" cellpadding="0" cellspacing="0" summary="新規登録">
        <thead>
        <tr>
            <th scope="col">NO<br>
                <?=$this->Paginator->sort('id',
                    '<img src="/img/icon01.png" alt="down" id="id_asc" />',
                    ['escape' => false]
                )?>
                <?=$this->Paginator->sort('id',
                    '<img src="/img/icon02.png" alt="up" id="id_desc" />',
                    ['escape' => false]
                )?>
            </th>
            <th scope="col">
                ログインID<br>
                <?=$this->Paginator->sort('login_id',
                    '<img src="/img/icon01.png" alt="down" id="login_id_asc" />',
                    ['escape' => false]
                )?>
                <?=$this->Paginator->sort('login_id',
                    '<img src="/img/icon02.png" alt="up" id="login_id_desc" />',
                    ['escape' => false]
                )?>
            </th>
            <th scope="col">アカウント名称<br>
                <?=$this->Paginator->sort('account_name',
                    '<img src="/img/icon01.png" alt="down" id="account_name_asc" />',
                    ['escape' => false]
                )?>
                <?=$this->Paginator->sort('account_name',
                    '<img src="/img/icon02.png" alt="up" id="account_name_desc" />',
                    ['escape' => false]
                )?>
            </th>
            <th scope="col">ユーザ権限<br>
                <?=$this->Paginator->sort('authority',
                    '<img src="/img/icon01.png" alt="down" id="authority_asc" />',
                    ['escape' => false]
                )?>
                <?=$this->Paginator->sort('authority',
                    '<img src="/img/icon02.png" alt="up" id="authority_desc"/>',
                    ['escape' => false]
                )?>
            </th>
            <th scope="col">編集</th>
            <th scope="col">削除</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($accounts as $account) { ?>
            <tr>
                <td scope="row"><?=$account->id?></td>
                <td data-title="ログインID"><?= !empty($account->login_id) ? h($account->login_id) : '' ?></td>
                <td data-title="アカウント名称"><?= !empty($account->account_name) ? h($account->account_name) : '' ?></td>
                <?php $userAuthority = Configure::read('USER_AUTHORITY'); ?>
                <td data-title="ユーザ権限"><?= !empty($userAuthority[$account->authority]) ? h($userAuthority[$account->authority]) : '' ?></td>
                <td data-title="編集"><a href="/management/account/edit/<?=$account->id?>">編集</a></td>
                <td data-title="削除"><a href="javascript:void(0); return true;" onclick="deleteAccount(<?=$account->id?>); return false;">削除</a></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<!-- End Res Table -->

<!-- Pager -->
<?= $this->Html->shtCommonPaging($this->Paginator, $pagingConfig); ?>
<!-- End Pager -->
<?= $this->Html->script('jquery.validate.min')?>
<?= $this->Html->script('jquery-ui')?>
<div id="dialog-confirm" style="display:none;">
    <p style="text-align: center; margin-top: 10px; font-weight: bold;">
        削除します。よろしいですか？
    </p>
</div>
<?= $this->Form->create(null, ['class' => 'frm', 'id' => 'submit-delete-form']) ?>
    <?=$this->Form->hidden('type',['value'=>'2'])?>
    <?=$this->Form->hidden('delete_id',['value'=>'0', 'id' => 'delete-id'])?>
<?= $this->Form->end() ?>
<script>
    var sort_active= '<?= $pagingConfig['sort'].'_'.$pagingConfig['direction']  ?>';
    var direction = '<?= $pagingConfig['direction']  ?>';
    $(document).ready(function () {
        $('#'+sort_active).attr("src","/img/sort_"+direction+"_active.png");
    });
    function deleteAccount(id){
        $("#dialog-confirm").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                "OK": function () {
                    $(this).dialog("close");
                    $('#delete-id').val(id);
                    $('#submit-delete-form').submit();
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
    }
    function clearSearchCondition(){
        $('#search-login-id').val('');
        $('#search-acount-name').val('');
        $('#search-authority').val(-1);
        $('#search-form').submit();
    }
</script>
