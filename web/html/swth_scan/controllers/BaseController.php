<?php


namespace app\controllers;

use Yii;
use yii\web\Controller;

class BaseController extends Controller
{
    public $timezone;


    public function beforeAction($action)
    {
        $session = Yii::$app->session;
        $this->timezone = $session['timezone_name'];
        $this->timezone = empty($this->timezone)?'UTC':$this->timezone;

        if (!parent::beforeAction($action)) {
            return false;
        }

        return true;
    }

    public function behaviors()
    {
        return [
            'ghost-access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }
}

?>