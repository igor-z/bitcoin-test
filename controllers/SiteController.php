<?php

namespace app\controllers;

use app\services\BitcoinService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
	protected $bitcoinService;

	public function __construct($id, $module, BitcoinService $bitcoinService, array $config = [])
	{
		$this->bitcoinService = $bitcoinService;

		parent::__construct($id, $module, $config);
	}

	/**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
	            'rules' => [
		            [
			            'allow' => true,
		            ],
	            ],
            ],
	        'verbs' => [
	        	'class' => VerbFilter::class,
		        'actions' => [
		            'create-address' => ['POST', 'PUT'],
		        ],
	        ]
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
        ];
    }

    public function actionCreateAddress()
    {
	    $this->bitcoinService->createAddress();

		$this->redirect(['index']);
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
		$bitcoin = $this->bitcoinService;

        return $this->render('index', [
	        'addresses' => $bitcoin->getAddresses(),
	        'transactions' => $bitcoin->getTransactions(),
	        'balance' => $bitcoin->getBalance(),
	        'credentials' => $bitcoin->getCredentials(),
        ]);
    }
}
