<?php

namespace NotificationChannels\Swiftsmsgh;

use Illuminate\Notifications\Notification;

class SwiftsmsghChannel
{
    private \Swiftsms\Swiftsmsgh $client;

    public function __construct(\Swiftsms\Swiftsmsgh $client)
    {
        $this->client = $client;
    }

    public function send($notifiable, Notification $notification)
    {
        $to = $notifiable->routeNotificationFor('Swiftsmsgh');

        if (!$to) {
            $to = $notifiable->routeNotificationFor(SwiftsmsghChannel::class);
        }

        if (!$to) {
            return null;
        }

        if (!method_exists($notification, 'toSwiftsmsgh')) {
            throw new \RuntimeException('Notification does not have a toSwiftsmsgh method.');
        }

        $message = $notification->toSwiftsmsgh($notifiable);

        if (is_string($message)) {
            $message = SwiftsmsghMessage::create($message);
        }

        if (!$message instanceof SwiftsmsghMessage) {
            return null;
        }

        // ... options loop from previous steps ...
        $options = [];
        if ($message->sender) {
            $options['sender_id'] = $message->sender;
        }
        if ($message->sendAt) {
            $options['schedule_time'] = $message->sendAt->format('Y-m-d H:i:s');
        }
        if ($message->campaign) {
            $options['campaign'] = $message->campaign;
        }
        if ($message->reference) {
            $options['reference'] = $message->reference;
        }
        if ($message->callbackUrl) {
            $options['callback_url'] = $message->callbackUrl;
        }

        try {
            $response = $this->dispatch($to, $message, $options);

            if ($response->isError()) {
                throw Exceptions\CouldNotSendNotification::serviceRespondedWithAnError($response);
            }

            return $response;

        } catch (\Exception $e) {
            throw Exceptions\CouldNotSendNotification::swiftsmsghError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Dispatch the message to the appropriate SDK method.
     */
    protected function dispatch(string $to, SwiftsmsghMessage $message, array $options): \Swiftsms\Response
    {
        switch ($message->type) {
            case 'voice':
                return $this->client->send_voice($to, $message->content, 'female', 'en-gb', $options);
            case 'mms':
                if (empty($message->mediaUrl)) {
                    throw new \InvalidArgumentException('MMS requires a media URL.');
                }
                return $this->client->send_mms($to, $message->content, $message->mediaUrl, $options);
            case 'otp':
                return $this->client->send_otp($to, $message->content, $options);
            case 'whatsapp':
                return $this->client->send_whatsapp($to, $message->content, $options);
            case 'viber':
                return $this->client->send_viber($to, $message->content, $options);
            case 'sms':
            default:
                return $this->client->send_sms($to, $message->content, $options);
        }
    }
}
