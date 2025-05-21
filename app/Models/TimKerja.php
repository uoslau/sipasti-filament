<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimKerja extends Model
{
    /** @use HasFactory<\Database\Factories\TimKerjaFactory> */
    use HasFactory;

    // add fillable
    protected $fillable = [];
    // add guaded
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }
}
