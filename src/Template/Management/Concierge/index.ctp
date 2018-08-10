<?php
echo $this->Html->css('jquery-ui');
?>
<h2 class="title-01">コンシェルジュ一覧</h2>
<!-- User-search -->
<div class="user_search">
    <?=$this->Form->create(null, ['url'=>['action'=>'index'], 'id' => 'search-form'])?>
        <?=$this->Form->hidden('type',['value'=>'1'])?>
        <div class="row">
            <div class="u-col txt-15">氏名</div>
            <div class="u-col txt-80">
                <?=$this->Form->text('search_name',[
                    'label' => false,
                    'value' => !empty($searchName)?h($searchName):'',
                    "class" => "txt-40",
                    'id' => 'search-name',
                ])?>
                <input class="btn pright" type="submit" value="検索"><span class="pright">&nbsp;&nbsp;</span><input class="btn pright" type="button" value="クリア" onclick="clearSearchCondition();">
            </div>
        </div>
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
                氏名
                <?=$this->Paginator->sort('name',
                    '<img src="/img/icon01.png" alt="down" id="name_asc" />',
                    ['escape' => false]
                )?>
                <?=$this->Paginator->sort('name',
                    '<img src="/img/icon02.png" alt="up" id="name_desc" />',
                    ['escape' => false]
                )?>
            </th>
            <th scope="col">キーワード</th>
            <th scope="col">対応外国語</th>
            <th scope="col">編集</th>
            <th scope="col">削除</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($concierges as $concierge) { ?>
            <tr>
                <td scope="row"><?=$concierge->id?></td>
                <td data-title="ログインID"><?= !empty($concierge->name) ? h($concierge->name) : '' ?></td>
                <td data-title="キーワード">
                    <?php foreach ($concierge->concierge_informations as $ci) {
                        if ($ci->info_type == 1) {
                            echo h($ci->info_text) . ' ';
                        }
                    } ?>
                </td>
                <td data-title="対応外国語">
                    <?php foreach ($concierge->concierge_informations as $ci) {
                        if ($ci->info_type == 2) {
                            echo h($ci->info_text) . ' ';
                        }
                    } ?>
                </td>
                <td data-title="編集"><a href="/management/concierge/edit/<?=$concierge->id?>">編集</a></td>
                <td data-title="削除"><a href="javascript:void(0); return true;" onclick="deleteConcierge(<?=$concierge->id?>); return false;">削除</a></td>
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
<?= $this->Form->create(null, ['id' => 'submit-delete-form']) ?>
    <?=$this->Form->hidden('type',['value'=>'2'])?>
    <?=$this->Form->hidden('delete_id',['value'=>'0', 'id' => 'delete-id'])?>
<?= $this->Form->end() ?>
<script>
    var sort_active= '<?= $pagingConfig['sort'].'_'.$pagingConfig['direction']  ?>';
    var direction = '<?= $pagingConfig['direction']  ?>';
    $(document).ready(function () {
        $('#'+sort_active).attr("src","/img/sort_"+direction+"_active.png");
    });
    function deleteConcierge(id){
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
        $('#search-name').val('');
        $('#search-form').submit();
    }
</script>
