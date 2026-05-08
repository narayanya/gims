<?php

namespace App\Providers;
use App\Models\SeedRequest;
use App\Models\Notification;
use App\Models\Accession;
use App\Models\Crop;
use App\Models\Variety;
use App\Observers\ActivityObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        // Register activity observers
        Accession::observe(new ActivityObserver('accession'));
        Crop::observe(new ActivityObserver('crop'));
        Variety::observe(new ActivityObserver('variety'));
        SeedRequest::observe(new ActivityObserver('request'));
        \App\Models\Storage::observe(new ActivityObserver('storage'));
        \App\Models\Lot::observe(new ActivityObserver('lot'));

        View::composer('*', function ($view) {
            $pendingRequestCount = 0;
            $pendingRequests     = collect();
            $notifications       = collect();
            $notificationCount   = 0;

            if (Auth::check()) {
                $user  = Auth::user();
                $roles = $user->roles->pluck('slug')->toArray();

                // Pending seed requests — visible to admin/super-admin
                if (array_intersect($roles, ['admin', 'super-admin'])) {
                    $pendingRequestCount = SeedRequest::where('status', 'pending')->count();
                    $pendingRequests     = SeedRequest::with(['user', 'crop'])
                        ->where('status', 'pending')
                        ->latest()
                        ->take(10)
                        ->get();
                }

                // Notifications for this user
                $notifications = Notification::where('user_id', $user->id)
                    ->latest()
                    ->take(10)
                    ->get();

                $notificationCount = Notification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count();
            }

            $expiringSoon = Accession::with(['crop'])
                ->whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [
                    Carbon::today(),
                    Carbon::today()->addDays(120),
                ])
                ->orderBy('expiry_date', 'asc')
                ->take(5)
                ->get();

            $view->with(compact('notifications', 'notificationCount', 'pendingRequestCount', 'pendingRequests', 'expiringSoon'));
        });
}
}
