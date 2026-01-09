# Swiftsmsgh Notification Channel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/swiftsmsgh.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/swiftsmsgh)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/github/actions/workflow/status/laravel-notification-channels/swiftsmsgh/run-tests.yml?branch=main&style=flat-square)](https://github.com/laravel-notification-channels/swiftsmsgh/actions)
[![StyleCI](https://styleci.io/repos/339892204/shield)](https://styleci.io/repos/339892204)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/swiftsmsgh.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/swiftsmsgh)

📲 [Swiftsmsgh](https://app.swiftsmsgh.com) Notifications Channel for Laravel.  
This package makes it easy to send SMS notifications with Swiftsms-GH using the official [swiftsmsgh-api-sdk](https://github.com/swiftsmsgh/swiftsmsgh-api-sdk).

## Contents

-   [Installation](#installation)
    -   [Setting up the Swiftsmsgh service](#setting-up-the-Swiftsmsgh-service)
-   [Usage](#usage)
    -   [Basic SMS](#basic-sms)
    -   [Scheduled SMS](#scheduled-sms)
    -   [Voice Call](#voice-call)
    -   [MMS Message](#mms-message)
    -   [Whatsapp Message](#whatsapp-message)
    -   [Available methods](#available-methods)
-   [Error Handling](#error-handling)
-   [Changelog](#changelog)
-   [Testing](#testing)
-   [Security](#security)
-   [Contributing](#contributing)
-   [Credits](#credits)
-   [License](#license)

## Installation

You can install the package via composer:

```bash
composer require laravel-notification-channels/swiftsmsgh
```

### Configuration

Add your Swiftsmsgh SENDER_ID and API_TOKEN to your `.env`

```php
SWIFTSMSGH_API_TOKEN=100|yourapitoken # always required
SWIFTSMSGH_SENDER_ID=Demo # always required
```

Add the configuration to your `services.php` config file:

```php
'swiftsmsgh' => [
    'sender_id' => env('SWIFTSMSGH_SENDER_ID', 'sender_id'),
    'api_token' => env('SWIFTSMSGH_API_TOKEN', 'api_token'),
]
```

### Setting up the Swiftsmsgh service

You'll need a Swiftsms-GH account. Head over to their [website](https://www.app.swiftsmsgh.com/) and create or login to your account.

Navigate to `API Integration` and then `API Token` in the sidebar to copy existing one or generate an API Token.

## Usage

You can use the channel in your `via()` method inside the notification:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\Swiftsmsgh\SwiftsmsghMessage;
use NotificationChannels\Swiftsmsgh\SwiftsmsghChannel;

class InvoicePaid extends Notification
{
    public function via($notifiable)
    {
        return [SwiftsmsghChannel::class];
    }

    public function toSwiftsmsgh($notifiable)
    {
        return (new SwiftsmsghMessage)
            ->content("Your invoice has been paid! Amount: {$this->amount}")
            ->from('MyApp');
    }
}
```

In your notifiable model, make sure to include a `routeNotificationForSwiftsmsgh()` method, which returns a phone number including country code.

```php
public function routeNotificationForSwiftsmsgh()
{
    return $this->phone; // 233200000000
}
```

### Basic SMS

```php
public function toSwiftsmsgh($notifiable)
{
    return SwiftsmsghMessage::create("Hello there!");
}
```

### Scheduled SMS

You can schedule messages to be sent at a later time:

```php
public function toSwiftsmsgh($notifiable)
{
    return (new SwiftsmsghMessage("Happy Birthday!"))
        ->from('MyCompany')
        ->schedule(now()->addDay())
        ->campaign('BirthdayBlast')
        ->callbackUrl('https://example.com/delivery-report');
}
```

### Voice Call

Send a text-to-speech voice call:

```php
public function toSwiftsmsgh($notifiable)
{
    return SwiftsmsghMessage::createVoice("Your verification code is 1234");
}
```

### MMS Message

Send a multimedia message (requires a public media URL):

```php
public function toSwiftsmsgh($notifiable)
{
    return SwiftsmsghMessage::createMms(
        "Check out this image!",
        "https://example.com/image.jpg"
    );
}
```

### Whatsapp Message

```php
public function toSwiftsmsgh($notifiable)
{
    return SwiftsmsghMessage::createWhatsapp("Hello from Whatsapp API!");
}
```

### Available methods

-   `content(string $content)`: Set the message content.
-   `from(string $sender)` or `sender(string $sender)`: Set the sender ID.
-   `schedule(DateTimeInterface $time)`: Schedule the message for a future time.
-   `campaign(string $name)`: Set a campaign name for reporting.
-   `callbackUrl(string $url)`: Set a callback URL for delivery reports.
-   `reference(string $ref)`: Set a custom reference ID.

## Error Handling

If the API request fails (e.g. invalid credentials, insufficient balance), the channel will throw a `NotificationChannels\Swiftsmsgh\Exceptions\CouldNotSendNotification` exception.

This allows you to leverage Laravel's built-in notification retry mechanism or handle failures gracefully.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

```bash
$ composer test
```

## Security

If you discover any security related issues, please email support@swiftsmsgh.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

-   [Majesty-Scofield](https://github.com/majesty-scofield)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
