<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Task extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'finished_at'];

    protected $dateFormat = 'Y-m-d';

    protected function casts(): array
    {
        return [
            'finished_at' => 'date:Y-m-d',
        ];
    }
}
