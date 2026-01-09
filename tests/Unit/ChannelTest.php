<?php

namespace NotificationChannels\Swiftsmsgh\Test\Unit;

use Mockery;
use Illuminate\Notifications\Notification;
use NotificationChannels\Swiftsmsgh\SwiftsmsghChannel;
use Swiftsms\Swiftsmsgh;
use NotificationChannels\Swiftsmsgh\Test\TestCase;

class ChannelTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $client = Mockery::mock(Swiftsmsgh::class);
        $channel = new SwiftsmsghChannel($client);

        $notification = new TestNotification();
        $notifiable = new TestNotifiable();

        // Use real response object since it's a DTO
        $response = new \Swiftsms\Response([
            'status' => 'success',
            'message' => 'Message sent',
            'code' => 0
        ]);

        $client->shouldReceive('send_sms')->once()->andReturn($response);

        $result = $channel->send($notifiable, $notification);
        $this->assertInstanceOf(\Swiftsms\Response::class, $result);
    }

    /** @test */
    public function it_throws_exception_if_method_missing()
    {
        $client = Mockery::mock(Swiftsmsgh::class);
        $channel = new SwiftsmsghChannel($client);

        $notification = new TestNotificationMissingMethod();
        $notifiable = new TestNotifiable();

        // We expect a RuntimeException
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Notification does not have a toSwiftsmsgh method.');

        $channel->send($notifiable, $notification);
    }

    /** @test */
    public function it_throws_exception_on_api_error()
    {
        $client = Mockery::mock(Swiftsmsgh::class);
        $channel = new SwiftsmsghChannel($client);

        $notification = new TestNotification();
        $notifiable = new TestNotifiable();

        // Use real response object since it's a DTO
        $response = new \Swiftsms\Response([
            'status' => 'error',
            'message' => 'Insufficient credit',
            'code' => 402
        ]);

        $client->shouldReceive('send_sms')->once()->andReturn($response);

        $this->expectException(\NotificationChannels\Swiftsmsgh\Exceptions\CouldNotSendNotification::class);
        $this->expectExceptionMessage('Swiftsms-GH responded with error: Insufficient credit');

        $channel->send($notifiable, $notification);
    }
}

class TestNotifiable
{
    public function routeNotificationFor($driver)
    {
        if ($driver === 'Swiftsmsgh') {
            return '1234567890';
        }
        return null;
    }
}

class TestNotification extends Notification
{
    public function toSwiftsmsgh($notifiable)
    {
        return 'message content';
    }
}

class TestNotificationMissingMethod extends Notification
{
}
