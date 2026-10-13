<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_path',
        'target_path',
        'status_code',
    ];

    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
        ];
    }
}
