<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'type', 'created_by', 'updated_by'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by'];

    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
