<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $fillable = [
        'user_id',
        'level',
        'category',
        'message',
        'stack_trace',
        'context',
        'url',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    /**
     * Helper to log an exception or error message
     */
    public static function log($message, $level = 'error', $category = null, \Throwable $exception = null, $context = [])
    {
        try {
            return self::create([
                'user_id' => auth()->id(),
                'level' => $level,
                'category' => $category,
                'message' => $message,
                'stack_trace' => $exception ? $exception->getMessage() . "\n" . $exception->getTraceAsString() : null,
                'context' => $context,
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Fallback to standard Laravel log if DB logging fails
            \Illuminate\Support\Facades\Log::error('Failed to log to DB: ' . $e->getMessage());
            if ($exception) {
                \Illuminate\Support\Facades\Log::error($exception->getMessage());
            }
            return null;
        }
    }}
