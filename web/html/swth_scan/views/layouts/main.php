<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use webvimark\modules\UserManagement\components\GhostMenu;
use webvimark\modules\UserManagement\UserManagementModule;

AppAsset::register($this);

$session = Yii::$app->session;
if (!$session->isActive){
    $session->open();
}

try {
    $timezone = @$session['timezone'];
    $timezoneName = timezone_name_from_abbr("", ((int)$timezone) * 3600, false);
    date_default_timezone_set($timezoneName);
    Yii::$app->timeZone = $timezoneName;
}catch (\Exception $e){
    Yii::$app->timeZone = 'UTC';
    $this->registerJs('console.log(\'cant set user timezone\');');
}

$timzone_js = <<< JS
    $(document).ready(function() {
        var visitortime = new Date();
        var visitortimezone = -visitortime.getTimezoneOffset()/60;
        if('{$timezone}'!= visitortimezone){
            $.ajax({
                type: "POST",
                url: "/timezone/set",
                data: 'timezone='+ visitortimezone,
                async: false,
                success: function(){
                    location.reload();
                }
            });
        }
    });
JS;

$this->registerJs(
    $timzone_js,
    View::POS_READY
    //['depends' => [\yii\web\JqueryAsset::className()]]
);


$this->registerJsFile('/js/helpers/cookie.helper.js',['position'=>yii\web\View::POS_HEAD]);
$this->registerJsFile('/js/helpers/theme.helper.js',['position'=>yii\web\View::POS_HEAD]);
$this->registerJsFile('/js/gsap/gsap.min.js');

$donateJs = <<<JS

    if(Cookies.get('dnt_jmpr')=='1') return;

    Cookies.set('dnt_jmpr','1',{expires:new Date(new Date() * 1 + 2 * 36e5)});

    var donate = $('.donate_hold').clone();
    donate.removeClass().addClass('donate');
    donate.css({
        'height'        : '40px',
        'width'         : '120px',
        'color'         : 'white',
        'background'    : 'rgb(238, 238, 238)',
        'border-radius' : '20px',
        'text-align'    : 'center',
        'vertical-align': 'middle',
        'bottom'        : '-150px',
        'left'          : '50%',
        'margin-left'   : '-60px',
        'position'      : 'fixed',
        'padding'       : '9px'
    });
    $('body').append(donate);
    
    var tl = new TimelineMax({repeat:0});

    tl.to(".donate",  {transformOrigin: "20% 100%", /*scaleY:0.55,*/ yoyo:true, repeat:1})
      .to(".donate",  {y: -400, ease:Circ.easeOut, yoyo:true, repeat:1,})
      .to(".donate",0.8,{rotationX:-360,yoyo:true,},1.6);
JS;

$this->registerJs($donateJs,yii\web\View::POS_READY);


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style href="/css/main_elements.css"></style>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-PWKE2JGHXD"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-PWKE2JGHXD');
    </script>
</head>
<body>
<?php $this->beginBody() ?>


<div class="wrap" style="overflow: hidden;">
    <?php

    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse',
        ],
    ]);



    $items = [
        [
            'label' => 'Faucet', 'url' => ['/faucet']
        ],
        [
            'label' => 'Trading',
            'items'=>[
                ['label' => 'Summary', 'url' => ['/trading']],
                ['label' => 'Trades', 'url' => ['/trades']],
            ]
        ],
        [
            'label' => 'Summary', 'url' => ['/summary']
        ],
        [
            'label' => 'External Transfers', 'items'=>[
                ['label' => 'All', 'url' => ['/external-transfers']],
                ['label' => 'By wallet', 'url' => ['/external-transfers-by-wallet']],
            ]
        ],

        [
            'label' => 'Stakes', 'url' => ['/stakes']
        ],
        [
            'label' => 'Rewards', 'items'=>[
                ['label' => 'All', 'url' => ['/rewards']],
                ['label' => 'By Wallet', 'url' => ['/rewards-by-wallet']],
                ['label' => 'By Validator', 'url' => ['/rewards-by-validator']],
                ['label' => 'By Wallet and Month', 'url' => ['/rewards-by-wallet-and-month']],
                ['label' => 'By Wallet and Validator', 'url' => ['/rewards-by-wallet-and-validator']],
                //['label' => 'By wallet', 'url' => ['/grouped-bonds']],
            ]
        ],
        [
            'label' => 'Unstakes', 'items'=>[
                ['label' => 'All', 'url' => ['/unstakes']],
                ['label' => 'By wallet', 'url' => ['/grouped-unstakes']],
                ['label' => 'With Delegated', 'url' => ['/unstaking']],
            ]
        ],
        ['label' => 'Unjails', 'url' => ['/unjails']],
        [
            'label' => 'Votes',
            'items' => [
                [
                    'label' => 'Votes',
                    'url' => ['/votes']
                ],
                [
                    'label' => 'Voters Top',
                    'url' => ['/voters-top']
                ]
            ]
        ],
        ['label' => 'Stats', 'url' => ['/home']],
        ['label' => 'Sends', 'url' => ['/sends']],
        ['label' => 'Account Info', 'url' => ['/account-info']],
        ['label' => 'About', 'url' => ['/about']],
        ['label' => '<div class="theme_switch_backgr"><div class="theme_switch"></div></div>'],
        /*Yii::$app->user->isGuest ? (
            ['label' => 'Login', 'url' => ['/site/login']]
        ) : (
            '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>'
        )*/
    ];

    if(Yii::$app->user->identity && @Yii::$app->user->identity->hasRole(['Admin'])){
        $items[]=[
            'label' => 'Backend routes',
            'items'=>[
                ['label'=>'Users','url'=>['/user-management/user/index']],
                ['label'=>'Roles','url'=>['/user-management/role/index']],
                ['label'=>'Permissions','url'=>['/user-management/permission/index']],
                ['label'=>'Permission Groups','url'=>['/user-management/auth-item-group/index']],
                //['label'=>'Visit Log','url'=>['/user-management/user-visit-log/index']],
                ['label'=>'Tokens','url'=>['/tokens/index']],

            ]
        ];
        $items[]=[
            'label' => 'Frontend routes',
            'items'=>[
                ['label'=>'Login', 'url'=>['/user-management/auth/login']],
                ['label'=>'Logout', 'url'=>['/user-management/auth/logout']],
                ['label'=>'Registration', 'url'=>['/user-management/auth/registration']],
                ['label'=>'Change own password', 'url'=>['/user-management/auth/change-own-password']],
                ['label'=>'Password recovery', 'url'=>['/user-management/auth/password-recovery']],
                ['label'=>'E-mail confirmation', 'url'=>['/user-management/auth/confirm-email']],
            ],
        ];
    }


    echo Nav::widget([
        'options' => ['class' => 'navbar-left nav-stacked'],
        'encodeLabels' => false,
        'items' => $items,
    ]);

    NavBar::end();
    ?>

    <div class="container  col-md-10 col-sm-8 main-content">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer main-footer">
    <div class="container">
        <div class="col-md-4 col-xs-4">
            <p class="pull-left">&copy; Switcheo Community fund <?= date('Y') ?></p>
        </div>
        <div class="col-md-4 col-xs-4 donate_hold">
            <p class="text-center"><a href="https://switcheo.org/account/swth1vxdnh987wa7l88qlamk899s85fun7n2zr0ppuk?net=main"><span class="heartBeat">❤️</span>️&nbsp;donate&nbsp;<span class="heartBeat">❤️</span></a></p>
        </div>
        <div class="col-md-4 col-xs-4">
            <p class="pull-right">Powered by <a href="https://www.yiiframework.com/" target="_blank">YII2</a> & <a href="https://degenpower.club" target="_blank">DP&nbsp;team&nbsp;🔥</a></p>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
