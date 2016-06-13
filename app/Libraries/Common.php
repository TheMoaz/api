<?php 

namespace App\Libraries;

use Services_Twilio;
use Services_Twilio_RestException;

class Common
{
    /**
     * Returns a formatted phone number
     * 
     * @param   string  $phone  Phone number to be formatted
     * @return  string          E.164 formatted phone number 
     */
    public static function format_phone($phone)
    {
        return '+' . preg_replace("/[^0-9]/", '', $phone);   // Strip away everything but numbers; append plus sign 
    }

    /**
     * Technical implementation of the core sending functionality
     * Sends message to phone number; uses Twilio
     * Formats the phone number as well. Raised exception is not thrown
     *
     * @param   String  $phone     Phone number
     * @param   String  $message   Message to send
     * @return  Boolean 
     */
    public static function sendSMS($phone, $message)
    {
        $cell = \App\Libraries\Common::format_phone($phone);

        //$config = config('services.twilio');

        $sid    = env('TWILIO_ID');
        $token  = env('TWILIO_SECRET');
        $number = env('TWILIO_NUMBER'); 
        
        $client = new Services_Twilio($sid, $token);

        try
        {
            return $client->account->messages->sendMessage($number, $cell, $message); 
        }
        catch (Services_Twilio_RestException $e)
        {
            return $e->getMessage();
        }
    }
}