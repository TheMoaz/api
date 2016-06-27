<?php 

namespace App\Libraries;

use Image;
use Services_Twilio;
use Services_Twilio_RestException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class Common
{
    /**
     * Returns a sanitized phone number
     * 
     * @param   string  $phone  Phone number to be formatted
     * @return  string          E.164 formatted phone number 
     */
    public static function format_phone($phone)
    {
        $phone = preg_replace("/[^0-9]/", '', $phone); 
        $phone = preg_replace("/^0+(?!$)/", '', $phone);
        
        return '+' . $phone; 
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
        // return true; 
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
    
    /**
     * Used to send email to specific user
     */
    public static function sendMail($user, $subject, $text, $template)
    {
        return Mail::send($template, ['name' => $user->name, 'code' => $text], function ($message) use ($user, $subject) 
        {
            $message->to($user->email, $user->name);
            $message->subject($subject);
        });
    }

    /**
     * Upload photo/video
     * 
     * @return  boolean
     */
    public static function upload(Request $request)
    {
        if ($request->hasFile('photo'))
        {
            if ($request->file('photo')->isValid())
            {
                $path   = 'avatars';                                                // define the upload path
                $ext    = $request->file('photo')->getClientOriginalExtension();    // get original image extension
                $name   = uniqid();                                                 // generate a unique id
                $file   = $name.'.'.$ext;                                           // rename the image
                
                $request->file('photo')->move($path, $file);                        // upload file to given path

                $img    = Image::make($path.'/'.$file);                             // load image into memory 
                $img->fit(350, 350);                                                // crop image to fit dimensions
                $img->encode('jpg', 80)->save($path.'/'.$name.'.jpg');              // encode image into jpeg format

                return $name.'.jpg';                                                // return filename for saving
            }
            return false;
        }
        elseif ($request->hasFile('video'))
        {
            $path   = 'videos';                                                 // define the upload path
            $ext    = $request->file('video')->getClientOriginalExtension();    // get original video extension
            $name   = uniqid();                                                 // generate a unique id
            $file   = $name.'.'.$ext;                                           // rename the image
            
            $request->file('video')->move($path, $file);                        // upload file to given path

            return $file;                                                       // return filename for saving
        }
        return false;
    }
}