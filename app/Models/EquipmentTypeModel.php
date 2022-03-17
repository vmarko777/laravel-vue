<?php

namespace App\Models;

use App\Models\EquipmentModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentTypeModel extends Model
{
    use HasFactory;

    public function hasEquimpent()
    {
        return $this->belongsTo(EquipmentModel::class);
    }

    protected $table = 'equipment_type';
    public $timestamps = false;
}
