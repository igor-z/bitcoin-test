<?php

namespace app\controllers;

use app\services\BitcoinServiceInterface;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class SiteController extends Controller
{
	protected $bitcoinService;

	public function __construct($id, $module, BitcoinServiceInterface $bitcoinService, array $config = [])
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

		$balanceDetails = $bitcoin->getBalanceDetails();

        return $this->render('index', [
        	'balance' => $balanceDetails->getBalance(),
        	'transactions' => $balanceDetails->getTransactions(),
        	'addresses' => $balanceDetails->getAddresses(),
        	'credentials' => $bitcoin->getCredentials(),
        ]);
    }
}
