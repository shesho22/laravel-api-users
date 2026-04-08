<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\DTOs\UserDTO;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\UserGroup;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json(
            $this->service->getAll()
        );
    }

    // POST /api/users -> Crear nuevo usuario
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|integer',
            'admin'    => 'required|boolean',
            'name'     => 'required|string|max:255',
            'cedula'   => 'required|string|unique:user',
            'email'    => 'required|email|unique:user',
            'pass'     => 'required|string|min:4'
        ]);

        $user = User::create($validated);

        return response()->json([
            'message' => 'Usuario creado con éxito',
            'user' => $user
        ], 201);
    }

    // GET /api/users/{id} -> Ver detalle
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['error' => 'No encontrado'], 404);

        return response()->json($user);
    }

    // PUT /api/users/{id} -> Actualizar
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'group_id' => 'integer',
            'admin'    => 'boolean',
            'name'     => 'string|max:255',
            'cedula'   => 'string|unique:user,cedula,' . $id,
            'email'    => 'email|unique:user,email,' . $id,
            'pass'     => 'string'
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Usuario actualizado',
            'user' => $user
        ]);
    }
    public function updatePassword(Request $request, $id)
    {
        // 1. Validar que la nueva contraseña llegue y sea segura (mínimo 6 caracteres por ejemplo)
        $request->validate([
            'pass' => 'required|string|min:6|confirmed' // 'confirmed' busca un campo 'pass_confirmation'
        ]);
        if (auth()->user()->admin == 0 && auth()->id() != $id) {
            return response()->json(['error' => 'No puedes cambiar contraseñas ajenas'], 403);
        }
        // 2. Buscar al usuario
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // 3. Actualizar la columna 'pass' en texto plano
        $user->pass = $request->pass;
        $user->save();

        return response()->json([
            'message' => 'Contraseña actualizada con éxito'
        ], 200);
    }
    // DELETE /api/users/{id} -> Eliminar
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['error' => 'No encontrado'], 404);

        //$user->delete();
        return response()->json(['message' => 'no se elimina el usuario']);
    }
    public function usersByCompany($companyId)
    {
        // 1. Get all group IDs that belong to the given company
        $groupIds = UserGroup::where('company_id', $companyId)->pluck('id');

        // 2. Fetch users whose group_id is within those groups
        $users = User::whereIn('group_id', $groupIds)->get();

        return response()->json($users, 200);
    }
}   
