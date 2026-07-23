<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class PasienController extends Controller
{
    public function index() {
        $pasien=Pasien::all();

        if($pasien->isEmpty()){
            return response()->json([
                "success" => true,
                "message" => "resource data not found !"
            ], 200);
        }

        return response()->json([
            "success" => true,
            "message" => "Get all resource",
            "data" => $pasien
        ], 200);
    }

    public function store(Request $request)
    {
        // 1. VALIDASI
        $validator = FacadesValidator::make($request->all(), [
            "nama" => "required|regex:/^[a-zA-Z\s.,\']+$/",
            "alamat" => "required|string|max:255",
            "no_wa" => "nullable|string|max:20",
            "tanggal_lahir" => "required|date",
            "jenis_kelamin" => "required|in:Laki-laki,Perempuan"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 422);
        }

        // 2. GENERATE KODE REKAM MEDIS DINAMIS
        $length = 3;
        $attempt = 0;

        do {
            $min = pow(10, $length - 1);
            $max = pow(10, $length) - 1;
            $randomNumber = rand($min, $max);
            $kodeRekamMedis = 'PUMDR-' . $randomNumber;
            $exists = Pasien::where('kode_rekammedis', $kodeRekamMedis)->exists();

            if ($exists) {
                $attempt++;
                if ($attempt > 10) {
                    $length++;
                    $attempt = 0;
                }
            }

        } while ($exists);

        // 3. SIMPAN DATA PASIEN
        $pasien = Pasien::create([
            "nama" => $request->nama,
            "kode_rekammedis" => $kodeRekamMedis,
            "alamat" => $request->alamat,
            "tanggal_lahir" => $request->tanggal_lahir,
            "jenis_kelamin" => $request->jenis_kelamin,
            "no_wa" => $request->no_wa
        ]);

        // 4. RESPONSE
        return response()->json([
            "success" => true,
            "message" => "resource created",
            "data" => $pasien
        ], 201);
    }

    public function show(string $id){
        $pasien = Pasien::find($id);

        if (!$pasien){
            return response()->json([
                "success" => false,
                "message" => "resource not found",
            ]);
        }
        return response()->json([
            "success" => true,
            "message" => "get detail resource",
            "data" => $pasien
        ]);
    }

    public function update(string $id, Request $request){
        $pasien = Pasien::find($id);
        if (! $pasien){
            return response()->json([
                "success" => false,
                "message" => "resourse not found"
            ], 404);
        }
        
        // 2 validator
        $validator = FacadesValidator::make($request->all(),[
            "nama" =>"required|regex:/^[a-zA-Z\s.,\']+$/",
            "alamat" => "required|string|max:255",
            "no_wa" => "nullable|string|max:20", 
            "tanggal_lahir" => "required|date",
            "jenis_kelamin" => "required|in:Laki-laki,Perempuan"
        ]);
        
        if ($validator->fails()) {
            return response()->json([
            "success" => false,
            "message" => $validator->errors()
        ], 422);
        }
        
        // 3 siapkan data yang mau diupdate
        $data= [
            "nama" => $request->nama,
            "alamat" => $request->alamat,
            "tanggal_lahir" => $request->tanggal_lahir,
            "jenis_kelamin" => $request->jenis_kelamin,
            "no_wa" => $request->no_wa
        ];

        // 5. update data
        $pasien->update($data);
        return response()->json([
            "success" => true,
            "message" => "resourse updated successfully",
            "data" => $data
        ]);
    }

    public function destroy(string $id){
        $pasien = Pasien::find($id);
        if (!$pasien){
            return response()->json([
                "success" => true,
                "message" => "resourse not found",
            ]);
        }
        $pasien ->delete();
        return response()->json([
            "success" => true,
            "message" => "resourse deleted successfully",
        ]);
    }

    public function getDokumenPublik($kode_rm)
    {
        $pasien = Pasien::where('kode_rekammedis', $kode_rm)->first();

        if (!$pasien) {
            return response()->json([
                "success" => false,
                "message" => "Dokumen tidak ditemukan atau kode tidak valid."
            ], 404);
        }

        return response()->json([
            "success" => true,
            "message" => "Data dokumen pasien berhasil ditarik",
            "data" => $pasien
        ], 200);
    }
}