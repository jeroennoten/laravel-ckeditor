<?php


namespace JeroenNoten\LaravelCkEditor\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken
{
    public function handle($request, Closure $next)
    {
        if (!$this->tokensMatch($request)) {
            throw new TokenMismatchException;
        }

        return $next($request);
    }

    private function tokensMatch(Request $request)
    {
        $cookieToken = $request->cookie('ckCsrfToken');

        $token = $request->input('ckCsrfToken');

        if (!is_string($cookieToken) || !is_string($token)) {
            return false;
        }

        return hash_equals($cookieToken, $token);
    }
}