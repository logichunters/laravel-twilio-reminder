<?php


namespace App\Helpers;


use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwillioHelper
{
    public static function sendSMS($to, $message)
    {
        $sid = config('twillio.account_sid');
        $token = config('twillio.auth_token');
        $phone_number = config('twillio.phone_number');
        try {
            $client = new Client($sid, $token);
            $client->messages->create($to, [
                'from' => $phone_number,
                'body' => $message
            ]);
            return [
                'success' => true,
                'message' => 'Message has been sent successfully!'
            ];
        } catch (ConfigurationException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        } catch (TwilioException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
