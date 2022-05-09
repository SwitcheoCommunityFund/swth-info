<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .about_text {
        background: #f1f9ff;
        border-radius: 0px 30px 30px 30px;
        padding: 20px;
        font-size: 16px;
        /*box-shadow: 0px 0px 3px 1px #92929296;*/
        box-shadow: 0px 3px 6px -4px rgba(0,0,0,0.6);
    }
    .about_text::before {
        content: "";
        border: 19px solid #f1f9ff;
        position: absolute;
        border-bottom: transparent;
        border-left: 11px transparent solid;
        border-right: transparent;
        left: -11px;
        top: 0;
    }
    .community_img {
        width: 50px;
        /*float: right;*/
        border-radius: 50%;
        box-shadow: 0px 3px 6px -4px rgba(0,0,0,0.6);
        margin-bottom: 10px;
    }
</style>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <br>
    <div class="col-md-1 col-sd-1 col-xs-1"><a target="_blank" href="https://t.me/switcheocommunityfund"><img src="/img/community.jpg" class="community_img"></a><br></div>
    <div class="col-md-5 col-sd-7 col-xs-12 about_text" align="justify">

        <p>Dear all,</p>

        <p>This tool/portal is fully funded by the Switcheo Commuity fund. Several categories are available at the moment: <b>“Unstakes”</b> - all the past and future token unstakes with values and dates; <b>“Rewards”</b> - detailed info on rewards with ability to see various grouped data and exact/precise values, download an excel sheet with all the rewards for your wallet and <b>“External transfers”</b> - where you can see all the deposits/withdrawals and much more (the tool is in constant development).</p>

        <p>We believe it is a great helpful tool/portal which we are planning to develop further together in a very professional way.</p>

        <p>If you have any suggestions on future development or see smth wrong in current release please let us know, here are the links to our:  <ul><li>Telegram: <a target="_blank"  href="https://t.me/switcheocommunityfund">https://t.me/switcheocommunityfund</a></li> <li>and Discord: <a target="_blank"  href="https://t.co/s5GhIhlXcJ?amp=1">https://t.co/s5GhIhlXcJ?amp=1</a>.</li></ul></p>

        <p>Thank you for being part of the superb diverse Switcheo community!!!</p>

        <p>P.s.: we couldn’t develop it without donations so we thank all the community members and validators who support the Community fund, thank you very much ❤️❤️❤️. If you wish to take part please donate to <a target="_blank"  href="https://switcheo.org/account/swth1vxdnh987wa7l88qlamk899s85fun7n2zr0ppuk?net=main">swth1vxdnh987wa7l88qlamk899s85fun7n2zr0ppuk</a>.</p>

    </div>
</div>
