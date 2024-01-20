<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class BigDeltaDuringImport extends Notification implements ShouldQueue
{
    use Queueable;

    private $source;
    private $old_value;
    private $new_value;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($source, $old_value, $new_value)
    {
        $this->source = $source;
        $this->old_value = $old_value;
        $this->new_value = $new_value;
    }

    static public function shouldNotify($old_value, $new_value) {
        if($old_value == $new_value)
            return false;

        if(0 == $old_value || 0 == $new_value)
            return true;

        $min = min($new_value, $old_value);
        $max = max($new_value, $old_value);

        $delta = $min / $max;

        return $delta < 0.5;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        $source = $this->source;
        $old_value = $this->old_value;
        $new_value = $this->new_value;

        return (new SlackMessage)
            ->warning()
            ->content(
                "*[BigDeltaDuringImport]*"
                . (($new_value > $old_value) ? "📈" : "📉")
            )
            ->attachment(function ($attachment) use ($source, $old_value, $new_value) {
                $url = route(config('admin.route.prefix') . '.sources.show', $source->id, false);
                $url = 'https://' . env('HEROKU_APP_NAME') . '.herokuapp.com' . $url;

                $attachment
                    ->title($source->name, $url)
                    ->fields([
                        'Old Value' => $old_value,
                        'New Value' => $new_value,
                    ]);
            });
    }
}
