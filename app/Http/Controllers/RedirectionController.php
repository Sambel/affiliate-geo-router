<?php

namespace App\Http\Controllers;

use App\Services\RedirectionService;
use Illuminate\Http\Request;

class RedirectionController extends Controller
{
    public function __construct(
        protected RedirectionService $redirectionService
    ) {}

    public function redirect(Request $request, string $operatorSlug)
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $referer = $request->header('referer');

        $url = $this->redirectionService->getRedirectUrl(
            $operatorSlug,
            $ip,
            $userAgent,
            $referer
        );

        if (!$url) {
            abort(404, 'Operator not found or inactive');
        }

        return redirect()->away($url, 302);
    }
}