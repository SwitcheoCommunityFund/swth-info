<?php

namespace app\controllers;

use Yii;
use app\models\Sends;
use app\models\SendsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SendsController implements the CRUD actions for Sends model.
 */
class SendsController extends Controller
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
     * Lists all Sends models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SendsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Sends model.
     * @param string $tr_hash
     * @param string $denom
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($tr_hash, $denom)
    {
        return $this->render('view', [
            'model' => $this->findModel($tr_hash, $denom),
        ]);
    }

    /**
     * Creates a new Sends model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Sends();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'tr_hash' => $model->tr_hash, 'denom' => $model->denom]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Sends model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $tr_hash
     * @param string $denom
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($tr_hash, $denom)
    {
        $model = $this->findModel($tr_hash, $denom);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'tr_hash' => $model->tr_hash, 'denom' => $model->denom]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Sends model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $tr_hash
     * @param string $denom
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($tr_hash, $denom)
    {
        $this->findModel($tr_hash, $denom)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Sends model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $tr_hash
     * @param string $denom
     * @return Sends the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($tr_hash, $denom)
    {
        if (($model = Sends::findOne(['tr_hash' => $tr_hash, 'denom' => $denom])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
