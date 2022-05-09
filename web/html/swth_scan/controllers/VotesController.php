<?php

namespace app\controllers;

use Yii;
use app\models\Votes;
use app\models\VotesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;

/**
 * VotesController implements the CRUD actions for Votes model.
 */
class VotesController extends Controller
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
     * Lists all Votes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VotesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionCharts()
    {
        /*$session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;*/

        $proposal_id = Yii::$app->request->post('proposal_id');
        $proposal_id = $proposal_id?$proposal_id:0;

        $votes = Votes::find()->where(['proposal_id'=>$proposal_id])->select(['count(*) as count','option'])->groupBy('option')->asArray()->all();

        return $this->renderAjax('chart', [
            'votes' => $votes,
        ]);
    }

    /**
     * Displays a single Votes model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    /**
     * Finds the Votes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Votes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Votes::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
