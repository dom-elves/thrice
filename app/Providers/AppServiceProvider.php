<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Laravel\Reverb\Events\ChannelRemoved;
use App\Jobs\EndUserSession;
use Illuminate\Auth\Events\Logout;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // todo: fix this
        // i have no idea why but sometimes it works, sometimes not
        // events still seem to fire in telescope but not in Log so, idk
        // maybe further down the line i'll see if ttl stuff can just autoprune things
        // rather than relying on detecting user closing tab/window etc
        // Event::listen(ChannelRemoved::class, function (ChannelRemoved $event) {
        //     Log::info('ChannelRemoved fired', ['channel' => $event->channel->name()]);
        //     if (preg_match('/^presence-App\.Models\.User\.(\d+)$/', $event->channel->name(), $m)) {
        //         Log::info('preg', ['channel' => $event->channel->name()]);
        //         Log::info('m?', $m);
        //         $user = User::find($m[1]);

        //         if ($user !== null) {
        //             event(new Logout('web', $user));
        //         }
        //     }
        // });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
