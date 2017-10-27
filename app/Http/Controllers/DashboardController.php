<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Coinbase\Wallet\Client;
use Coinbase\Wallet\Configuration;
use Coinbase\Wallet\Resource\Address;
use Coinbase\Wallet\Resource\Account;
use Coinbase\Wallet\Enum\Param;
use Coinbase\Wallet\Exception\TwoFactorRequiredException;
use Coinbase\Wallet\Resource\Transaction;

class DashboardController extends Controller
{
    //
    public function index() {
      $apiKey = env('COINBASE_API_KEY');
      $apiSecret = env('COINBASE_API_SECRET');
      $configuration = Configuration::apiKey($apiKey, $apiSecret);
      $client = Client::create($configuration);

      $currencies = $client->getCurrencies();
      $rates = $client->getExchangeRates();
      $buyPrice = $client->getBuyPrice('BTC-USD');
      $sellPrice = $client->getSellPrice('BTC-USD');
      $spotPrice = $client->getSpotPrice('BTC-USD');
      $time = $client->getTime();
      dd($currencies, $rates, $buyPrice, $sellPrice, $spotPrice);
      try {
          //$client->createAccountTransaction($account, $transaction);
          $account = new Account();
          $account->setName("NewWallet");
          $client->createAccount($account);
          $accounts = $client->getAccounts();
          dd($accounts);
      } catch (TwoFactorRequiredException $e) {
          // show 2FA dialog to user and collect 2FA token

          // retry call with token
dd("No Good");
          // $client->createAccountTransaction($account, $transaction, [
          //     Param::TWO_FACTOR_TOKEN => '123456',
          // ]);
      }
      // $account = $client->getPrimaryAccount();
      // $address = new Address(['name'=>'PMS Address001']);
      // $client->createAccountAddress($account, $address);
// $orders = $client->getOrders();
// $order = $client->getOrder($orderId);
// dd($orders, $order);


      dd($accounts);
    }
}
