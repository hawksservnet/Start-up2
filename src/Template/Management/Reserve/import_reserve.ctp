<?php
use Cake\Core\Configure;

echo $this->Html->css('jquery-ui');
?>
<?= $this->Html->script('jquery-ui')?>
<?= $this->Html->script('jquery.validate.min.js')?>

<h2 class="title-01">IMPORT RESERVES</h2>

<?= $this->Form->create(null,['type' => 'file', 'class'=>'','id'=>'namefrm','method'=>'post','url'=>'']) ?>
<div class="user_search">
    <div class="row m-b15">
        <div class="u-col txt-15">CSV file（※）</div>
        <div class="u-col txt-80">
            <?=$this->Form->file('csv_name', ['id' => 'csv-name', 'accept' => ".csv", 'class' => "txt-40"])?>
        </div>
    </div>
    <div class="row m-b15"></div>
    <div class="row m-b15">
        <div class="u-col">
            <input type="button" value="Import CSV" name="" class="btn" id='importcsv'>
        </div>
    </div>
</div>
<?= $this->Form->end() ?>
<div class="flash-message flash-error msg_error" onclick="$(this).hide();" style="display:none;"></div>
<div class="show-time"><?= $this->Flash->render() ?></div>
<?php if(!empty($failData)){ ?>
    <div class="res-tbl fix-left">
        <table class="tbl" cellpadding="0" cellspacing="0" summary="">
            <thead>
            <tr>
                <th scope="col">Row</th>
                <th scope="col">タイムスタンプ</th>
                <th scope="col">日付</th>
                <th scope="col">開始時刻</th>
                <th scope="col">終了時刻</th>
                <th scope="col">担当コンシェルジュ</th>
                <th scope="col">ID</th>
                <th scope="col">Invalid Value</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($failData as $item) { ?>
                <tr>
                    <td data-title="タイムスタンプ"><?= $item['no'] ?></td>
                    <td data-title="タイムスタンプ"><?= $item['record']['csv_time'] ?></td>
                    <td data-title="日付"><?= $item['record']['work_date'] ?></td>
                    <td data-title="開始時刻"><?= $item['record']['work_time_start'] ?></td>
                    <td data-title="終了時刻"><?= $item['record']['work_time_end'] ?></td>
                    <td data-title="担当コンシェルジュ" style="<?= $item['type'] == '2' ? 'color: #a94442;': '' ?>"><?= $item['record']['responsible_concierge'] ?></td>
                    <td data-title="ID" style="<?= $item['type'] == '1' ? 'color: #a94442;': '' ?>"><?= empty($item['record']['user_id']) ? '不明' : $item['record']['user_id'] ?></td>
                    <td data-title="Message" style="color: #a94442;"><?= implode('|', $item['message']) ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>
<script type="text/javascript">
    function validUploadCSV(){
        var defaultMessage = "";
        if($('#csv-name')[0] && $('#csv-name')[0].files[0] ){
            var csvUpload = $('#csv-name')[0].files[0];
            var csvName = csvUpload.name;
            if(csvName.length > 0 && !checkFileType(csvName,[".csv"])
            ){
                defaultMessage = "Invalid File Type.";
                $('.msg_error').html(defaultMessage);
                $('.msg_error').show();
                return false;
            }
        }
        $('.msg_error').html(defaultMessage);
        $('.msg_error').hide();
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
            var sCurExtension = list[j];
            if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                return true;
            }
        }
        return false
    }
    $(document).ready(function() {
        $('#csv-name').change( function(event) {
            validUploadCSV();
        });

        $('#importcsv').click(function(){
            var defaultMessage = "";
            if($('#csv-name')[0] && $('#csv-name')[0].files[0] ) {
                if(validUploadCSV()){
                    $('#namefrm').submit();
                }
            }else{
                defaultMessage = "Please select File.";
                $('.msg_error').html(defaultMessage);
                $('.msg_error').show();
            }
        })
    });
</script>
