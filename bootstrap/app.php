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
            'super.guard' => \App\Http\Middleware\EnsureSuperGuard::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $e) {
            //
        });

        $exceptions->render(function (\Illuminate\Http\Request $request, Throwable $exception) {
            // Handle CSRF token mismatch (419) - redirect to landing
            if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
                return redirect('/')->with('status', 'Your session has expired. Please try again.');
            }

            // Handle 404 Not Found - redirect to landing
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return redirect('/')->with('status', 'The page you are looking for could not be found.');
            }

            // Handle 403 Forbidden (Unauthorized) - redirect to landing
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException || 
                ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $exception->getStatusCode() === 403)) {
                return redirect('/')->with('status', 'You do not have permission to access this page.');
            }

            // Handle 409 Conflict errors (including session ended) - redirect to landing
            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $exception->getStatusCode() === 409) {
                return redirect('/')->with('status', 'Your session has ended. Please log in again.');
            }

            // Handle database query exceptions that might be 409
            if ($exception instanceof \Illuminate\Database\QueryException && $exception->getCode() == 23000) {
                return redirect('/')->with('status', 'A conflict occurred. Please try again.');
            }

            // Handle authentication exceptions (session expired)
            if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                return redirect('/')->with('status', 'Your session has expired. Please log in again.');
            }

            // Handle other potential conflict scenarios
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                // Check if it's a conflict-related validation error
                $errors = $exception->errors();
                if (isset($errors['conflict']) || str_contains(json_encode($errors), 'already exists')) {
                    return redirect('/')->with('status', 'A conflict occurred. Please try again.');
                }
            }
        });
    })->create();