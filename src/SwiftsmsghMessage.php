<?php

namespace NotificationChannels\Swiftsmsgh;

use DateTimeInterface;

class SwiftsmsghMessage
{
    public string $content;
    public ?string $sender = null;
    public ?string $campaign = null;
    public ?string $reference = null;
    public ?DateTimeInterface $sendAt = null;

    public string $type = 'sms'; // sms, voice, mms, otp, whatsapp, viber
    public ?string $mediaUrl = null;
    public ?string $callbackUrl = null;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public static function create(string $content = ''): self
    {
        return new self($content);
    }

    public static function createVoice(string $content): self
    {
        $message = new self($content);
        $message->type = 'voice';
        return $message;
    }

    public static function createMms(string $content, string $mediaUrl): self
    {
        $message = new self($content);
        $message->type = 'mms';
        $message->mediaUrl = $mediaUrl;
        return $message;
    }

    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    // ... existing methods ...


    public function from(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function schedule(DateTimeInterface $sendAt): self
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function sender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function sendAt(DateTimeInterface $sendAt): self
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function campaign(string $campaign): self
    {
        $this->campaign = $campaign;

        return $this;
    }

    public function reference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function callbackUrl(string $url): self
    {
        $this->callbackUrl = $url;

        return $this;
    }
}
