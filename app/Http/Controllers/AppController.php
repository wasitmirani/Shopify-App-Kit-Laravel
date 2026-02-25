<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use function config;
use function redirect;

class AppController extends Controller
{
    /**
     *  Validate shop domain and redirect to authenticate with shopify.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login(Request $request)
    {
        $request->validate([
            'store' => 'required|ends_with:myshopify.com',
        ], [
            'store.required' => 'Please enter store url',
        ]);
        $store = $request->input('store');
        $url = config('app.url') . '/authenticate?shop=' . $store;

        return redirect($url);
    }

    /**
     * Update the user or reauthorize.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateApp(Request $request)
    {
        if (!$request->shop) {
            return 'Shop Does Not Exist';
        }
        $apiKey = config('shopify-app.api_key');
        $apiScopes = config('shopify-app.api_scopes');
        $redirectUrl = config('app.url') . '/authenticate';
        $primaryDomain = $request->shop;

        $url = "https://{$primaryDomain}/admin/oauth/authorize/?client_id={$apiKey}&redirect_uri={$redirectUrl}&scope={$apiScopes}";

        return redirect($url);
    }
}
