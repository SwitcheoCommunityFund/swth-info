<?php

namespace app\controllers;

use Yii;
use app\models\TokenPairs;
use app\models\TokenPairsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TokenPairsController implements the CRUD actions for TokenPairs model.
 */
class TokenPairsController extends Controller
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
     * Lists all TokenPairs models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TokenPairsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TokenPairs model.
     * @param string $id
     * @param string $system
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $system)
    {
        return $this->render('view', [
            'model' => $this->findModel($id, $system),
        ]);
    }

    /**
     * Creates a new TokenPairs model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TokenPairs();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'system' => $model->system]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TokenPairs model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @param string $system
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $system)
    {
        $model = $this->findModel($id, $system);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'system' => $model->system]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TokenPairs model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @param string $system
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id, $system)
    {
        $this->findModel($id, $system)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TokenPairs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @param string $system
     * @return TokenPairs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $system)
    {
        if (($model = TokenPairs::findOne(['id' => $id, 'system' => $system])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
