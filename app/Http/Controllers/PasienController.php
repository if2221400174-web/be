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
            "nama" => "required|string|max:255|regex:/^[^\d]+$/u",
            "alamat" => "required|string|max:255",
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
            // menentukan batas angka berdasarkan panjang digit
            $min = pow(10, $length - 1);
            $max = pow(10, $length) - 1;

            // generate angka acak
            $randomNumber = rand($min, $max);

            // gabungkan dengan prefix
            $kodeRekamMedis = 'PUMDR-' . $randomNumber;

            // cek apakah sudah ada di database
            $exists = Pasien::where('kode_rekammedis', $kodeRekamMedis)->exists();

            // jika sudah ada, tambah percobaan
            if ($exists) {
                $attempt++;

                // kalau terlalu sering gagal, tambah digit
                if ($attempt > 10) {
                    $length++;   // jadi 4 digit, 5 digit, dst
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
            "jenis_kelamin" => $request->jenis_kelamin
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
        //mencari data
        $pasien = Pasien::find($id);
        if (! $pasien){
            return response()->json([
                "success" => false,
                "message" => "resourse not found"
            ], 404);
        }
        // 2 validator
        $validator = FacadesValidator::make($request->all(),[
            "nama" => "required|string|max:255|regex:regex:/^[^\d]+$/u",
            "alamat" => "required|string|max:255",
            "tanggal_lahir" => "required|date",
            "jenis_kelamin" => "required|in:Laki-laki,Perempuan"
        ]);
        if ($validator->fails()) {
            return response()->json([
            "success" => false,
            "message" => $validator->errors()
        ], 422);
        }
        //3 siapkan data yang mau diupdate
        $data= [
            "nama" => $request->nama,
            "alamat" => $request->alamat,
            "tanggal_lahir" => $request->tanggal_lahir,
            "jenis_kelamin" => $request->jenis_kelamin
        ];

        //5. update data
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

}
