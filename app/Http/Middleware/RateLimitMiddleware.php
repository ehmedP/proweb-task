<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    private const WITHDRAWAL_RATE_LIMIT = 10;
    private const GENERAL_RATE_LIMIT = 100;
    private const RATE_LIMIT_WINDOW = 3600; // 1 saat

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $isWithdrawal = $request->routeIs('withdrawal.perform');

        if (!$user) {
            return $next($request);
        }

        $limit = $isWithdrawal ? self::WITHDRAWAL_RATE_LIMIT : self::GENERAL_RATE_LIMIT;
        $key = $this->getRateLimitKey($user->id, $isWithdrawal);

        $current = Redis::incr($key);

        if ($current === 1) {
            Redis::expire($key, self::RATE_LIMIT_WINDOW);
        }

        if ($current > $limit) {
            return response()->json([
                'success' => false,
                'message' => 'Rate limit exceeded. Please try again later.',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $response = $next($request);

        $response->headers->set('X-RateLimit-Limit', $limit);

        return $response;
    }

    private function getRateLimitKey(int $userId, bool $isWithdrawal): string
    {
        $type = $isWithdrawal ? 'withdrawal' : 'general';
        return "rate_limit:{$type}:{$userId}:" . now()->format('Y-m-d-H');
    }
}
