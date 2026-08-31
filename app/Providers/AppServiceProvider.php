<?php

namespace App\Providers;

use App\Services\Ai\AiServiceInterface;
use App\Services\Ai\OpenAiCompatibleAiService;
use App\Services\Pdf\PdfGeneratorInterface;
use App\Services\Pdf\ResumePdfGenerator;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PdfGeneratorInterface::class, ResumePdfGenerator::class);

        $this->app->bind(AiServiceInterface::class, function ($app) {
            $config = $app['config']->get('services.ai');

            return new OpenAiCompatibleAiService(
                baseUrl: $config['base_url'],
                apiKey: (string) $config['api_key'],
                model: $config['model'],
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        JsonResource::withoutWrapping();
    }
}
