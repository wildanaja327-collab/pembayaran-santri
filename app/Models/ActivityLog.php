<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'username',
        'role',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Helper statis untuk mencatat log aktivitas sistem secara konsisten
     */
    public static function record(string $action, string $description, ?User $user = null): self
    {
        $currentUser = $user ?? Auth::user();

        return self::create([
            'user_id' => $currentUser?->id_user,
            'username' => $currentUser?->username ?? 'system/tamu',
            'role' => $currentUser?->role ?? 'system',
            'action' => strtoupper($action),
            'description' => $description,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => substr(Request::userAgent() ?? 'CLI/System', 0, 255),
            'created_at' => now(),
        ]);
    }
}

