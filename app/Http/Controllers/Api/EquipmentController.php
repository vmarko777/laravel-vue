<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EquipmentResource;
use App\Models\EquipmentModel;
use App\Models\EquipmentTypeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return EquipmentResource::collection(EquipmentModel::with('hasType')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         Validator::validate($request->all(), [
            '*.sn' => ['required', 'regex:/^[0-9a-zA-Z-_\@]+$/','unique:equipment'],
            '*.equipment_id'   => ['sometimes', 'required', 'numeric'],
            '*.notes'   => ['max:2048']
        ]);
        
        foreach($request->all() as $req) {
            $req['type_code'] = mt_rand(10000000000, 99999999999);

            $sn_mask = $this->replace($req['sn']);

            $equpment = EquipmentModel::create($req);
            $equpment_type = new EquipmentTypeModel;

            $e_type = $equpment_type::where('equipment_id', $req['equipment_id'])->get();

            $equpment_type->sn_mask = $sn_mask;
            $equpment_type->equipment_id = $equpment->id;
            $equpment_type->e_type = $e_type[0]->e_type;
            $equpment_type->save();
        }
         return response('ok', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EquipmentModel  $equipmentModel
     * @return \Illuminate\Http\Response
     */
    public function show($id, EquipmentModel $equipmentModel)
    {
        return new EquipmentResource($equipmentModel::find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EquipmentModel  $equipmentModel
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request, EquipmentModel $equipmentModel)
    {
        $e = $equipmentModel::find($id);
        $sn_mask = '';

        if ($request->input('sn') != $e->sn) {
            $request->validate([
                'sn' => ['regex:/^[0-9a-zA-Z-_\@]+$/','unique:equipment']
            ]);

            $sn_mask = $this->replace($request->input('sn'));
            $e->sn = $request->input('sn');
            EquipmentTypeModel::where('equipment_id', $id)->update(['sn_mask' => $sn_mask]);
        } 

        $e_type = EquipmentTypeModel::where('equipment_id', $request->input('id'))->select('e_type')->get();
        EquipmentTypeModel::where('equipment_id', $id)->update(['e_type' => $e_type[0]->e_type]);

        $e->notes = $request->input('notes');
        $e->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EquipmentModel  $equipmentModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, EquipmentModel $equipmentModel)
    {
        $equipmentModel::find($id)->delete();

        return response()->noContent();
 
   }

   private function replace($sn) {
        $patterns = ['/\d+/', '/[A-Z]+/', '/[a-z]+/', "/\D\d\D/", "/\D[A-Z]\D/", '/-|_|@/'];
        $replace = ['N', 'A', 'a', 'X', 'X', 'Z'];

        $sn_mask = preg_replace($patterns, $replace, $sn);

        return $sn_mask;
   }
}
