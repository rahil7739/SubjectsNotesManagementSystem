<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'name', 'description'];

    protected static function booted(): void
    {
        static::deleted(function (Subject $subject): void {
            if ($subject->isForceDeleting()) {
                return;
            }

            DB::table('notes')->where('subject_id', $subject->id)->whereNull('deleted_at')->update([
                'deleted_at' => $subject->deleted_at,
                'deleted_with_subject' => true,
                'updated_at' => now(),
            ]);
        });

        static::restoring(function (Subject $subject): void {
            DB::table('notes')->where('subject_id', $subject->id)->where('deleted_with_subject', true)->update([
                'deleted_at' => null,
                'deleted_with_subject' => false,
                'updated_at' => now(),
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
