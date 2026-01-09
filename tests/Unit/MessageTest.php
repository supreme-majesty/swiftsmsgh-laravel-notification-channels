<?php

namespace NotificationChannels\Swiftsmsgh\Test\Unit;

use NotificationChannels\Swiftsmsgh\SwiftsmsghMessage;
use NotificationChannels\Swiftsmsgh\Test\TestCase;

class MessageTest extends TestCase
{
    /** @test */
    public function it_can_accept_content_when_constructed()
    {
        $message = new SwiftsmsghMessage('Hello World');

        $this->assertEquals('Hello World', $message->content);
    }

    /** @test */
    public function it_can_set_content()
    {
        $message = (new SwiftsmsghMessage)->content('Hello World');

        $this->assertEquals('Hello World', $message->content);
    }

    /** @test */
    public function it_can_set_sender()
    {
        $message = (new SwiftsmsghMessage)->sender('MySender');

        $this->assertEquals('MySender', $message->sender);
    }

    /** @test */
    public function it_can_set_campaign()
    {
        $message = (new SwiftsmsghMessage)->campaign('MyCampaign');

        $this->assertEquals('MyCampaign', $message->campaign);
    }

    /** @test */
    public function it_can_set_reference()
    {
        $message = (new SwiftsmsghMessage)->reference('REF123');

        $this->assertEquals('REF123', $message->reference);
    }
}
