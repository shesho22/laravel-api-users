<?php

namespace App\Http\Controllers\Api\questionarie;

use App\Http\Controllers\Controller;
use App\Models\Questionnarie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionnarieController extends Controller
{
    /**
     * Cuestionarios vinculados a grupos de la empresa (histórico / pasados).
     */
    public function past(int $companyId)
    {
        $questionnaires = DB::table('questionnarie as q')
            ->select('q.id', 'q.name')
            ->join('user_questionnarie as uq', 'q.id', '=', 'uq.questionnarie_id')
            ->join('user_group as ug', 'uq.group_id', '=', 'ug.id')
            ->where('ug.company_id', $companyId)
            ->groupBy('q.id', 'q.name')
            ->get();

        return response()->json($questionnaires, 200);
    }

    public function all()
    {
        $questionnaires = Questionnarie::all();
        return response()->json($questionnaires, 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questionarie = Questionnarie::all();
        return response()->json($questionarie, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
