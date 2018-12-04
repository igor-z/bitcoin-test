<?php

use app\DTOs\CredentialsDTO;
use app\DTOs\TransactionDTO;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $this yii\web\View
 * @var TransactionDTO[] $transactions
 * @var string[] $addresses
 * @var float $balance
 * @var string $currentAddress
 * @var CredentialsDTO $credentials
 */

$this->title = 'Bitcoin client';
?>

<div class="site-index">
	<div class="well form-horizontal">
		<div class="form-group">
			<label class="col-md-2">
				JSON-RPC Credentials:
			</label>
		</div>
		<div class="form-group">
			<label class="col-md-2">
				Server address
			</label>
			<div class="col-md-4">
				<?=$credentials->getHost()?>:<?=$credentials->getPort()?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-4">
				<form action="<?=Url::to(['site/index'])?>">
					<button type="submit" class="btn btn-default">Check balance</button>
				</form>

				<?=Html::beginForm(['site/create-address'])?>
					<button type="submit" class="btn btn-default">Create new address</button>
				<?=Html::endForm()?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-2">
			<ul class="list-group">
				<li class="list-group-item active">Balance:</li>
				<li class="list-group-item">
					<h3 style="margin: 0" class="font-weight-bold i-balance"><?=sprintf("%0.8f", $balance);?><br>BTC</h3>
				</li>
			</ul>
		</div>
		<div class="col-md-4">
			<ul class="list-group">
				<li class="list-group-item active">My Addresses:</li>

				<?php foreach ($addresses as $address):?>
					<li class="list-group-item"><?=$address?></li>
				<?php endforeach;?>
			</ul>
		</div>
		<div class="col-md-6">
			<ul class="list-group">
				<li class="list-group-item active">Transactions:</li>

				<?php foreach ($transactions as $transaction):?>
					<li class="list-group-item">
						txid: <?=$transaction->getId()?><br>
						address: <?=$transaction->getAddress()?><br>
						amount: <?=$transaction->getAmount()?><br>
					</li>
				<?php endforeach;?>
			</ul>
		</div>
	</div>
</div>
