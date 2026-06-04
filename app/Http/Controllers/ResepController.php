<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResepController extends Controller
{
    public function index() {
        $resep = Resep::with('pemeriksaan')->get();

        if ($resep->isEmpty()){
            return response()->json([
                "success"=> true,
                "messege" => "resource data not found"
            ], 200);
        }
        return response()->json([
            "success"=> true,
            "messege" => "Get all resource",
            "data" => $resep,
        ], 200);
    }



    public function store(Request $request){
    //1. validator
        $validator = Validator::make($request->all(),[
            'pemeriksaan_id' => 'required|exists:pemeriksaans,id'
        ]);

        //2. check validator error
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


        $resep = Resep::create([
            "pemeriksaan_id" => $request->pemeriksaan_id,
        ]);


        //5. response
        return response()->json([
            "success"=> true,
            "message" => "multiple resep created successfully!",
            "data" => $resep
        ],201);
    }

    public function show(string $id){
        $resep = Resep::with('pemeriksaan')->find($id);

        if (!$resep){
            return response()->json([
                "success" => false,
                "messege" => "resource not found",
            ]);
        }

        return response()->json([
            "success" => true,
            "messege" => "Get resource",
            "data" => $resep
        ], 200);
    }

    public function update(string $id, Request $request){
        //mencari data
        $resep = Resep::find($id);
        if (! $resep){
            return response()->json([
                "success" => false,
                "messege" => "resource not found",
            ]);
        }

        //1 validator
        $validator = Validator::make($request->all(),[
            'pemeriksaan_id' => 'required|exists:pemeriksaans,id'
        ]);

        //2. check validator eror
        if ($validator->fails()){
            return response()->json([
                "success"=> false,
                "message" => $validator->errors()
            ], 422);
        };
        //4. simpan banyak resep
            $resep = Resep::create([
                "pemeriksaan_id" => $request->pemeriksaan_id,
            ]);

        //5. response
        return response()->json([
            "success"=> true,
            "message" => "resource update successfully!",
            "data" => $resep
        ],200);
    }

    public function destroy(string $id){
        //mencari data
        $resep = Resep::find($id);
        if (! $resep){
            return response()->json([
                "success" => false,
                "messege" => "resource not found",
            ]);
        }

        //delete data
        $resep->delete();

        //response
        return response()->json([
            "success"=> true,
            "message" => "resource delete successfully!",
        ],200);
    }
}
