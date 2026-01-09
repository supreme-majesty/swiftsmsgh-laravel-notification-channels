<?php

namespace NotificationChannels\Swiftsmsgh;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Swiftsms\Swiftsmsgh;

class SwiftsmsghServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->singleton(Swiftsmsgh::class, function ($app) {
            if (
                empty($app['config']['services.swiftsmsgh.sender_id'])
                || empty($app['config']['services.swiftsmsgh.api_token'])
            ) {
                throw new \InvalidArgumentException('Missing swiftsmsgh config in services');
            }

            return new Swiftsmsgh(
                $app['config']['services.swiftsmsgh.api_token'],
                $app['config']['services.swiftsmsgh.sender_id']
            );
        });
    }

    public function provides(): array
    {
        return [Swiftsmsgh::class];
    }
}
