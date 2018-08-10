<?php
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'error';

if (Configure::read('debug')):
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error400.ctp');

    $this->start('file');
?>
<?php if (!empty($error->queryString)) : ?>
    <p class="notice">
        <strong>SQL Query: </strong>
        <?= h($error->queryString) ?>
    </p>
<?php endif; ?>
<?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?php Debugger::dump($error->params) ?>
<?php endif; ?>
<?= $this->element('auto_table_warning') ?>
<?php
    if (extension_loaded('xdebug')):
        xdebug_print_function_stack();
    endif;

    $this->end();
endif;
?>
<?php
$arrayUrl = explode('/', $url);
$this->layout = 'concierge';
if(count($arrayUrl) > 1 && 'management' == strtolower($arrayUrl[1])){
    echo $this->Html->css('/assets-sht2/css/style');
    $this->assign('isError', '1');
    $this->layout = 'management';
}
?>
<?php if($this->layout != 'management'): ?>
    <!-- ページタイトル -->
    <h2 id="page-title" class="clearfix">
        <div class="page-title-inner">
            <span class="en">404 Not Found</span>
            <span class="jp"></span>
        </div>
    </h2>
    <section id="concierge" class="section-container">
        <div class="section-inner">
            <div id="not-found-cont" class="section-contents">

                <h3 class="title">リクエストされたページが<br class="sp">見つかりません。</h3>
                <p class="text">リクエストされたページは一時的にアクセスできないか、移動または削除された可能性があります。<br class="pc">URLに間違いがないかご確認をお願いいたします。</p>

                <div class="btn center">
                    <div class="btn-inner clear">
                        <a href="/">
                            <span class="text en">BACK TO TOP</span>
                        </a>
                        <div class="line"></div>
                        <div class="line2"></div>
                    </div>
                </div>

            </div><!-- /.section-contents -->
        </div><!-- /.section-inner -->
    </section>
<?php else: ?>
    <!-- ページタイトル -->
    <h2 id="page-title" class="clearfix" style="margin-top: 0px;">
        <div class="page-title-inner">
            <span class="en">PAGE NOT FOUND</span>
            <span class="jp">404ページ</span>
        </div>
    </h2>
    <section id="concierge" class="section-container">
        <div class="section-inner">
            <div id="not-found-cont" class="section-contents">

                <h3 class="title">リクエストされたページが<br class="sp">見つかりません。</h3>
                <p class="text">リクエストされたページは一時的にアクセスできないか、移動または<br class="pc">削除された可能性があります。<br>URLに間違いがないかご確認をお願いいたします。</p>
                <div class="btn-block">
                    <a class="btn" href="<?=Configure::read('FUEL_ADMIN_URL') . 'admin/users'?>" style="line-height: inherit;">TOPへ戻る</a>
                </div>
            </div><!-- /.section-contents -->
        </div><!-- /.section-inner -->
    </section>
<?php endif; ?>
