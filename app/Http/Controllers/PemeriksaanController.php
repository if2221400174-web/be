<?php

namespace App\Http\Controllers;

use App\Models\Pemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PemeriksaanController extends Controller
{
    public function index() {
        $pemeriksaan = Pemeriksaan::with(
        'rekam_medis.pasien',
        'user',
        'resep.details.obat'
        )->get();

        if ($pemeriksaan->isEmpty()){
            return response()->json([
                "success"=> true,
                "messege" => "resource data not found"
            ], 200);
        }
        return response()->json([
            "success"=> true,
            "messege" => "Get all resource",
            "data" => $pemeriksaan,
        ], 200);
    }

    public function store(Request $request){
        //1 validator
        try {
            $validator = Validator::make($request->all(),[
                'tanggal_pemeriksaan' => 'required|date',
                'keluhan' => 'required|string|max:500',
                'diagnosa' => 'required|string|max:500',
                'catatan' => 'required|string|max:500',
                'rekam_medis_id' => 'required|integer|exists:rekam_medis,id'
            ]);

            //2. check validator eror
            if ($validator->fails()){
                return response()->json([
                    "success"=> false,
                    "message" => $validator->errors()
                ], 422);
            };
        }  catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->errors()
            ], 422);
        }


        $pemeriksaan = Pemeriksaan::create([
            "tanggal_pemeriksaan" => $request->tanggal_pemeriksaan,
            "keluhan" => $request->keluhan,
            "diagnosa" => $request->diagnosa,
            "catatan" => $request->catatan,
            "rekam_medis_id" => $request->rekam_medis_id,
            'user_id' => Auth::id()
        ]);

        //5. response
        return response()->json([
            "success"=> true,
            "message" => "resource add successfully!",
            "data" => $pemeriksaan
        ],201);
    }
    public function show(string $id){
        $pemeriksaan = Pemeriksaan::with('rekam_medis', 'user')->find($id);

        if (!$pemeriksaan){
            return response()->json([
                "success" => false,
                "messege" => "resource not found",
            ]);
        }
        return response()->json([
            "success" => true,
            "messege" => "Get resource",
            "data" => $pemeriksaan
        ], 200);
    }
    public function update(string $id, Request $request){
        //mencari data
        $pemeriksaan = Pemeriksaan::find($id);
        if (! $pemeriksaan){
            return response()->json([
                "success" => false,
                "message" => "resourse not found"
            ], 404);
        }
        // 2 validator
        $validator = Validator::make($request->all(),[
            'keluhan' => 'required|string|max:500',
            'diagnosa' => 'required|string|max:500',
            'catatan' => 'required|string|max:500',
        ]);
        if ($validator->fails()) {
            return response()->json([
            "success" => false,
            "message" => $validator->errors()
        ], 422);
        }

        //3 siapkan data yang mau diupdate
        $data= [
            "keluhan" => $request->keluhan,
            "diagnosa" => $request->diagnosa,
            "catatan" => $request->catatan,
        ];
        //5. update data
        $pemeriksaan->update($data);
        return response()->json([
            "success" => true,
            "message" => "resourse updated successfully",
            "data" => $pemeriksaan
        ]);
    }

    public function destroy(string $id){
        $pemeriksaan = Pemeriksaan::find($id);
        if (!$pemeriksaan){
            return response()->json([
                "success" => true,
                "messege" => "resourse not found",
            ]);
        }
        $pemeriksaan ->delete();
        return response()->json([
            "success" => true,
            "messege" => "resourse deleted successfully",
        ]);
    }

}
