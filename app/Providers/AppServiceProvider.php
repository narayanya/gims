<?php

namespace App\Providers;
use App\Models\SeedRequest;
use App\Models\Notification;
use App\Models\Accession;
use App\Models\Lot;
use App\Models\Crop;
use App\Models\Variety;
use App\Observers\ActivityObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            $pendingRequestCount = 0;
            $pendingRequests     = collect();
            $notifications       = collect();
            $notificationCount   = 0;
            $authEmployeeId      = null;
            $authProfilePhoto    = asset('assets/images/users/avatar-1.jpg'); // default fallback

            if (Auth::check()) {
                $user  = Auth::user();
                $roles = $user->roles->pluck('slug')->toArray();

                // Employee ID for profile photo
                $authEmployeeId = $user->employee_id ?? null;

                // Build profile photo URL — works for both S3 and local storage
                if ($authEmployeeId) {
                    $imagePath = 'Employee_Image/' . $authEmployeeId . '.jpg';
                    $disk      = env('FILESYSTEM_DISK', 'local');

                    try {
                        if ($disk === 's3') {
                            // Generate a short-lived temporary URL from S3
                            $authProfilePhoto = \Illuminate\Support\Facades\Storage::disk('s3')
                                ->temporaryUrl($imagePath, now()->addMinutes(30));
                        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath)) {
                            $authProfilePhoto = \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath);
                        }
                        // else: keep the default avatar
                    } catch (\Throwable $e) {
                        // S3 not reachable or file missing — keep default avatar
                    }
                }

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

            $expiringSoon = Lot::with(['crop'])
                ->whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [
                    Carbon::today(),
                    Carbon::today()->addDays(120),
                ])
                ->orderBy('expiry_date', 'asc')
                ->take(5)
                ->get();

            $view->with(compact('notifications', 'notificationCount', 'pendingRequestCount', 'pendingRequests', 'expiringSoon', 'authEmployeeId', 'authProfilePhoto'));
        });
}
}
