<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/main_elements.css',
        //'css/bs3_darkness.css',
        //'css/bootstrap.dark.css',
    ];

    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];


    public function init()
    {
        parent::init();
        /*$cookies = Yii::$app->request->cookies;
        if(@$cookies['theme']=='dark')
        {
            $this->css[]='css/bootstrap.dark.css';
            $this->css[]='css/dark_modifier.css';
        }*/
    }
}
