<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="flash-message flash-error">
    <button type="button" class="close" onclick="$(this).parent().remove();" aria-hidden="true">×</button>
    <p><?= $message ?></p>
</div>
