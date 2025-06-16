<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $requestId = Str::uuid();

        $request->headers->set('X-Request-ID', $requestId);

        $response = $next($request);

        $endTime = microtime(true);
        $latency = round(($endTime - $startTime) * 1000);

        $this->logRequest($request, $response, $requestId, $latency);

        return $response;
    }

    private function logRequest(Request $request, Response $response, string $requestId, int $latency): void
    {
        try {
            AuditLog::query()->firstOrCreate([
                'request_id' => $requestId,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent() ?? '',
                'method' => $request->method(),
                'endpoint' => $request->path(),
                'request_payload' => $this->sanitizePayload($request->all()),
                'response_status' => $response->getStatusCode(),
                'response_payload' => $this->getResponsePayload($response),
                'latency_ms' => $latency,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log audit trail', [
                'error' => $e->getMessage(),
                'request_id' => $requestId,
            ]);
        }
    }

    private function sanitizePayload(array $payload): array
    {
        $sensitiveFields = ['token', 'secret', 'key'];

        foreach ($sensitiveFields as $field) {
            if (isset($payload[$field])) {
                $payload[$field] = '***';
            }
        }

        return $payload;
    }

    private function getResponsePayload(Response $response): ?array
    {
        if ($response->getStatusCode() >= 400) {
            $content = $response->getContent();
            return $content ? json_decode($content, true) : null;
        }

        return null;
    }
}
