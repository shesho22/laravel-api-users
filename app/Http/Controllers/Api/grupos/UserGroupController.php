<?php

namespace App\Http\Controllers\Api\grupos;

use App\Http\Controllers\Controller;
use App\Models\UserGroup;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    public function index()
    {
        return response()->json(UserGroup::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|integer',
            'name'       => 'required|string|max:255',
            'bind'       => 'required|integer', // Ajusta el tipo según tu lógica
            'creation'   => 'required|date'     // O string si es un timestamp manual
        ]);

        $group = UserGroup::create($validated);
        return response()->json($group, 201);
    }

    public function show($id)
    {
        $group = UserGroup::find($id);
        if (!$group) return response()->json(['error' => 'Grupo no encontrado'], 404);
        return response()->json($group);
    }

    public function update(Request $request, $id)
    {
        $group = UserGroup::findOrFail($id);

        $group->update($request->all());

        return response()->json([
            'message' => 'Grupo actualizado',
            'group' => $group
        ]);
    }

    public function destroy($id)
    {
        UserGroup::destroy($id);
        return response()->json(['message' => 'Grupo eliminado']);
    }
}
