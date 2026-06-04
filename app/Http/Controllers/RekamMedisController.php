<?php

namespace App\Http\Controllers;

use App\Models\Rekam_medis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RekamMedisController extends Controller
{
    public function index()
    {
        $rekam_medis = Rekam_medis::with([
            'pasien',
            'pemeriksaan.user',
            'pemeriksaan.resep.details.obat'
        ])->get();

        if ($rekam_medis->isEmpty()) {
            return response()->json([
                "success" => true,
                "message" => "resource data not found"
            ], 200);
        }

        return response()->json([
            "success" => true,
            "message" => "Get all resource",
            "data" => $rekam_medis,
        ], 200);
    }

    public function store(Request $request){
        //1 validator
        $validator = Validator::make($request->all(),[
            'pasien_id' => 'required|integer|exists:pasien,id'
        ]);

        //2. check validator eror
        if ($validator->fails()){
            return response()->json([
                "success"=> false,
                "message" => $validator->errors()
            ], 422);
        };
        //4. insert data
        $rekam_medis = Rekam_medis::create([
            "pasien_id" => $request->input('pasien_id'),
        ]);

        //5. response
        return response()->json([
            "success"=> true,
            "message" => "resource add successfully!",
            "data" => $rekam_medis
        ],201);
    }
    public function show(string $id){
        $rekam_medis = Rekam_medis::with([
            'pemeriksaan.user',
            'pemeriksaan.resep.details.obat'
        ])->find($id);

        if (!$rekam_medis){
            return response()->json([
                "success" => false,
                "messege" => "resource not found",
            ]);
        }
        return response()->json([
            "success" => true,
            "messege" => "get detail resource",
            "data" => $rekam_medis
        ]);
    }

    public function update(string $id, Request $request){
        //mencari data
        $rekam_medis = Rekam_medis::find($id);
        if (! $rekam_medis){
            return response()->json([
                "success" => false,
                "message" => "resourse not found"
            ], 404);
        }
        // 2 validator
        $validator = Validator::make($request->all(),[
            'pasien_id' => 'required|integer|exists:pasien,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
            "success" => false,
            "message" => $validator->errors()
        ], 422);
        }

        //3 siapkan data yang mau diupdate
        $data= [
            "pasien_id" => $request->pasien_id,
        ];
        //5. update data
        $rekam_medis->update($data);
        return response()->json([
            "success" => true,
            "message" => "resourse updated successfully",
            "data" => $rekam_medis
        ]);
    }

    public function destroy(string $id){
        $rekam_medis = Rekam_medis::find($id);
        if (!$rekam_medis){
            return response()->json([
                "success" => true,
                "messege" => "resourse not found",
            ]);
        }
        $rekam_medis ->delete();
        return response()->json([
            "success" => true,
            "messege" => "resourse deleted successfully",
        ]);
    }
}
