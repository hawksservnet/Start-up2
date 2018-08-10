<?php
/**
 * Admin layout
 *
 */
use \Cake\Core\Configure;

$pageInfo = Configure::read('page_head_info');
$controller = $this->request->params['controller'];
$action = $this->request->params['action'];
$pageHeadInfo = [
    'title' => 'STARTUP HUB TOKYO',
    'keywords' => '',
    'description' => ''
];
if (!empty($pageInfo[$controller . '|' .$action])) {
    $pageHeadInfo = $pageInfo[$controller . '|' .$action];
}
?>
<!DOCTYPE html>
<html>
	<head>
        <?= $this->Html->charset() ?>
		<title><?=$pageHeadInfo['title']?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, minimal-ui">
		<meta name="description" content="<?=$pageHeadInfo['description']?>" />
		<meta name="keywords" content="<?=$pageHeadInfo['keywords']?>" />
        <?=$this->Html->meta(
            'favicon.ico',
            '/img/favicon.ico',
            ['type' => 'icon']
        );?>
        <?= $this->Html->css('bootstrap') ?>
        <?= $this->Html->css('style') ?>

        <?= $this->Html->script('jquery')?>
        <script>

                callFuel();

            function callFuel() {
                $.ajax({
                    url: '<?=Configure::read('FUEL_ADMIN_URL') . 'admin/ajax/session/keep'?>',
                    type: "GET",
                    dataType: "json",
                    async:false,
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function (data) {
                        if (data.flag==0){
                            location.href='<?=Configure::read('FUEL_ADMIN_URL') . 'admin/logout'?>';
                        }
                    },
                });
            }
        </script>
        <?= $this->Html->script('detectbrowser')?>
        <?= $this->Html->script('common')?>
        <?= $this->Html->script('bootstrap')?>

	</head>
	<body>
    <div class="navbar navbar-inverse">
        <!-- Header -->
        <div class="container" style="padding-left: 10%;">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <?php if(!empty($loginInfo) && ($loginInfo['AUTH'] == 3 || $loginInfo['AUTH'] == 4)): ?>
                    <a class="navbar-brand" role="button" title="一般用管理画面" style="cursor: default;">一般用管理画面</a>
                <?php else: ?>
                    <a class="navbar-brand" href="<?=Configure::read('FUEL_ADMIN_URL') . 'admin/'?>" title="一般用管理画面">一般用管理画面</a>
                <?php endif; ?>

            </div>
            <div class="navbar-collapse collapse">
                <?php
                $isError = $this->fetch('isError');
                ?>
                <?php if(empty($isError)): ?>
                    <ul class="nav navbar-nav" style="width:85%;">
                        <?php if(!empty($loginInfo) && $loginInfo['AUTH'] != 2): ?>
                            <li class="sub <?= ($controller=='Schedule')?'active':'' ?>">
                                <a href="#!" role="button" title="予約管理">予約管理</a>
                                <ul class="sub-nav">
                                    <li><a href="/management/schedule/week" title="スケジュール（週）">スケジュール（週）</a></li>
                                    <li><a href="/management/schedule/month" title="スケジュール（月）">スケジュール（月）</a></li>
                                    <li><a href="/management/schedule/day" title="スケジュール（日）">スケジュール（日）</a></li>
                                </ul>
                            </li>
                            <li class="<?= ($controller=='CounselNote' || $controller=='Reserve')?'active':'' ?>"><a href="/management/counsel_note/note" title="カルテ">カルテ</a></li>
                            <?php if(!empty($loginInfo) && ($loginInfo['AUTH'] == 0 || $loginInfo['AUTH'] == 4)): ?>
                                <li class="<?= ($controller=='Shift')?'active':'' ?>"><a href="/management/shift/list" title="シフト管理">シフト管理</a></li>
                                <li class="<?= ($controller=='Concierge')?'active':'' ?>"><a href="/management/concierge/list" title="コンシェルジュ登録・変更">コンシェルジュ登録・変更</a></li>
                            <?php endif; ?>
                            <?php if(!empty($loginInfo) && $loginInfo['AUTH'] == 0): ?>
                                <li class="<?= ($controller=='Account')?'active':'' ?>"><a href="/management/account/" title="アカウント管理">アカウント管理</a></li>
                            <?php endif; ?>
                            <?php if(!empty($loginInfo) && ($loginInfo['AUTH'] == 0 || $loginInfo['AUTH'] == 1 || $loginInfo['AUTH'] == 5)): ?>
                                <li class="<?= ($controller=='Nursery' && $action=='reserve')?'active':'' ?>"><a href="/management/nursery/reserve" title="託児予約">託児予約</a></li>
                            <?php endif; ?>
                            <?php if(!empty($loginInfo) && ($loginInfo['AUTH'] == 0 || $loginInfo['AUTH'] == 1 || $loginInfo['AUTH'] == 5)): ?>
                                <li class="<?= ($controller=='Nursery' && ( $action=='schedule' ||$action =='setting') )?'active':'' ?>"><a href="/management/nursery/schedule" title="実施日一覧
">実施日一覧
                                    </a></li>
                            <?php endif; ?>
                            <li><a href="/" title="ユーザサイトを開く" target="_blank">ユーザサイトを開く</a></li>
                            <?php if(isset($loginInfo) && is_array($loginInfo)): ?>
                                <li><a href="<?=Configure::read('FUEL_ADMIN_URL') . 'admin/logout'?>" title="ログアウト">ログアウト</a></li>
                            <?php endif ?>
                        <?php endif ?>
                    </ul>
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="container">
			<!-- End Header -->
			<!-- Wrapper -->
			<main class="main">
				<!-- Main content -->
				<section class="main-cont">
                    <?= $this->fetch('content') ?>
				</section>
			</main>
			<!-- End Wrapper -->
		</div>

    <!-- Footer -->
    <footer class="footer">
        <section class="footer-cont">
            <a class="flink" href="<?=Configure::read('FUEL_ADMIN_URL') . 'admin/users'?>" title="管理画面TOPへ">管理画面TOPへ</a>
            <p class="fcopy">Copyright Startup Hub Tokyo</p>
        </section>
    </footer>
    <!-- End Footer -->

	</body>
</html>
