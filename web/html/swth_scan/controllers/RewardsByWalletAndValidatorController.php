<?php

namespace app\controllers;

use Yii;
use app\models\RewardsByWalletAndValidator;
use app\models\RewardsByWalletAndValidatorSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RewardsByWalletAndValidatorController implements the CRUD actions for RewardsByWalletAndValidator model.
 */
class RewardsByWalletAndValidatorController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all RewardsByWalletAndValidator models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RewardsByWalletAndValidatorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RewardsByWalletAndValidator model.
     * @param string $wallet
     * @param string $validator
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($wallet, $validator)
    {
        return $this->render('view', [
            'model' => $this->findModel($wallet, $validator),
        ]);
    }


    /**
     * Finds the RewardsByWalletAndValidator model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $wallet
     * @param string $validator
     * @return RewardsByWalletAndValidator the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($wallet, $validator)
    {
        if (($model = RewardsByWalletAndValidator::findOne(['wallet' => $wallet, 'validator' => $validator])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
