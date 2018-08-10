<?php echo $user_name;?> 様<br/>
<br/>
お世話になっております。スタートアップハブ東京事務局です。<br/>
お申込みいただいておりますコンシェルジュ問診ですが、<br/>
残念ですがキャンセルさせていただくこととなりました。<br/>
<br/>
予約日： <?php echo date("Y-m-d", strtotime($work_date));?><br/>
時間： <?php echo substr($work_time_start,0,2). ':00' . ' ～　' . substr($work_time_end,0,2). ':00';?><br/>
担当者： <?php echo h($pre_concierge_name);?><br/>
<br/>
キャンセル理由：<br/>
<?php echo nl2br(h($comment));?><br/>
<br/>
<br/>
ご了承のほど、よろしくお願いいたします
