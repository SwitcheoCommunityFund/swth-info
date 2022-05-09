<?php

namespace app\controllers;

use Yii;
use app\models\UnstakingStaked;
use app\models\UnstakingStakedSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UnstakingStakedController implements the CRUD actions for UnstakingStaked model.
 */
class UnstakingStakedController extends Controller
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
     * Lists all UnstakingStaked models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UnstakingStakedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UnstakingStaked model.
     * @param string $date
     * @param string $wallet
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($date, $wallet)
    {
        return $this->render('view', [
            'model' => $this->findModel($date, $wallet),
        ]);
    }

    /**
     * Creates a new UnstakingStaked model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UnstakingStaked();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'date' => $model->date, 'wallet' => $model->wallet]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UnstakingStaked model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $date
     * @param string $wallet
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($date, $wallet)
    {
        $model = $this->findModel($date, $wallet);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'date' => $model->date, 'wallet' => $model->wallet]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UnstakingStaked model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $date
     * @param string $wallet
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($date, $wallet)
    {
        $this->findModel($date, $wallet)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UnstakingStaked model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $date
     * @param string $wallet
     * @return UnstakingStaked the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($date, $wallet)
    {
        if (($model = UnstakingStaked::findOne(['date' => $date, 'wallet' => $wallet])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
