<?php

namespace App\Http\Controllers\Api\questionarie;

use App\Http\Controllers\Controller;
use App\Models\UserQuestionnarie;
use Illuminate\Http\Request;

class UserQuestionnarieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questionaries = UserQuestionnarie::all();
        return response()->json($questionaries, 200);
        // return response()->json(['message' => 'Lista de cuestionarios de usuario'], 200);
    }


    public function questionnariesByGroup(int $groupId)
    {
        $data = UserQuestionnarie::query()
            ->join('questionnarie as qn', 'user_questionnarie.questionnarie_id', '=', 'qn.id')
            ->where('user_questionnarie.group_id', $groupId)
            ->select('qn.id as questionnarie_id', 'qn.name as questionnarie_name')
            ->distinct()
            ->orderBy('qn.name')
            ->get();
        return response()->json($data, 200);
    }
    public function UsersquestionnariesByGroup(int $groupId, int $questionariesId)
    {
        $data = UserQuestionnarie::query()
            ->join('user as u', 'user_questionnarie.user_id', '=', 'u.id')
            // 👇 Segundo join para obtener el nombre del objetivo
            ->join('user as ut', 'user_questionnarie.user_target_id', '=', 'ut.id')
            ->where('user_questionnarie.group_id', $groupId)
            ->where('user_questionnarie.questionnarie_id', $questionariesId)
            ->select(
                'u.id as user_id',
                'u.name as user_name',
                'ut.name as target_name',
                'user_questionnarie.id as id',
                'user_questionnarie.state as state',
                'user_questionnarie.deadline as deadline'
            )
            ->distinct()
            ->get();

        return response()->json($data, 200);
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
