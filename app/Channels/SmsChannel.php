<?php

namespace App\Channels;

use Ghasedak\GhasedakApi;
use Illuminate\Notifications\Notification;


class SmsChannel
{
    public function send($notifiable, Notification $notification)
    {
        return "Done";

        $receptor = $notifiable->cellphone;
        $template = 'api';
        $param1 = $notification->code;

        $api = new GhasedakApi(env('GHASEDAK_API_KEY'));
        $api->Verify($receptor, $template, $param1);
    }
}
