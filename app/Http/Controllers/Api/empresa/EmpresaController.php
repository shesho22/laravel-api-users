<?php

namespace App\Http\Controllers\Api\empresa;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    //
    public function index()
    {
        return response()->json(Company::all(), 200);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id'          => 'required|integer|unique:company,id', // Si el ID lo envías tú
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        $validated['creation'] = now();
        $company = Company::create($validated);

        return response()->json($company, 201);
    }
    public function show($id)
    {
        $company = Company::find($id);
        if (!$company) return response()->json(['error' => 'Empresa no encontrada'], 404);
        return response()->json($company);
    }
    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $company->update($request->all());
        return response()->json([
            'message' => 'Empresa actualizada',
            'company' => $company
        ]);
    }
    public function destroy($id)
    {
        Company::destroy($id);
        return response()->json(['message' => 'Empresa eliminada']);
    }
}
