<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;
use app\helpers\ViewCommon;

/* @var $this yii\web\View */
/* @var $model app\models\Tokens */
/* @var $form yii\widgets\ActiveForm */



$init_prev = !$model->image?[]:['/img/tokens/'.$model->image];
$filepath = Yii::getAlias('@app').'/web/img/tokens/'.$model->image;
$filesize = file_exists($filepath)?filesize($filepath):0;

$denoms = ViewCommon::getDenoms();
$blockchains = ViewCommon::getBlockchains();

?>

<div class="tokens-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'denom')->dropDownList($denoms, ['prompt' => '']) ?>

    <?= $form->field($model, 'blockchain')->dropDownList($blockchains, ['prompt' => '']) ?>

    <?= $form->field($model, 'decimals')->textInput() ?>

    <?= $form->field($model, 'chain_id')->textInput() ?>

    <?= $form->field($model, 'originator')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'asset_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lock_proxy_hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'image')->widget(FileInput::classname(), [
        'options' => ['multiple' => false, 'accept' => 'image/*'],
        'pluginOptions' => [
            'previewFileType' => 'image',
            'initialPreview'=>$init_prev,
            'initialPreviewAsData'=>true,
            'initialCaption'=>$model->image,
            'initialPreviewConfig' => [
                [
                    'caption' => $model->image,
                    'size' => $filesize,
                    'url'=>$filepath.$model->image
                ],
            ],
            //'required'=>false,
        ]
    ]);

    //$form->field($model, 'image')->fileInput(); ?>

    <?= $form->field($model, 'coin_gecko_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>
