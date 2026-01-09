<?php

namespace NotificationChannels\Swiftsmsgh\Exceptions;

class CouldNotSendNotification extends \Exception
{
    public static function serviceRespondedWithAnError($response): self
    {
        return new static(sprintf('Swiftsms-GH responded with error: %s', $response->message), $response->code ?? 400);
    }

    public static function swiftsmsghError(string $message, int $code): self
    {
        return new static(sprintf('Swiftsms-GH responded with error %d, message: %s', $code, $message), $code);
    }
}
