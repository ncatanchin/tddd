<?php

namespace PragmaRX\Tddd\Package\Support;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\NotifierFactory;
use PragmaRX\Tddd\Package\Events\TestsFailed;
use PragmaRX\Tddd\Package\Events\UserNotifiedOfFailure;

class Notifier
{
    /**
     * Are notifications enabled?
     *
     * @return bool
     */
    public function enabled()
    {
        return config('tddd.notifications.notify_on.fail') == true;
    }

    /**
     * Send notifications.
     *
     * @param $tests
     *
     * @return void
     */
    public function notifyViaChannels($tests)
    {
        if (!$this->enabled()) {
            return false;
        }

        collect(config('tddd.notifications.channels'))->each(function ($value, $channel) use ($tests) {
            event(new TestsFailed($tests, $channel));

            event(new UserNotifiedOfFailure($tests));
        });
    }

    /**
     * Send a notification.
     *
     * @param $title
     * @param $body
     * @param null $icon
     *
     * @return bool
     */
    public function notifyViaDesktop($title, $body, $icon = null)
    {
        if (!static::enabled()) {
            return false;
        }

        $notifier = NotifierFactory::create();

        $notification =
            (new Notification())
                ->setTitle($title)
                ->setBody($body);

        if (!is_null($icon)) {
            $notification->setIcon('http://vjeantet.fr/images/logo.png');
        }

        $notifier->send($notification);
    }
}
