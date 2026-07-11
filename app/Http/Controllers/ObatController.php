<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ObatController extends Controller
{
    public function index() {
        $obat=Obat::all();

        if($obat->isEmpty()){
            return response()->json([
                "success" => true,
                "messege" => "resource data not found !"
            ], 200);
        }


        return response()->json([
            "success" => true,
            "messege" => "Get all resource",
            "data" => $obat
        ], 200);
    }

    public function store(Request $request){
        //1. validator
        $validator = Validator::make($request->all(),[
            "nama_obat" => "required|string|max:255|regex:/[a-zA-Z]/",
            "harga_obat" => "required|string|max:255"
        ]);
        //2. check validator eror
        if ($validator->fails()){
            return response()->json([
                "success"=> false,
                "messege" => $validator->errors()
            ], 422);
        };
        //insert data
        $obat = Obat::create([
            "nama_obat" => $request->nama_obat,
            "harga_obat" => $request->harga_obat
        ]);

        return response()->json([
            "success" => true,
            "messege" => "resource created",
            "data" => $obat
        ], 201);
    }
    public function show(string $id){
        $obat = Obat::find($id);

        if (!$obat){
            return response()->json([
                "success" => false,
                "messege" => "resource not found",
            ]);
        }
        return response()->json([
            "success" => true,
            "messege" => "Get resource",
            "data" => $obat
        ], 200);
    }

    public function update(string $id, Request $request){
        //mencari data
        $obat = Obat::find($id);
        if (! $obat){
            return response()->json([
                "success" => false,
                "messege" => "resourse not found"
            ], 404);
        }
        // 2 validator
        $validator = Validator::make($request->all(),[
            "nama_obat" => "required|string|max:255|regex:/[a-zA-Z]/",
            "harga_obat" => "required|string|max:255"
        ]);
        if ($validator->fails()) {
            return response()->json([
            "success" => false,
            "message" => $validator->errors()
        ], 422);
        }
        //3 siapkan data yang mau diupdate
        $data= [
            "nama_obat" => $request->nama_obat,
            "harga_obat" => $request->harga_obat
        ];

        //5. update data
        $obat->update($data);
        return response()->json([
            "success" => true,
            "messege" => "resourse updated successfully",
            "data" => $data
        ], 200);
    }

    public function destroy(string $id){
        $obat = Obat::find($id);
        if (!$obat){
            return response()->json([
                "success" => false,
                "messege" => "resourse not found",
            ], 404);
        }
        $obat->delete();
        return response()->json([
            "success" => true,
            "messege" => "resourse deleted successfully",
        ], 200);
    }
}
