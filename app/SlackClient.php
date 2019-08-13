<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;

class SlackClient
{
    // Import Notifiable Trait
    use Notifiable;

    // Specify Slack Webhook URL to route notifications to
    public function routeNotificationForSlack()
    {
        return Config::get('notification.slack_channel_url');
    }
}