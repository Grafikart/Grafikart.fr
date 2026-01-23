<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handle redirection if the slug is not right
 */
class RedirectIfSlugMismatch
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $modelName): Response
    {
        $slug = $request->route('slug');
        $model = $request->route($modelName);

        assert($model instanceof Model, sprintf('Route parameter %s must be an eloquent model', $modelName));
        if ($slug !== $model->slug) {
            return to_route($request->route()->getName(), [
                ...$request->route()->parameters(),
                'slug' => $model->slug,
            ], 301);
        }

        return $next($request);
    }
}
