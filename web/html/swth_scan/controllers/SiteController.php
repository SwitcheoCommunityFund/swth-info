<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\db\Expression;

use app\models\LoginForm;
use app\models\ContactForm;

use app\models\Rewards;
use app\models\Bonds;
use app\models\ExternalTransfers;
use app\models\Delegates;
use app\models\Tokens;
use app\models\Trades;
use app\models\AirdropLog;

use app\helpers\FaucetHelper;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout','faucet-console','get-faucet-console'],
                'rules' => [
                    [
                        'actions' => ['logout','faucet-console','get-faucet-console'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    function actionTestRunNode()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $command = 'sh /var/www/html/swth_js/run_if_not_run.sh test.js /dev/null';
        exec($command,$out);
        exec('ps auxww | grep test.js',$out2);
        return [$out,$out2];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;


        return $this->render('index',[
            'unbonded_this_month'          => @Bonds::find()->select('sum(value) as value')->where("date_trunc('month',timezone('{$timezone}',date) + interval '30 days') = date_trunc('month',timezone('{$timezone}',current_timestamp))")->andWhere("date + interval '30 days' <= current_timestamp ")->groupBy(["date_trunc('month',timezone('{$timezone}',date))"])->one()->value/pow(10,8),
            'unbonded_this_week'           => @Bonds::find()->select('sum(value) as value')->where("date_trunc('week',timezone('{$timezone}',date) + interval '30 days') = date_trunc('week',timezone('{$timezone}',current_timestamp))")->andWhere("date + interval '30 days' <= current_timestamp ")->groupBy(["date_trunc('week',timezone('{$timezone}',date))"])->one()->value/pow(10,8),
            'unbonding_in_future'          => @Bonds::find()->select('sum(value) as value')->where("date + interval '30 days' > current_timestamp ")->one()->value/pow(10,8),
            'unbonding_total'              => @Bonds::find()->select('sum(value) as value')->one()->value/pow(10,8),

            //'rewards_generated_this_week'  => @Rewards::find()->select(['sum(value) as value','denom'])->where("date_trunc('week',timezone('{$timezone}',date)) = date_trunc('week',timezone('{$timezone}',current_timestamp))")->andWhere('denom is not null')->groupBy(["date_trunc('week',timezone('{$timezone}',date))",'denom'])->asArray()->all(),
            //'rewards_generated_this_month' => @Rewards::find()->select(['sum(value) as value','denom'])->where("date_trunc('month',timezone('{$timezone}',date)) = date_trunc('month',timezone('{$timezone}',current_timestamp))")->andWhere('denom is not null')->groupBy(["date_trunc('month',timezone('{$timezone}',date))",'denom'])->asArray()->all(),

            //'withdrawals_this_month'       => @ExternalTransfers::find()->select(['sum(amount) as value', 'denom'])->where("date_trunc('month',timezone('{$timezone}',timestamp)) = date_trunc('month',timezone('{$timezone}',current_timestamp))")->andWhere(['status'=>'success','transfer_type'=>'withdrawal'])->groupBy(["date_trunc('month',timezone('{$timezone}',current_timestamp))","denom"])->asArray()->all(),
            //'withdrawals_this_week'        => @ExternalTransfers::find()->select(['sum(amount) as value', 'denom'])->where("date_trunc('week',timezone('{$timezone}',timestamp)) = date_trunc('week',timezone('{$timezone}',current_timestamp))")->andWhere(['status'=>'success','transfer_type'=>'withdrawal'])->groupBy(["date_trunc('week',timezone('{$timezone}',current_timestamp))","denom"])->asArray()->all(),

            //'deposits_this_month'          => @ExternalTransfers::find()->select(['sum(amount) as value', 'denom'])->where("date_trunc('month',timezone('{$timezone}',timestamp)) = date_trunc('month',timezone('{$timezone}',current_timestamp))")->andWhere(['status'=>'success','transfer_type'=>'deposit'])->groupBy(["date_trunc('month',timezone('{$timezone}',current_timestamp))","denom"])->asArray()->all(),
            //'deposits_this_week'           => @ExternalTransfers::find()->select(['sum(amount) as value', 'denom'])->where("date_trunc('week',timezone('{$timezone}',timestamp)) = date_trunc('week',timezone('{$timezone}',current_timestamp))")->andWhere(['status'=>'success','transfer_type'=>'deposit'])->groupBy(["date_trunc('week',timezone('{$timezone}',current_timestamp))","denom"])->asArray()->all(),

            'delegates_this_week'          => @Delegates::find()->select('sum(value) as value')->where("date_trunc('week',timezone('{$timezone}',date)) = date_trunc('week',timezone('{$timezone}',current_timestamp))")->groupBy(["date_trunc('week',timezone('{$timezone}',date))"])->one()->value/pow(10,8),
            'delegates_this_month'         => @Delegates::find()->select('sum(value) as value')->where("date_trunc('month',timezone('{$timezone}',date)) = date_trunc('month',timezone('{$timezone}',current_timestamp))")->groupBy(["date_trunc('month',timezone('{$timezone}',date))"])->one()->value/pow(10,8),
            'delegates_total'              => @Delegates::find()->select('sum(value) as value')->one()->value/pow(10,8),

            'tokens'                       => @Tokens::find()->asArray()->all(),

            'ethereum_created_wallets'     => @AirdropLog::find()->count(),

            'timezone'=>$timezone,
            //'q'=>@Bonds::find()->select('sum(value) as value')->where("date_trunc('month',timezone('{$timezone}',date) + interval '30 days') = date_trunc('month',timezone('{$timezone}',current_timestamp))")->andWhere("date + interval '30 days' <= current_timestamp")->groupBy(["date_trunc('month',date)"])->createCommand()->sql,
        ]);
    }

    public function actionTradingSummary()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $monthTotalExpr = new Expression("SUM(price*quantity) filter (where date_trunc('month',timezone('{$timezone}',block_created_at)) = date_trunc('month',timezone('{$timezone}',current_timestamp)))");
        $H24TotalExpr = new Expression("SUM(price*quantity) filter (where block_created_at >= current_timestamp - interval '24 hours')");
        $rowCount = new Expression("count(*) over (order by 1)");

        $tradesSummary = Trades::find()
            ->select([
                'denom'=>'m.quote',
                'trade_sum'=>'SUM(price*quantity)',
                'trade_month_sum'=>$monthTotalExpr,
                'trade_24h_sum'=>$H24TotalExpr,
                'count'=>$rowCount
            ])
            ->alias('tr')
            ->leftJoin(['m' => 'markets'],'m.name=tr.market')
            ->leftJoin(['t' => 'tokens'],'m.quote=cast(t.denom as text)')

            ->groupBy(["m.quote"])
            ->orderBy('SUM(price*quantity) desc')
            ->asArray()
            ->all();

        $monthTotalExprUsd = new Expression("SUM(price*quantity*COALESCE(th.current_price,tn.current_price)) filter (where date_trunc('month',timezone('{$timezone}',block_created_at)) = date_trunc('month',timezone('{$timezone}',current_timestamp)))");
        $H24TotalExprUsd = new Expression("SUM(price*quantity*COALESCE(th.current_price,tn.current_price)) filter (where block_created_at >= current_timestamp - interval '24 hours')");

        $tradesSummaryUSD = Trades::find()
            ->select([
                'trade_sum_usd'=>'SUM(price*quantity*COALESCE(th.current_price,tn.current_price))',
                'trade_month_sum_usd'=>$monthTotalExprUsd,
                'trade_24h_sum_usd'=>$H24TotalExprUsd,
                'count'=>$rowCount
            ])
            ->alias('tr')
            ->leftJoin(['m' => 'markets'],'m.name=tr.market')
            ->leftJoin(['t' => 'tokens'],'m.quote=cast(t.denom as text)')
            ->leftJoin(['th' => 'token_history'],"th.currency = 'usd' AND t.coin_gecko_id is not null AND th.id=t.coin_gecko_id and th.date = date(block_created_at)")
            ->leftJoin(['tn' => 'token_price_now'],"tn.currency = 'usd' AND t.coin_gecko_id is not null AND tn.id=t.coin_gecko_id and date(block_created_at)+1 >= current_date")

            //->groupBy(["m.quote"])
            ->orderBy('SUM(price*quantity) desc')
            ->asArray()
            ->one();


        return [
            'trades_summary' => $tradesSummary,
            'trades_summary_usd' => $tradesSummaryUSD
        ];
    }


    public function actionTotals()
    {

        $this->layout = 'test';

        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        return $this->render('test-area',[
            'unbonded_this_month'          => @Bonds::find()->select('sum(value) as value')->where("date_trunc('month',timezone('{$timezone}',date) + interval '30 days') = date_trunc('month',timezone('{$timezone}',current_timestamp))")->andWhere("date + interval '30 days' <= current_timestamp ")->groupBy(["date_trunc('month',timezone('{$timezone}',date))"])->one()->value/pow(10,8),
            'unbonded_this_week'           => @Bonds::find()->select('sum(value) as value')->where("date_trunc('week',timezone('{$timezone}',date) + interval '30 days') = date_trunc('week',timezone('{$timezone}',current_timestamp))")->andWhere("date + interval '30 days' <= current_timestamp ")->groupBy(["date_trunc('week',timezone('{$timezone}',date))"])->one()->value/pow(10,8),
            'unbonding_in_future'          => @Bonds::find()->select('sum(value) as value')->where("date + interval '30 days' > current_timestamp ")->one()->value/pow(10,8),
            'unbonding_total'              => @Bonds::find()->select('sum(value) as value')->one()->value/pow(10,8),

            //'rewards_generated_this_week'  => @Rewards::find()->select(['sum(value) as value','denom'])->where("date_trunc('week',timezone('{$timezone}',date)) = date_trunc('week',timezone('{$timezone}',current_timestamp))")->andWhere('denom is not null')->groupBy(["date_trunc('week',timezone('{$timezone}',date))",'denom'])->asArray()->all(),
            //'rewards_generated_this_month' => @Rewards::find()->select(['sum(value) as value','denom'])->where("date_trunc('month',timezone('{$timezone}',date)) = date_trunc('month',timezone('{$timezone}',current_timestamp))")->andWhere('denom is not null')->groupBy(["date_trunc('month',timezone('{$timezone}',date))",'denom'])->asArray()->all(),

            //'withdrawals_this_month'       => @ExternalTransfers::find()->select(['sum(amount) as value', 'denom'])->where("date_trunc('month',timezone('{$timezone}',timestamp)) = date_trunc('month',timezone('{$timezone}',current_timestamp))")->andWhere(['status'=>'success','transfer_type'=>'withdrawal'])->groupBy(["date_trunc('month',timezone('{$timezone}',current_timestamp))","denom"])->asArray()->all(),
            //'withdrawals_this_week'        => @ExternalTransfers::find()->select(['sum(amount) as value', 'denom'])->where("date_trunc('week',timezone('{$timezone}',timestamp)) = date_trunc('week',timezone('{$timezone}',current_timestamp))")->andWhere(['status'=>'success','transfer_type'=>'withdrawal'])->groupBy(["date_trunc('week',timezone('{$timezone}',current_timestamp))","denom"])->asArray()->all(),

            //'deposits_this_month'          => @ExternalTransfers::find()->select(['sum(amount) as value', 'denom'])->where("date_trunc('month',timezone('{$timezone}',timestamp)) = date_trunc('month',timezone('{$timezone}',current_timestamp))")->andWhere(['status'=>'success','transfer_type'=>'deposit'])->groupBy(["date_trunc('month',timezone('{$timezone}',current_timestamp))","denom"])->asArray()->all(),
            //'deposits_this_week'           => @ExternalTransfers::find()->select(['sum(amount) as value', 'denom'])->where("date_trunc('week',timezone('{$timezone}',timestamp)) = date_trunc('week',timezone('{$timezone}',current_timestamp))")->andWhere(['status'=>'success','transfer_type'=>'deposit'])->groupBy(["date_trunc('week',timezone('{$timezone}',current_timestamp))","denom"])->asArray()->all(),

            'delegates_this_week'          => @Delegates::find()->select('sum(value) as value')->where("date_trunc('week',timezone('{$timezone}',date)) = date_trunc('week',timezone('{$timezone}',current_timestamp))")->groupBy(["date_trunc('week',timezone('{$timezone}',date))"])->one()->value/pow(10,8),
            'delegates_this_month'         => @Delegates::find()->select('sum(value) as value')->where("date_trunc('month',timezone('{$timezone}',date)) = date_trunc('month',timezone('{$timezone}',current_timestamp))")->groupBy(["date_trunc('month',timezone('{$timezone}',date))"])->one()->value/pow(10,8),
            'delegates_total'              => @Delegates::find()->select('sum(value) as value')->one()->value/pow(10,8),

            'tokens'                       => @Tokens::find()->asArray()->all(),

            'ethereum_created_wallets'     => @AirdropLog::find()->count(),

            'timezone'=>$timezone,
            //'q'=>@Bonds::find()->select('sum(value) as value')->where("date_trunc('month',timezone('{$timezone}',date) + interval '30 days') = date_trunc('month',timezone('{$timezone}',current_timestamp))")->andWhere("date + interval '30 days' <= current_timestamp")->groupBy(["date_trunc('month',date)"])->createCommand()->sql,
            'q'=>Trades::find()
                ->select([
                    'trade_sum_usd'=>'SUM(price*quantity*COALESCE(th.current_price,tn.current_price))',
                    'trade_month_sum_usd'=>(new Expression("SUM(price*quantity*COALESCE(th.current_price,tn.current_price)) filter (where date_trunc('month',timezone('{$timezone}',block_created_at)) = date_trunc('month',timezone('{$timezone}',current_timestamp)))")),
                    'trade_24h_sum_usd'=>(new Expression("SUM(price*quantity*COALESCE(th.current_price,tn.current_price)) filter (where block_created_at >= current_timestamp - interval '24 hours')")),
                    'count'=>(new Expression("count(*) over (order by 1)"))
                ])
                ->alias('tr')
                ->leftJoin(['m' => 'markets'],'m.name=tr.market')
                ->leftJoin(['t' => 'tokens'],'m.quote=cast(t.denom as text)')
                ->leftJoin(['th' => 'token_history'],"th.currency = 'usd' AND t.coin_gecko_id is not null AND th.id=t.coin_gecko_id and th.date = date(block_created_at)")
                ->leftJoin(['tn' => 'token_price_now'],"tn.currency = 'usd' AND t.coin_gecko_id is not null AND tn.id=t.coin_gecko_id and date(block_created_at)+1 >= current_date")

                //->groupBy(["m.quote"])
                ->orderBy('SUM(price*quantity) desc')
                ->createCommand()->sql,
        ]);


    }

    public function actionWithdrawalsDeposits()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;


        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;


        $month = new Expression("sum(amount) filter (where date_trunc('month',timezone('{$timezone}',timestamp)) = date_trunc('month',timezone('{$timezone}',current_timestamp)))");
        $week = new Expression("sum(amount) filter (where date_trunc('week',timezone('{$timezone}',timestamp)) = date_trunc('week',timezone('{$timezone}',current_timestamp)))");

        return [
            'withdrawals' => @ExternalTransfers::find()->select(['month'=>$month, 'week'=>$week, 'denom'])->where(['status'=>'success','transfer_type'=>'withdrawal'])
                ->groupBy(["denom"])
                ->having(['IS NOT', $month, null])->orHaving(['IS NOT', $week, null])
                ->orderBy('denom')
                ->asArray()->all(),
            'deposits'    => @ExternalTransfers::find()->select(['month'=>$month, 'week'=>$week, 'denom'])->where(['status'=>'success','transfer_type'=>'deposit'])
                ->groupBy(["denom"])
                ->having(['IS NOT', $month, null])->orHaving(['IS NOT', $week, null])
                ->orderBy('denom')
                ->asArray()->all(),

            'timezone'=>$timezone,
        ];


    }


    /**
     * Login action.
     *
     * @return Response|string
     */
    /*public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }*/

    /**
     * Logout action.
     *
     * @return Response
     */
    /*public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }*/

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    /*public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }*/

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionFaucetConsole()
    {
        return $this->render('faucet-console');
    }

    public function actionGetFaucetConsole()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //Yii::$app->request->isPost;
        $post = \Yii::$app->request->post();
        $wallet = $post['wallet'];
        try{$f = new FaucetHelper($wallet,1);}catch (\Exception $e){
            return ['message'=>$e->getMessage(),'trace'=>$e->getTraceAsString()];
        }
        return [
            'status'=>'success',
            'data'=>[
                'wallet_name'=>$f->checkWalletName(),
                //'cookie'=>$f->checkCookie(),
                'balance'=>$f->checkBalance(),
                'last_send'=>$f->checkLastTransfer(),
                //'locker_test' =>$f->checkLockerTest(),
                'locker' =>$f->checkLocker(),
                //'send' => $f->sendTokens(),
                //'send' => $f->sendTokensJs(),
                'success'=>$f->successSendTokens(),
                'node'=>$f->nodeState(),
            ],
            'fails'=>$f->failStack
        ];
    }

    public function actionFaucet()
    {
        return $this->render('faucet');
    }

    public function actionGetFaucet()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //Yii::$app->request->isPost;
        $post = \Yii::$app->request->post();
        $wallet = $post['wallet'];
        $amount = @$post['amount'];
        try{$f = new FaucetHelper($wallet,$amount);}catch (\Exception $e){
            return ['status'=>'fail','fails'=>['Internal server error']];
        }


        if($f->checkWalletName())
        {
            if($f->checkCookie())
            {
                if($f->nodeState())
                {
                    if($f->checkBalance())
                    {
                        if($f->checkLocker())
                        {
                            if($f->checkLastTransfer())
                            {
                                if($f->sendTokensJs())
                                {
                                    $f->successSendTokens();
                                    return ['status'=>'success'];
                                }
                            }
                        }
                    }
                }
            }
        }


        return [
            'status'=>'fail',
            'fails'=>$f->failStack
        ];
    }

    public function actionTesteg()
    {
        $cookies = Yii::$app->request->cookies;
        //$cookies = $_COOKIE;
        return $this->render('testeg',[
            'cookies'=>$cookies,
            'chart_controller'=>'transactions-count',
        ]);
    }

    public function actionChangeTheme($theme)
    {
        $cookies = Yii::$app->request->cookies;
        $themes = ['light','dark'];
        if(in_array($theme,$themes)){
            $cookies->setValue('theme',$theme);
        }
        //$this->refresh()->send();
    }

}
