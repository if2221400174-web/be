<?php

namespace App\Http\Controllers;

use App\Models\Detail_resep;
use App\Models\Resep;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    public function index() {
        $transaksi = Transaksi::with([
        'pemeriksaan.resep.details.obat',
        'pemeriksaan.rekam_medis.pasien'
        ])->get();

        if ($transaksi->isEmpty()){
            return response()->json([
                "success"=> true,
                "messege" => "resource data not found"
            ], 200);
        }
        return response()->json([
            "success"=> true,
            "messege" => "Get all resource",
            "data" => $transaksi,
        ], 200);
    }

    public function store(Request $request){
        //1 validator
        $validator = Validator::make($request->all(),[
            'jasa_medis' => 'required|integer',
            'pemeriksaan_id' => 'required|integer|exists:pemeriksaans,id'
        ]);

        //2. check validator eror
        if ($validator->fails()){
            return response()->json([
                "success"=> false,
                "message" => $validator->errors()
            ], 422);
        };

        //2. CEK: apakah transaksi sudah ada (anti double)
        $existing = Transaksi::where('pemeriksaan_id', $request->pemeriksaan_id)->first();

        if ($existing){
            return response()->json([
                "success" => false,
                "message" => "Transaksi untuk pemeriksaan ini sudah ada"
            ], 409);
        }

        //3. ambil resep + detail + obat
        $resep = Resep::with('details.obat')
            ->where('pemeriksaan_id', $request->pemeriksaan_id)
            ->first();

        if (!$resep){
            return response()->json([
                "success"=> false,
                "message" => "Resep tidak ditemukan"
            ], 404);
        }

        if ($resep->details->isEmpty()){
            return response()->json([
                "success"=> false,
                "message" => "Detail resep kosong"
            ], 404);
        }

        //4. hitung subtotal (lebih aman dari null)
        $subtotal = Detail_resep::where('resep_id', $resep->id)
        ->join('obats', 'detail_reseps.obat_id', '=', 'obats.id')
        ->sum('obats.harga_obat');

        //5. hitung total tarif
        $totalTarif = $request->jasa_medis + $subtotal;

        //6. insert data
        $transaksi = Transaksi::create([
            "jasa_medis" => $request->jasa_medis,
            "total_tarif" => $totalTarif,
            "pemeriksaan_id" => $request->pemeriksaan_id,
            "status" => "belum_bayar",
        ]);

        //5. response
        return response()->json([
            "success"=> true,
            "message" => "resource add successfully!",
            "data" => $transaksi
        ],201);
    }

    public function show(string $id){
        $transaksi = Transaksi::with([
        'pemeriksaan.resep.details.obat'
        ])->find($id);

        if (!$transaksi){
            return response()->json([
                "success"=> true,
                "messege" => "resource data not found"
            ], 200);
        }
        return response()->json([
            "success"=> true,
            "messege" => "Get resource",
            "data" => $transaksi,
        ], 200);
    }

    public function update(Request $request, string $id){
        $transaksi = Transaksi::find($id);

        if (!$transaksi){
            return response()->json([
                "success"=> true,
                "messege" => "resource data not found"
            ], 200);
        }

        //1 validator
        $validator = Validator::make($request->all(),[
            'jasa_medis' => 'required|integer',
            'pemeriksaan_id' => 'required|integer|exists:pemeriksaans,id',
            'status' => 'nullable|in:belum_bayar,lunas'
        ]);

        //2. check validator eror
        if ($validator->fails()){
            return response()->json([
                "success"=> false,
                "message" => $validator->errors()
            ], 422);
        };

        //3. ambil resep + detail + obat
        $resep = Resep::with('details.obat')
            ->where('pemeriksaan_id', $request->pemeriksaan_id)
            ->first();

        if (!$resep){
            return response()->json([
                "success"=> false,
                "message" => "Resep tidak ditemukan"
            ], 404);
        }

        if ($resep->details->isEmpty()){
            return response()->json([
                "success"=> false,
                "message" => "Detail resep kosong"
            ], 404);
        }

        //4. hitung subtotal dari obat
        $subtotal = Detail_resep::where('resep_id', $resep->id)
        ->join('obats', 'detail_reseps.obat_id', '=', 'obats.id')
        ->sum('obats.harga_obat');

        //5. hitung total tarif
        $totalTarif = $request->jasa_medis + $subtotal;

        //4. update data
        $transaksi->update([
            "jasa_medis" => $request->jasa_medis,
            "total_tarif" => $totalTarif,
            "pemeriksaan_id" => $request->pemeriksaan_id,
             "status" => $request->status ?? $transaksi->status,
        ]);

        //5. response
        return response()->json([
            "success"=> true,
            "message" => "resource update successfully!",
            "data" => $transaksi
        ],200);
    }

    public function destroy(string $id){
        $transaksi = Transaksi::find($id);

        if (!$transaksi){
            return response()->json([
                "success"=> true,
                "messege" => "resource data not found"
            ], 200);
        }

        $transaksi->delete();

        return response()->json([
            "success"=> true,
            "message" => "resource delete successfully!"
        ], 200);
    }
}
