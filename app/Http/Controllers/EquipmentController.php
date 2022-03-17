<?php

namespace App\Http\Controllers;

//use App\Models\EquipmentModel;
use App\Models\EquipmentTypeModel;
use Illuminate\Support\Facades\DB;
//use Illuminate\Http\Requests\EquipmentRequest;

class EquipmentController extends Controller
{

    public function index()
    {
       return view('welcome');
    }


    public function getList()
    {
        $list = EquipmentTypeModel::all();

        return response()->json($list);
    }

}
