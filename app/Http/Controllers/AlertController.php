<?php

namespace App\Http\Controllers;

use App\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;

class AlertController extends Controller
{
    //
    public function checkAlert($alert, $userRemind)
    {
        $diffInMinute = $this->diffInMinute($userRemind);
        if ($diffInMinute == $alert["minutes_shift"]) {
            if ($alert["level"] != 1) {
                $replied = false;

                for($i = 2; $i <= $alert['level']; $i ++) {
                    $replied = $this->checkPreviousLevel($userRemind["content"][($i - 1)], $userRemind);

                    if ($replied) {
                        break;
                    }
                }

                if (!$replied) {
                    $this->sendAlert($alert, $userRemind);
                }
            } else {
                $this->sendAlert($alert, $userRemind);
            }
        }
    }

    public function checkPreviousLevel($alert, $userRemind)
    {
        $from = "";
        $to = "";
        $todayWorkTime = $this->getTodayWorkingTime($userRemind);
        $time_in_24_hour_format  = date("H:i:s", strtotime($todayWorkTime));
        $prev_level_time = date('Y-m-d') . ' ' . $time_in_24_hour_format;
        foreach ($userRemind['content'] as $row) {
            if ($row['level'] == ($alert['level'] - 1)) {
                $from = date('Y-m-d H:i:s', strtotime('-' . ($row['minutes_shift'] - 1) . ' minutes', strtotime($prev_level_time)));
                $to = date('Y-m-d H:i:s', strtotime('+' . ($row['wait_minutes'] + 1) . ' minutes', strtotime($from)));
                break;
            }
        }

        if ($from == '' || $to == '') {
            return false;
        }

        $result = Reply::whereBetween('date_received', [$from, $to])->where("phone_number", $alert["phone_number"])->count();

        return $result && $result > 0;
    }

    private function sendAlert($alert, $userRemind)
    {
        if ($alert["type"] === "SMS") {
            $message = $alert["message"];
            $message = str_replace("{name}", $userRemind["name"], $message);
            $this->sendSMS($alert["phone_number"], $message);
        } else if ($alert["type"] === "Voice") {
            $this->sendVoiceMail($alert["phone_number"], $alert["message"]);
        }
        return true;
    }

    private function sendVoiceMail($to, $voiceSMS)
    {
        // send voice mail
        $sid = getenv('TWILIO_ACCOUNT_SID');
        $token = getenv('TWILIO_AUTH_TOKEN');
        $twilio_number = getenv('TWILIO_PHONE_NUMBER');
        $client = new Client($sid, $token);
        $client->account->calls->create(
            $to,
            $twilio_number,
            array(
                "url" => 'http://173.237.137.60/~vicauto/reminder-app/public/index.php/api/getAudioResponse?url='.$voiceSMS
            )
        );
        Log::info("Voice call sent" . $to);
    }
    public function getAudioResponse (Request $request) {
        $response = new VoiceResponse();
        $response->play('https://api.twilio.com/cowbell.mp3');
        echo $response;
    }
    function sendSMS($to, $sms_body)
    {
        // Your Account SID and Auth Token from twilio.com/console
        $sid = getenv('TWILIO_ACCOUNT_SID');
        $token = getenv('TWILIO_AUTH_TOKEN');
        $client = new Client($sid, $token);
        $client->messages->create(
            $to,
            [
                // A Twilio phone number you purchased at twilio.com/console
                'from' => getenv('TWILIO_PHONE_NUMBER'),
                // the body of the text message you'd like to send
                'body' => $sms_body
            ]
        );
        Log::info("SMS sent" . $to);
    }

    private function diffInMinute($userRemind)
    {
        $today = $this->getTodayWorkingTime($userRemind);
        return strtotime(now($userRemind['timezone'])) > strtotime($today) ? 'Over' : now($userRemind['timezone'])->diffInMinutes($today);
    }

    private function getTodayWorkingTime($userRemind){
        $today = now($userRemind['timezone'])->format("l");

        return $userRemind["reminder_time"][$today];
    }
}
