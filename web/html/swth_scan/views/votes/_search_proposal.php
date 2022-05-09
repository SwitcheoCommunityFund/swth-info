<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Proposals;

/* @var $this yii\web\View */
/* @var $model app\models\VotesSearch */
/* @var $form yii\widgets\ActiveForm */

$proposals = Proposals::find()->select(['proposal_id','title'])->orderBy('proposal_id')->active()->asArray()->all();
array_walk($proposals, function(&$item){ $item['title'] = $item['proposal_id'] . ' ' . $item['title']; });
$proposals = array_column($proposals,'title','proposal_id');

?>

<div class="votes-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <br>

    <?= $form->field($model, 'proposal_id')->dropDownList($proposals,[
        'prompt' => '',
        'onchange' => 'this.form.submit()'
    ])->label('Proposal',['class'=>'label-class']); ?>

    <br>
    <br>

    <?php ActiveForm::end(); ?>

</div>
