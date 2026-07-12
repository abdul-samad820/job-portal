<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'file_path',
        'original_name',
        'file_size',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function getFileSizeForHumansAttribute(): string
    {
        $bytes = $this->file_size ?? 0;

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / (1024 * 1024), 1).' MB';
    }

    protected static function booted()
    {
        // Clean up the physical file whenever a resume record is removed —
        // but only if no application still points at this exact file path.
        // Applications store the path as a snapshot (resume_id is nulled
        // via nullOnDelete), so deleting the file from under them would
        // break their "View Resume" link.
        static::deleting(function ($resume) {
            $stillUsed = JobApplication::where('resume', $resume->file_path)->exists();

            if ($resume->file_path && ! $stillUsed) {
                Storage::disk('public')->delete($resume->file_path);
            }
        });
    }
}
