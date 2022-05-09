<?php

namespace app\controllers;

use Yii;
use app\models\RewardsByWallet;
use app\models\RewardsByWalletSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RewardsByWalletController implements the CRUD actions for RewardsByWallet model.
 */
class RewardsByWalletController extends Controller
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
     * Lists all RewardsByWallet models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RewardsByWalletSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RewardsByWallet model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($wallet,$denom)
    {
        return $this->render('view', [
            'model' => $this->findModel(['wallet'=>$wallet,'denom'=>$denom]),
        ]);
    }


    /**
     * Finds the RewardsByWallet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return RewardsByWallet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RewardsByWallet::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
