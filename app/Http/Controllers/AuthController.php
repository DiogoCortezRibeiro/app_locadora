<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request) {
        // autenticação do usuário (email e senha)
        $token = auth('api')->attempt($request->all(['email', 'password']));

        if ($token) {  // usuário autenticado com sucesso
            return response()->json(['token' => $token], 200);
        } 

        return response()->json(['erro' => 'Usuário ou senha inválidos!'], 403); // 403 = forbiden -> proibido (potencial login inválido)
    }

    public function logout() {
        auth('api')->logout();
        return response()->json(['msg' => 'Logout realizado com sucesso']);
    }

    public function refresh() {
        $token = auth('api')->refresh(); // cliente encaminhe um jwt válido
        return response()->json(['token' => $token]);
    }

    public function me() {
        return response()->json(auth()->user());
    }
}
