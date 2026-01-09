<?php

namespace NotificationChannels\Swiftsmsgh\Test;

use NotificationChannels\Swiftsmsgh\SwiftsmsghServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            SwiftsmsghServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('services.swiftsmsgh.api_token', 'test_token');
        $app['config']->set('services.swiftsmsgh.sender_id', 'TEST_SENDER');
    }
}
