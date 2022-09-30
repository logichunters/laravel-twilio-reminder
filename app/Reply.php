<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    //
    protected $table = "replies";
    protected $fillable = [
        "phone_number", "message_body", "status", "date_received"
    ];
    /**
     * @var mixed
     */
}
