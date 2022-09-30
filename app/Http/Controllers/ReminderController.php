<?php

namespace App\Http\Controllers;

use App\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReminderController extends Controller
{
    //
    private $UserController;

    public function __construct()
    {
        $this->UserController = new UserController;
    }

    public function index()
    {
        $reminders = $this->_readUsers();
        foreach ($reminders as $reminder) {
            $this->UserController->checkRemind($reminder);
        }

//        return response()->json($reminders);
    }

    private function _readUsers()
    {
        return json_decode(file_get_contents(public_path('./reminder.json')), true);
    }

    public function getResponse(Request $request)
    {
        var_dump($request["From"]);
        $reminders = $this->_readUsers();
        $timezone = 'PST';
        foreach ($reminders as $reminder) {
            if ($request["From"] == $reminder["phone_number"]) {
                $timezone = $reminder["timezone"];
            }
        }
        $time_val = new \DateTime();
        $time_val->setTimezone(new \DateTimeZone($timezone));
        $time_val = $time_val->format('Y-m-d H:i:s');
        Log::info("Replied from" . $request["From"]);
        $reply = Reply::create([
            "phone_number" => $request["From"],
            "message_body" => $request["Body"],
            "status" => $request["SmsStatus"],
            "date_received" => $time_val
        ]);
        return $reply;
    }
}
