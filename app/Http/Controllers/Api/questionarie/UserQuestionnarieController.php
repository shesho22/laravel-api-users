<?php

namespace App\Http\Controllers\Api\questionarie;

use App\Http\Controllers\Controller;
use App\Models\UserQuestionnarie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserQuestionnarieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questionaries = UserQuestionnarie::all();
        return response()->json($questionaries, 200);
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
     * Registro masivo de evaluaciones.
     *
     * Por cada ítem se generan hasta 3 tipos de registros en user_questionnarie:
     *   rol 0 → auto-evaluación  (user_id = user_target_id = evaluado_id)
     *   rol 1 → líder             (user_id = lider.usuario_id)
     *   rol 2 → evaluador         (user_id = evaluador.usuario_id)
     *
     * El peso mínimo para auto-evaluación es 10 cuando llega en 0.
     */
    public function masiva(Request $request)
    {
        $request->validate([
            'group_id'                                  => 'required|integer',
            'questionnarie_id'                          => 'required|integer',
            'evaluaciones'                              => 'required|array|min:1',
            'evaluaciones.*.evaluado_id'                => 'required|integer',
            'evaluaciones.*.porcentaje_auto_evaluacion' => 'nullable|numeric|min:0',
            'evaluaciones.*.auto_evaluacion'            => 'nullable|boolean',
            'evaluaciones.*.lideres'                    => 'nullable|array',
            'evaluaciones.*.lideres.*.usuario_id'       => 'required|integer',
            'evaluaciones.*.lideres.*.porcentaje'       => 'nullable|numeric|min:0',
            'evaluaciones.*.evaluadores'                => 'nullable|array',
            'evaluaciones.*.evaluadores.*.usuario_id'   => 'required|integer',
            'evaluaciones.*.evaluadores.*.porcentaje'   => 'nullable|numeric|min:0',
        ]);

        $groupId         = $request->input('group_id');
        $questionnarieId = $request->input('questionnarie_id');
        $evaluaciones    = $request->input('evaluaciones');

        // Peso mínimo garantizado para auto-evaluación cuando llega en 0
        $autoEvalMinWeight = 10;

        // Fechas en zona horaria de Colombia
        $start    = Carbon::now('America/Bogota');
        $deadline = $start->copy()->addMonth();

        $rows = [];
        $skipped = 0;

        // Validar registros existentes para evitar duplicados
        $existingRecords = UserQuestionnarie::where('group_id', $groupId)
            ->where('questionnarie_id', $questionnarieId)
            ->get(['user_id', 'user_target_id'])
            ->mapWithKeys(function ($item) {
                return [$item->user_id . '_' . $item->user_target_id => true];
            })->toArray();

        foreach ($evaluaciones as $eval) {
            $evaluadoId  = $eval['evaluado_id'];
            $autoPercent = $eval['porcentaje_auto_evaluacion'] ?? 0;

            // ── Auto-evaluación (rol = 0) ─────────────────────────────────
            if (!empty($eval['auto_evaluacion'])) {
                if (isset($existingRecords[$evaluadoId . '_' . $evaluadoId])) {
                    $skipped++;
                } else {
                    $weight = $autoPercent > 0 ? $autoPercent : $autoEvalMinWeight;
                    $rows[] = [
                        'group_id'         => $groupId,
                        'questionnarie_id' => $questionnarieId,
                        'user_id'          => $evaluadoId,
                        'user_target_id'   => $evaluadoId,
                        'rol'              => 1,
                        'weight'           => $weight,
                        'real_weight'      => $weight,
                        'state'            => 0,
                        'start'            => $start->toDateTimeString(),
                        'deadline'         => $deadline->toDateTimeString(),
                    ];
                }
            }

            // ── Líderes (rol = 1) ─────────────────────────────────────────
            foreach ($eval['lideres'] ?? [] as $lider) {
                $userId = $lider['usuario_id'];
                if (isset($existingRecords[$userId . '_' . $evaluadoId])) {
                    $skipped++;
                    continue;
                }

                $weight = $lider['porcentaje'] ?? 0;
                $rows[] = [
                    'group_id'         => $groupId,
                    'questionnarie_id' => $questionnarieId,
                    'user_id'          => $userId,
                    'user_target_id'   => $evaluadoId,
                    'rol'              => 0,
                    'weight'           => $weight,
                    'real_weight'      => $weight,
                    'state'            => 0,
                    'start'            => $start->toDateTimeString(),
                    'deadline'         => $deadline->toDateTimeString(),
                ];
            }

            // ── Evaluadores (rol = 2) ─────────────────────────────────────
            foreach ($eval['evaluadores'] ?? [] as $evaluador) {
                $userId = $evaluador['usuario_id'];
                if (isset($existingRecords[$userId . '_' . $evaluadoId])) {
                    $skipped++;
                    continue;
                }

                $weight = $evaluador['porcentaje'] ?? 0;
                $rows[] = [
                    'group_id'         => $groupId,
                    'questionnarie_id' => $questionnarieId,
                    'user_id'          => $userId,
                    'user_target_id'   => $evaluadoId,
                    'rol'              => 1,
                    'weight'           => $weight,
                    'real_weight'      => $weight,
                    'state'            => 0,
                    'start'            => $start->toDateTimeString(),
                    'deadline'         => $deadline->toDateTimeString(),
                ];
            }
        }

        DB::transaction(function () use ($rows) {
            // insertOrIgnore es idempotente: no duplica si el endpoint se ejecuta dos veces
            DB::table('user_questionnarie')->insertOrIgnore($rows);
        });

        return response()->json([
            'message'  => 'Evaluaciones procesadas correctamente',
            'inserted' => count($rows),
            'skipped'  => $skipped,
        ], 201);
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
