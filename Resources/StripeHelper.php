<?php
/**
 * Created by PhpStorm.
 * User: yevhen
 * Date: 14.06.18
 * Time: 15:29
 */

namespace App\Resources;


use App\Models\Event\Event;
use App\Models\Event\StripEventManyDataConnect;
use App\Models\Event\StripEventPayment;
use App\SystemParameters;
use Illuminate\Http\Request;
use Stripe\Account;
use Stripe\Charge;
use Stripe\Stripe;

class StripeHelper
{
    /**
     * @var null
     */
    private static $instance = null;

    /**
     * StripeHelper constructor.
     */
    private function __construct()
    {
        Stripe::setApiKey(SystemParametersSingleton::getParam('stripe_key_secret'));
    }

    /**
     * get Instance
     */
    private static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
    }

    /**
     * @param $event_id
     * @param $country_sortname
     * @param $email
     * @param $external_account
     * @return StripEventManyDataConnect
     */
    public static function createAccount($event_id, $country_sortname, $email, $external_account){
        self::getInstance();

        $res = Account::create([
            'type' => 'standard',
            'country' => $country_sortname,
            'email' => $email
        ]);
        $res->external_accounts->create(array("external_account" => $external_account));

        $stripe_conect = new StripEventManyDataConnect();
        $stripe_conect->stripe_account_id = $res->id;
        $stripe_conect->email = $email;

        $event = Event::where('id',$event_id)->with('demand.money')->first();
        $stripe_conect->event_id = $event->id;

        foreach ($event->demand as $item) {
            if ($item->money->count()){
                $stripe_conect->event_money_id = $item->money->first()->id;
            }
        }

        $stripe_conect->save();

        return $stripe_conect;
    }

    /**
     * @param $amount
     * @param $source
     * @param $event_id
     * @return Charge
     */
    public static function pushCharge($amount, $source, $event_id){
        self::getInstance();

        $application_fee = (int) $amount * SystemParametersSingleton::getParam('profit') /100;
        $stripe = Event::find($event_id)->stripe()->first();

        $charge = Charge::create(array(
            "amount" => $amount,
            "currency" => "usd",
            "source" => $source,
            "application_fee" => $application_fee,
        ), array("stripe_account" => "$stripe->stripe_account_id"));

        self::saveCharge($stripe,$amount,$charge);

        return $charge;
    }

    /**
     * @param $stripe
     * @param $amount
     * @param $charge
     */
    private static function saveCharge($stripe, $amount, $charge){
        $profit = (int) $amount * SystemParametersSingleton::getParam('profit') /100;
        $stripe_profit = (int) $amount * SystemParametersSingleton::getParam('stripe_relative_rate') /100
            + SystemParametersSingleton::getParam('stripe_fixed_rate');

        $event_payment = new StripEventPayment();
        $event_payment->strip_event_many_data_connect_id = $stripe->id;
        $event_payment->amount = $amount;
        $event_payment->amount_result = $amount - $stripe_profit - $profit;
        $event_payment->profit = $profit;
        $event_payment->stripe_profit = $stripe_profit;
        $event_payment->stripe_charge_id = $charge->id;
        $event_payment->save();
    }
}