<?php

namespace App\Http\Controllers;

use App\Models\Detail_resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DetailResepController extends Controller
{

    public function index()
    {
        $detai_resep = Detail_resep::with('resep', 'obat')->get();

        if ($detai_resep->isEmpty()){
            return response()->json([
                'message' => 'Data detai resep tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Data detai resep berhasil diambil',
            'data' => $detai_resep
        ], 200);
    }


    public function store(Request $request)
    {
        //1. validator
        $validator = Validator::make($request->all(),[
            'resep_id' => 'required|exists:reseps,id',
            'items' => 'required|array|min:1',
            'items.*.obat_id' => 'required|exists:obats,id',
            'items.*.aturan_pakai' => 'required|string|max:255'
        ]);

        //2. check validator eror
        if ($validator->fails()){
            return response()->json([
                "success"=> false,
                "message" => $validator->errors()
            ], 422);
        }

        //3. cek user login
        $user = auth('api')->user();
        if (!$user){
            return response()->json([
                'success' => false,
                'message' => 'user not found'
            ], 401);
        }

        $data = [];

        foreach ($request->items as $item) {
            $detail = Detail_resep::create([
                'resep_id' => $request->resep_id,
                'obat_id' => $item['obat_id'],
                'aturan_pakai' => $item['aturan_pakai'],
            ]);

            $data[] = $detail;
        }
        return response()->json([
            "success"=> true,
            "message" => "Data detai resep berhasil disimpan",
            "data" => $data
        ], 201);
    }

    public function show(string $id)
    {
        $detai_resep = Detail_resep::with('resep', 'obat')->find($id);

        if (!$detai_resep){
            return response()->json([
                'message' => 'Data detai resep tidak ditemukan'
            ], 200);
        }

        return response()->json([
            'message' => 'Data detai resep berhasil diambil',
            'data' => $detai_resep
        ], 200);
    }

    public function update(Request $request, $id)
    {
        // 1. cari data
        $detailResep = Detail_resep::find($id);

        if (!$detailResep) {
            return response()->json([
                'success' => false,
                'message' => 'Data detail resep tidak ditemukan'
            ], 404);
        }

        // 2. validasi
        $validator = Validator::make($request->all(), [
            'obat_id' => 'required|exists:obats,id',
            'aturan_pakai' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 422);
        }

        // 3. cek user login
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // 4. update data
        $detailResep->update([
            "obat_id" => $request->obat_id,
            "aturan_pakai" => $request->aturan_pakai
        ]);

        return response()->json([
            "success" => true,
            "message" => "Data detail resep berhasil diupdate",
            "data" => $detailResep
        ], 200);
    }

    public function destroy(string $id)
    {
        $detai_resep = Detail_resep::find($id);

        if (!$detai_resep){
            return response()->json([
                'message' => 'Data detai resep tidak ditemukan'
            ], 404);
        }

        $detai_resep->delete();

        return response()->json([
            'message' => 'Data detai resep berhasil dihapus'
        ], 200);
    }
}
