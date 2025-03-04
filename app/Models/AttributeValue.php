<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeValue extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['entity_id', 'attribute_id', 'value', 'created_by', 'updated_by'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'entity_id');
    }
}
