<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StripeModule
 *
 * @author admin3embed
 */
require_once('lib/Stripe.php');

class StripeModule {

    public function __construct() {

        Stripe::setApiKey("sk_test_6OnagOXTxplZIsWdr4k3oCXT"); //Pranay account: publish-key  pk_test_3Qg1fAkeuC5WG9JBtkHBQZ3Q  
//        Stripe::setApiKey("sk_live_Ao6gu3HbpO00L3Jz7k2JHo0Y");//Pranay account: publish-key  pk_live_RiWBVd9XV7nix5XXwO53y7VH  
//        Stripe::setApiKey("sk_test_ugSQUJAwnHOnn0AukcvkBQyI"); //Chetan account: publish-key  pk_test_3Qg1fAkeuC5WG9JBtkHBQZ3Q  
    }

    private function createCustomer($args) {

        $customer = Stripe_Customer::create(array(
                    'card' => $args['token'],
                    "email" => $args['email']
        ));

        return $customer; //https://stripe.com/docs/api?lang=php#create_customer
    }

    private function createCustomerTest($args) {

        $customer = Stripe_Customer::create(array(
                    'card' => array(
                        'number' => $args['number'],
                        'exp_month' => (int) $args['exp_month'],
                        'exp_year' => (int) $args['exp_year'],
                        'cvc' => (int) $args['cvc']
                    ),
                    "email" => $args['email']
        ));

        return $customer; //https://stripe.com/docs/api?lang=php#create_customer
    }

    private function getCustomer($args) {

        $customer = Stripe_Customer::retrieve($args['stripe_id']);

        return $customer; //https://stripe.com/docs/api?lang=php#retrieve_customer
    }

    private function updateCustomer($args) {

        $customer = Stripe_Customer::retrieve($args['stripe_id']);

        $customer->description = $args['description'];

        $customer->email = $args['email'];

        $customer->save();

        return $customer; //https://stripe.com/docs/api?lang=php#update_customer
    }

    private function updateCustomerDefCard($args) {

        $customer = Stripe_Customer::retrieve($args['stripe_id']);

        $customer->default_card = $args['card_id'];

        $customer->save();

        return $customer; //https://stripe.com/docs/api?lang=php#update_customer
    }

    private function deleteCustomer($args) {

        $customer = Stripe_Customer::retrieve($args['stripe_id']);

        $customer->delete();

        return $customer; //https://stripe.com/docs/api?lang=php#delete_customer
    }

    private function addCard($args) {

        $card = Stripe_Customer::retrieve($args['stripe_id']);

        $card->cards->create(array(
            'card' => $args['token']
        )); //https://stripe.com/docs/api?lang=php#create_card         
        return $card->cards;
    }

    private function getCardData($args) {

        $customer = Stripe_Customer::retrieve($args['stripe_id']);

        $card = $customer->cards->retrieve($args["card_id"]);

        return $card; //https://stripe.com/docs/api?lang=php#retrieve_card
    }

    private function updateCard($args) {

        $cu = Stripe_Customer::retrieve($args['stripe_id']);

        $card = $cu->cards->retrieve($args["card_id"]);

//        $card->name = $args["name"];

        $card->exp_month = $args["exp_month"];

        $card->exp_year = $args["exp_year"];

        $card->save();

        return $card; //https://stripe.com/docs/api?lang=php#update_card
    }

    private function getAllCards($args) {

        $cards = Stripe_Customer::retrieve($args['stripe_id']); //->cards->all();

        return $cards; //https://stripe.com/docs/api?lang=php#retrieve_card
    }

    private function deleteCard($args) {

        $cu = Stripe_Customer::retrieve($args['stripe_id']);

        $card = $cu->cards->retrieve($args["card_id"])->delete();

        return $card; //https://stripe.com/docs/api?lang=php#delete_card
    }

    private function chargeCard($args) {

        $charge = Stripe_Charge::create(array(
                    "customer" => $args['stripe_id'],
                    "amount" => (int) $args['amount'],
                    "currency" => $args['currency'],
                    "description" => $args['description']
        ));

        return $charge; //https://stripe.com/docs/api?lang=php#create_charge
    }

    private function getCharge($args) {

        return Stripe_Charge::retrieve($args['charge_id']); //https://stripe.com/docs/api?lang=php#retrieve_charge
    }

    private function updateCharge($args) {

        $charge = Stripe_Charge::retrieve($args['charge_id']);

        $charge->description = $args['description'];

        $charge->save();

        return $charge; //https://stripe.com/docs/api?lang=php#update_charge
    }

    private function refundCharge($args) {

        $charge = Stripe_Charge::retrieve($args['charge_id']);

        $charge->refund();

        return $charge; //https://stripe.com/docs/api?lang=php#refund_charge
    }

    private function createRecipient($args) {

        return Stripe_Recipient::create(array(
                    "name" => $args["name"],
                    "type" => $args["type"], //"individual"/"Corporation"
                    "tax_id" => $args["tax_id"],
                    "bank_account" => array(
                        "country" => $args["country"],
                        "routing_number" => $args["routing_number"],
                        "account_number" => $args["account_number"]
                    ),
                    "email" => $args["email"],
                    "description" => $args["description"]
        )); //https://stripe.com/docs/api?lang=php#create_recipient
    }

    private function getRecipient($args) {

        return Stripe_Recipient::retrieve($args['stripe_id']); //https://stripe.com/docs/api?lang=php#retrieve_recipient
    }

    private function updateRecipient($args) {

        $rp = Stripe_Recipient::retrieve($args['stripe_id']);

        if ($args['name'] != '')
            $rp->name = $args['name'];

        if ($args['description'] != '')
            $rp->description = $args['description'];

        if ($args['email'] != '')
            $rp->email = $args['email'];

        if ($args['tax_id'] != '')
            $rp->tax_id = $args['tax_id'];

        if ($args['bank_account'] != '' && $args['routing_number'] != '')
            $rp->bank_account = array('country' => 'US', 'routing_number' => $args['routing_number'], 'account_number' => $args['bank_account']);

        $rp->save();

        return $rp; //https://stripe.com/docs/api?lang=php#update_recipient
    }

    private function deleteRecipient($args) {

        $rp = Stripe_Recipient::retrieve($args['stripe_id']);

        $rp->delete();

        return $rp; //https://stripe.com/docs/api?lang=php#delete_recipient
    }

    private function createTransfer($args) {

        $transfer = Stripe_Transfer::create(array(
                    "amount" => (int) $args["amount"],
                    "currency" => $args["currency"],
                    "recipient" => $args["recipient"],
                    "description" => $args["description"],
                    "statement_description" => $args["statement_description"]
        ));

        return $transfer; //https://stripe.com/docs/api?lang=php#create_transfer
    }

    private function retrieveTransfer($args) {

        return Stripe_Transfer::retrieve($args["transfer_id"]); //https://stripe.com/docs/api?lang=php#retrieve_transfer
    }

    private function updateTransfer($args) {

        $tr = Stripe_Transfer::retrieve($args["transfer_id"]);

        $tr->description = $args["description"];

        $tr->save();

        return $tr; //https://stripe.com/docs/api?lang=php#update_transfer
    }

    private function createCoupon($args) {

        return Stripe_Coupon::create($args); //https://stripe.com/docs/api/php#create_coupon
    }

    private function getAllTransfers($args) {

        return Stripe_Transfer::all(array("count" => (int) $args['count'])); //https://stripe.com/docs/api?lang=php#list_transfers
    }

    public function apiStripe($method, $args) {

        try {

            return $this->{$method}($args);
        } catch (Stripe_CardError $e) {
            // Since it's a decline, Stripe_CardError will be caught

            return $e->getJsonBody();
        } catch (Stripe_InvalidRequestError $e) {
            return array("error" => array("message" => "Invalid parameters were supplied to Stripes API"));
        } catch (Stripe_AuthenticationError $e) {
            return array("error" => array("message" => "Authentication with Stripes API failed"));
            // (maybe you changed API keys recently)
        } catch (Stripe_ApiConnectionError $e) {
            return array("error" => array("message" => "Network communication with Stripe failed"));
        } catch (Stripe_Error $e) {
            return array("error" => array("message" => "Error occured!"));
            // yourself an email
        } catch (Exception $e) {
            return array("error" => array("message" => "Something else happened, completely unrelated to Stripe"));
        }
    }

}

?>
