<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    use HasFactory;

    public $table = 'employees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'patronymic',
        'gender',
        'salary',
    ];

    /**
     * @return string
     */
    public function getFullNameAttribute(): string {
        return $this->name . ' ' . $this->surname . ' ' . $this->patronymic;
    }

    /**
     * @return BelongsToMany
     */
    public function departments(): BelongsToMany {
        return $this->belongsToMany(Department::class);
    }
}
