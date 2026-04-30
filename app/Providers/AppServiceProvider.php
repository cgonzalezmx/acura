<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        JsonResource::withoutWrapping();
        Vite::prefetch(concurrency: 3);

        Blueprint::macro('blamable', function() {
            $this->foreignId('created_by')->nullable()->constrained('users');
            $this->foreignId('updated_by')->nullable()->constrained('users');
            $this->foreignId('deleted_by')->nullable()->constrained('users');
        });

        Blueprint::macro('dropBlamable', function() {
            $this->dropConstrainedForeignId('created_by');
            $this->dropConstrainedForeignId('updated_by');
            $this->dropConstrainedForeignId('deleted_by');
        });

        Blueprint::macro('versioning', function() {
            $this->integer('version')->default(1);
        });
    }
}
