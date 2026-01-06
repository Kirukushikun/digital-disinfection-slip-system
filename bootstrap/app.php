<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'livewire/',
            'livewire/upload-file',
            'livewire/preview-file',
        ]);
        $middleware->alias([
            'user.type' => \App\Http\Middleware\EnsureUserType::class,
            'custom.throttle' => \App\Http\Middleware\CustomThrottleRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $e) {
            //
        });

        $exceptions->render(function (\Illuminate\Http\Request $request, Throwable $exception) {
            if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
                return redirect('/');
            }

            // Handle 409 Conflict errors
            if ($exception instanceof \Illuminate\Database\QueryException && $exception->getCode() == 23000) {
                // Handle unique constraint violations (duplicate entries)
                return response()->view('errors.409', [], 409);
            }

            // Handle other potential conflict scenarios
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                // Check if it's a conflict-related validation error
                $errors = $exception->errors();
                if (isset($errors['conflict']) || str_contains(json_encode($errors), 'already exists')) {
                    return response()->view('errors.409', ['errors' => $errors], 409);
                }
            }
        });
    })->create();