<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Summary */

$this->title = $model->wallet;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Summaries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<style>
    .bc-name {
        color: #006a00;
        padding-right: 4px;
        float:right;
        font-weight: 200;
    }
</style>
<div class="summary-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'wallet',
            'wait_unbonding_value',
            'unbonding_value',
            [
                'attribute'=>'rewards_value',
                'format'=>'raw',
                'filter'=>false,
                'value'=>function($data){
                    if(!is_array($data->rewards_value)) return null;
                    $tab='<table>';
                    foreach ($data->rewards_value as $bc=>$val)
                    {
                        if(empty($val)) continue;
                        $tab .= "<tr><td class='bc-name'>$bc:</td><td>$val</td></tr>";
                    }

                    return $tab=='<table>'?null:$tab.'</table>';
                }
            ],
            //'external_in',
            [
                'attribute'=>'external_in',
                'format'=>'raw',
                'filter'=>false,
                'value'=>function($data){
                    if(!is_array($data->external_in)) return null;
                    $tab='<table>';
                    foreach ($data->external_in as $bc=>$val)
                    {
                        if(empty($val)) continue;
                        $tab .= "<tr><td class='bc-name'>$bc:</td><td>$val</td></tr>";
                    }

                    return $tab=='<table>'?null:$tab.'</table>';
                }
            ],
            //'external_out',
            [
                'attribute'=>'external_out',
                'format'=>'raw',
                'filter'=>false,
                'value'=>function($data){
                    if(!is_array($data->external_out)) return null;
                    $tab='<table>';
                    foreach ($data->external_out as $bc=>$val)
                    {
                        if(empty($val)) continue;
                        $tab .= "<tr><td class='bc-name'>$bc:</td><td>$val</td></tr>";
                    }

                    return $tab=='<table>'?null:$tab.'</table>';
                }
            ],
        ],
    ]) ?>

</div>
