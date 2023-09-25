<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            //Creamos el usuario
            $createUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_name' => $request->role_name
            ]);

            //Retornamos el Json con el usuario creado y el token
            return response()->json([
                'success'   => true,
                'token'     => $createUser->createToken('API TOKEN')->plainTextToken,
                'data'      => $createUser,
            ], 201);
        } catch (\Exception $e) {
            // Manejo de errores genéricos
            return response()->json(['success' => false, 'message' => 'Error en la solicitud' . $e->getMessage()], 500);
        }
    }

    public function login()
    {
        try {
            //Obtenemos solo el correo y la contraseña del request
            $credentials = request(['email', 'password']);

            //Si las credenciales no concuerdan devolvemos un mensaje de no autorizado
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['success' => false, 'message' => 'Acceso no autorizado'], 401);
            }

            //Instanciamos metodo para obtener el token 
            return $this->respondWithToken($token);
        } catch (\Exception $e) {
            // Manejo de errores genéricos
            return response()->json(['success' => false, 'message' => 'Error en la solicitud' . $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        //Eliminamos los token del usuario logieado y cerramos cesión
        auth()->user()->tokens()->delete();

        //Retornamos el Json con el mensaje de cierre de cesión
        return response()->json([
            'success'   => true,
            'message'      => "Se ha cerrado la sesión con exito",
        ], 200);
    }
    protected function respondWithToken($token)
    {
        try {
            //Obtenemos el usuario logueado  
            $user = auth()->user();

            //Respondemos con el token y los datos del usuario logueado
            return response()->json([
                'success' => true,
                'token_type' => 'bearer',
                'token' => $user->createToken('API TOKEN')->plainTextToken,
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores genéricos
            return response()->json(['success' => false, 'message' => 'Error en la solicitud' . $e->getMessage()], 500);
        }
    }
}
