<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //

    private $AlertController;

    public function __construct()
    {
        $this->AlertController = new AlertController;
    }

    public function checkRemind($userRemind)
    {
        if (!$this->_isWorkday($userRemind)) {
            return $userRemind;
        }
        $alerts = $userRemind["content"];
        foreach ($alerts as $alert) {
            $res = $this->AlertController->checkAlert($alert, $userRemind);
        }
        return $userRemind;
    }

    private function _isWorkday($userRemind)
    {
        $today = now($userRemind['timezone'])->format("l");
        return $userRemind["reminder_time"][$today];
    }
}
