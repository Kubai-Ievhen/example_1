<?php

namespace App\Http\Controllers\Event;

use App\Models\Event\Event;
use App\Resources\StripeHelper;
use App\Resources\SystemParametersSingleton;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class EventPaymentController extends Controller
{
    /**
     * @api {get} /api/event/payment/public_key Get public Stripe token(key)
     * @apiName Get public Stripe token
     * @apiGroup Event Stripe
     * @apiPermission User
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Bool}  key   String with public Stripe API key(token).
     * @apiSuccessExample {string} Success-Response:
     *     HTTP/1.1 200 OK
     *          "pk_test_7pZFhRePelXGwKQEpRU0aLtg"
     */
    public function getPublicToken(){
        return SystemParametersSingleton::getParam('stripe_key_publishable');
    }

    /**
     * @api {get} /api/event/:event_id/payment/account Create Stripe Account for Event money
     * @apiName Create Stripe Account
     * @apiGroup Event Stripe
     * @apiPermission User
     * @apiParam {Number} :event_id Id of Event
     * @apiParam {String} country_sortname Country sortname of payment account
     * @apiParam {String} email Email of payment account
     * @apiParam {String} pay_token Payment token of payment account
     * @apiParamExample {json} Request-Example:
     *     {
     *       "country_sortname":"US",
     *       "email":"test.test.tt",
     *       "pay_token":"tok_1Ccvr4HXROnRwfUlJfKzTLW0",
     *
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Bool}  status   Status of saving (true - ok).
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 OK
     *  {
     *      "status":true,
     * }
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Event not found
     * @apiError (400) array Array validation errors
     * @apiError (400) array.parameter parameter(key) and value of validation error
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *          "supplies":
     *              [
     *                  "The videos field is required."
     *              ]
     *      }
     * @apiError (500) mess Same unspecified error
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 500
     *          Something is wrong
     */
    public function createAccount(Request $request, $event_id)
    {
        $validator = $this->validateCreateAccount($request);
        if($validator->fails()){
            return response(json_encode($validator->errors()), 400);
        }

        if (Event::find($event_id)){
            if (StripeHelper::createAccount($event_id, $request->get('country_sortname'), $request->get('email'),$request->get('pay_token'))) {
                return response(['status'=>true], 201);
            };
            return response('Something is wrong',500);
        }

        return response('Event Not Found', 404);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function validateCreateAccount(Request $request)
    {
        return Validator::make($request->all(), [
            'country_sortname' => 'required|exists:countries,sortname',
            'email' => 'required|email',
            'pay_token' => 'required|string',
        ]);
    }

    /**
     * @api {get} /api/event/:event_id/payment/charge Create payment to event money needs
     * @apiName Create payment
     * @apiGroup Event Stripe
     * @apiPermission User
     * @apiParam {Number} :event_id Id of Event
     * @apiParam {Number} amount Amount to be donated by the user (in dollars)
     * @apiParam {String} pay_token Payment token of payment account
     * @apiParamExample {json} Request-Example:
     *     {
     *       "amount":100,
     *       "pay_token":"tok_1Ccvr4HXROnRwfUlJfKzTLW0",
     *
     *     }
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (201) {Bool}  status   Status of saving (true - ok).
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 OK
     *  {
     *      "status":true,
     * }
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Event not found
     * @apiError (400) array Array validation errors
     * @apiError (400) array.parameter parameter(key) and value of validation error
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 400 Bad Request
     *     {
     *          "supplies":
     *              [
     *                  "The videos field is required."
     *              ]
     *      }
     * @apiError (500) mess Same unspecified error
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 500
     *          Something is wrong
     */
    public function createPayment(Request $request, $event_id)
    {
        $validator = $this->validateCreatePayment($request);
        if($validator->fails()){
            return response(json_encode($validator->errors()), 400);
        }

        if (Event::find($event_id)){
            if (StripeHelper::pushCharge($request->get('amount')*100,$request->get('pay_token'),$event_id)) {
                return response(['status'=>true], 201);
            };
            return response('Something is wrong',500);
        }

        return response('Event Not Found', 404);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function validateCreatePayment(Request $request)
    {
        return Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'pay_token' => 'required|string',
        ]);
    }

    /**
     * @api {get} /api/event/:event_id/payments/all Get All Event Money Transfers
     * @apiName Get All Event Money Transfers
     * @apiGroup Event Stripe
     * @apiPermission User
     * @apiParam {Number} :event_id Id of Event
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Array[]}  array   Data array of Money Transfers.
     * @apiSuccess (200) {Number}  array.id   Id Money Transfers.
     * @apiSuccess (200) {Number}  array.strip_event_many_data_connect_id   Stripe content id of event Money Transfers.
     * @apiSuccess (200) {Number}  array.amount   Total amount of money transfer. (in cents)
     * @apiSuccess (200) {Number}  array.amount_result   Amount of money transfer to the account of the event.(in cents)
     * @apiSuccess (200) {String}  array.stripe_charge_id   Stripe Id of Money Transfers.
     * @apiSuccess (200) {Number}  array.profit   Money arrive at the account of the site.(in cents)
     * @apiSuccess (200) {Number}  array.stripe_profit   Price of services of Stripe.(in cents)
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *  [
     *      {
     *          "id":1,
     *          "strip_event_many_data_connect_id":1,
     *          "amount":1000,
     *          "amount_result":883,
     *          "stripe_charge_id":"dghsfdghfds",
     *          "profit":10,
     *          "stripe_profit":113,
     *      },
     *      .......
     *  ]
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Event not found
     */
    public function getEventMoneyTransfers($event_id){
        if ($event = Event::find($event_id)){
            return $event->stripe()->first()->event_payment()->get();
        }

        return response('Event Not Found', 404);
    }

    /**
     * @api {get} /api/event/:event_id/payments/sum Get Sum Event Money Transfers
     * @apiName Get Sum Event Money Transfers
     * @apiGroup Event Stripe
     * @apiPermission User
     * @apiParam {Number} :event_id Id of Event
     * @apiHeader {String} X-CSRF-TOKEN X-CSRF-TOKEN.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "X-CSRF-TOKEN": "Gnknh68NbfXCay7GZUIouJQtEO67BPgQ9QckOXCD"
     *     }
     * @apiSuccess (200) {Number}  result   Amount of money transfer to the account of the event.(in dollars)
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *  {
     *      "result":8.83
     *  }
     * @apiError (404) event Not found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Event not found
     */
    public function getEventMoneyTransfersSum($event_id){
        if ($event = Event::find($event_id)){
            $sum = 0;
            foreach ($event->stripe()->first()->event_payment()->get() as $payment) {
                $sum += $payment->amount_result;
            };

            return ['result'=>$sum/100];
        }

        return response('Event Not Found', 404);
    }
}