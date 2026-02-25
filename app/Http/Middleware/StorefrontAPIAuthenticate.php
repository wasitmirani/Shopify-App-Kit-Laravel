<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

use function explode;
use function response;

class StorefrontAPIAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $domain = $request->shop;
        $onlyName = @explode('.', $domain)[0];
        $domain = $onlyName . '.myshopify.com';

        /** @var User $shop */
        $user = User::where('name', $domain)->whereNotNull('password')->first();

        if (empty($user)) {
            return response(['message' => 'Your store is not linked with the Iconito.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        Auth::login($user, true);

        return $next($request);
    }
}
