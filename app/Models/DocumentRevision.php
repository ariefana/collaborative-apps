<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentRevision extends Model
{
    use HasFactory;

    protected $fillable = ['document_id', 'user_id', 'content'];

    // relasi balik ke User agar frontend bisa membaca nama pembuat revisi
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
