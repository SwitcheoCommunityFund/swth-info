<?php


namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class TimezoneController extends Controller
{
    public function actionSet()
    {
        $session = Yii::$app->session;
        if(!Yii::$app->request->isPost) return false;
        $post = Yii::$app->request->post();
        if(@$post['timezone']){
            $session['timezone'] = $post['timezone'];
            try{
                $timezoneName = timezone_name_from_abbr("", ((int)$post['timezone']) * 3600, false);
                $session['timezone_name'] = $timezoneName;

            }catch (\Exception $e){
                $session['timezone_name'] = 'UTC';
            }
        } else return false;
        return true;
    }
}