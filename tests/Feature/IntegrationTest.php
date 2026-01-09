<?php

namespace NotificationChannels\Swiftsmsgh\Test\Feature;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Mockery;
use NotificationChannels\Swiftsmsgh\SwiftsmsghChannel;
use NotificationChannels\Swiftsmsgh\SwiftsmsghMessage;
use NotificationChannels\Swiftsmsgh\SwiftsmsghServiceProvider;
use Orchestra\Testbench\TestCase;
use Swiftsms\Swiftsmsgh;
use Swiftsms\Response;

class IntegrationTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [SwiftsmsghServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('services.swiftsmsgh.api_token', 'test_token');
        $app['config']->set('services.swiftsmsgh.sender_id', 'TestSender');
    }

    /** @test */
    public function it_resolves_dependencies_and_sends_notification()
    {
        // Mock the SDK
        $mockClient = Mockery::mock(Swiftsmsgh::class);
        $this->instance(Swiftsmsgh::class, $mockClient);

        $response = new Response([
            'status' => 'success',
            'message' => 'Message sent',
            'code' => 0
        ]);

        $mockClient->shouldReceive('send_sms')
            ->once()
            ->withArgs(function ($to, $content, $options) {
                return $to === '233240000000' &&
                    $content === 'Integration Test' &&
                    $options['sender_id'] === 'MySender' &&
                    $options['callback_url'] === 'https://example.com/callback';
            })
            ->andReturn($response);

        // Send Notification
        $notification = new TestIntegrationNotification();
        $notifiable = new TestIntegrationNotifiable();

        $notifiable->notify($notification);
    }
}

class TestIntegrationNotifiable
{
    use Notifiable;

    public function routeNotificationForSwiftsmsgh()
    {
        return '233240000000';
    }
}

class TestIntegrationNotification extends Notification
{
    public function via($notifiable)
    {
        return [SwiftsmsghChannel::class];
    }

    public function toSwiftsmsgh($notifiable)
    {
        return (new SwiftsmsghMessage('Integration Test'))
            ->from('MySender')
            ->callbackUrl('https://example.com/callback');
    }
}
