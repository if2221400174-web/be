<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function store(Request $request){
        //1. setup validator
        $validator = Validator::make($request->all(),[
            'username' => 'required|max:225',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
        //2. cek validator
        if ($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('users', 'public');
        }

        //3. create user
        $user = User::create([
            'username' => $request->username,
            'foto' => $fotoPath,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'dokter'
        ]);

        //4. cek keberhasilan
        if ($user){
            return response()->json([
                'succes' => true,
                'message' => 'user created successfully',
                'data' => $user
            ],201);
        }

        //5. cek gagal
        return response()->json([
            'succes'=> false,
            'message' => 'user creatin faild'
        ]);
    }

    public function login(Request $request){
        //1. setup validator
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //2. cek validator
        if ($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        //3. get kredensial dari request
        $credentials = $request->only('email', 'password');

        //4. cek isFailed
        if (!$token =auth()->guard('api')->attempt($credentials)){
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password anda salah !'
            ], 401);
        }

        //5. cek is Success
        return response()->json([
            'success' => true,
            'message' => 'Login successfully',
            'user' => auth()->guard('api')->user(),
            'token' =>$token,
        ], 200);
    }

    //lupa pasword
    public function forgotPassword(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => now(),
                'expired_at' => now()->addMinutes(60)
            ]
        );

        return response()->json([
            "success" => true,
            "message" => "Token reset dibuat",
            "token" => $token
        ]);
    }

    public function resetPassword(Request $request){
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record){
            return response()->json([
                "success" => false,
                "message" => "Token tidak valid"
            ],400);
        }

        if (now()->gt($record->expired_at)){
            return response()->json([
                "success" => false,
                "message" => "Token kadaluarsa"
            ],400);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            "success" => true,
            "message" => "Password berhasil direset"
        ]);
    }

    public function changePassword(Request $request){
    /** @var \App\Models\User $user */
    $user = auth('api')->user();

    if (!$user){
        return response()->json([
            "success" => false,
            "message" => "Unauthorized"
        ], 401);
    }

    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    if (!Hash::check($request->current_password, $user->password)){
        return response()->json([
            "success" => false,
            "message" => "Password lama salah"
        ], 400);
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json([
        "success" => true,
        "message" => "Password berhasil diubah"
    ]);
}

    public function logout(Request $request){
        try{
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Logout successfully'
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed'
            ], 500);
        }
    }

    //CRUD
    public function index() {
        $user = User::all();

        if($user->isEmpty()){
            return response()->json([
                "success"=>true,
                "messege" => "resource data not found"
            ], 200);
        }

        return response()->json([
            "success"=> true,
            "messege" => "Get all resource",
            "data" => $user
        ], 200);
    }

    //show
    public function show(string $id){
        $user = User::find($id);

        if(!$user){
            return response()->json([
                "success"=>false,
                "messege" => "resource not found"
            ], 404);
        }

        return response()->json([
            "success" => true,
            "messege" => "Get resource",
            "data" => $user
        ]);
    }

    //update
    public function update(Request $request, string $id)
    {
        // 1. Cari data
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "resource not found"
            ], 404);
        }

        // 2. Validator
        $validator = Validator::make($request->all(), [
            "username" => "required|string|max:255",
            "foto"     => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
            "email"    => "required|email|unique:users,email," . $user->id,
            "role"     => "required|string|in:admin,dokter|max:100",
            "password" => "nullable|string|min:8",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 400);
        }

        // 3. Siapkan data
        $data = [
            'username' => $request->username,
            'email'    => $request->email,
            'role'     => $request->role,
        ];

        // 4. Proses foto jika ada file baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto) {
                $oldPath = public_path('storage/' . $user->foto);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $data['foto'] = $request->file('foto')->store('users', 'public');
        }

        // 5. Update password jika dikirim
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        // 6. Update & return
        $user->update($data);

        return response()->json([
            "success" => true,
            "message" => "resource updated",
            "data"    => $user
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "Unauthorized"
            ], 401);
        }

        // VALIDASI
        $validator = Validator::make($request->all(), [
            "username" => "required|string|max:255",
            "email"    => "required|email|unique:users,email," . $user->id,
            "password" => "nullable|string|min:8",
            "foto"     => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 422);
        }

        // DATA DASAR
        $user->username = $request->username;
        $user->email = $request->email;

        // PASSWORD (optional)
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // FOTO
        if ($request->hasFile('foto')) {

            // hapus foto lama
            if ($user->foto) {
                $oldPath = public_path('storage/' . $user->foto);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $user->foto = $request->file('foto')->store('users', 'public');
        }

        $user->save();

        return response()->json([
            "success" => true,
            "message" => "Profile berhasil diupdate",
            "data" => $user
        ]);
    }
     //delete
    public function destroy(string $id){
        $user = User::find($id);
        if(!$user){
            return response()->json([
                "success"=>false,
                "messege" => "resourse not found"
            ], 404);
        }
        $user->delete();
        return response() ->json([
            "success" =>true,
            "messege" => "resource deleted",
            "data" => $user
        ], 200);
    }
}
