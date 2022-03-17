<?php

namespace App\Models;

use App\Models\EquipmentTypeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentModel extends Model
{
    use HasFactory;


    public function hasType() {
        return $this->hasOne(EquipmentTypeModel::class, 'equipment_id', 'id');
    }

    protected $table = 'equipment';
    protected $fillable = ['sn', 'type_code', 'notes'];
}
