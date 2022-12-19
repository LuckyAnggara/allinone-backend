<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'branch_code',
        'address',
        'phone_number',
        'head_id',
        'email',
        'fax_number',
    ];

    public function head()
    {
        return $this->hasOne(Employee::class, 'id', 'head_id');
    }
}
