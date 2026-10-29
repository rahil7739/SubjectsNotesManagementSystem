<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Note extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'subject_id', 'title', 'body'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class)->withTrashed();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(NoteAttachment::class);
    }
}
