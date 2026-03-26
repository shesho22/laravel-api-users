<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\DTOs\UserDTO;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    // =========================
    // Crear usuario (LOG)
    // =========================
    public function create(UserDTO $dto)
    {
        $user = $this->repository->create([
            'name'     => $dto->name,
            'email'    => $dto->email,
            'password' => Hash::make($dto->pass),
            'role'     => $dto->role
        ]);

        // ✅ LOG: creación
        Log::info('Usuario creado', [
            'admin_id'     => auth()->id(),
            'created_id'   => $user->id,
            'email'        => $user->email,
            'role'         => $user->role,
            'ip'           => request()->ip()
        ]);

        return $user;
    }

    // =========================
    // Listar usuarios
    // =========================
    public function getAll()
    {
        return $this->repository->getAll();
    }

    // =========================
    // Obtener usuario
    // =========================
    public function getById($id)
    {
        return $this->repository->findById($id);
    }

    // =========================
    // Actualizar usuario (LOG)
    // =========================
    public function update($id, array $data)
    {
        // Evitar cambio de rol propio
        if (auth()->id() == $id && isset($data['role'])) {
            unset($data['role']);
        }

        // Encriptar password
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $updated = $this->repository->update($id, $data);

        // ✅ LOG: actualización
        Log::info('Usuario actualizado', [
            'admin_id'   => auth()->id(),
            'updated_id' => $id,
            'fields'     => array_keys($data),
            'ip'         => request()->ip()
        ]);

        return $updated;
    }

    // =========================
    // Eliminar usuario (LOG)
    // =========================
    public function delete($id)
    {
        if (auth()->id() == $id) {
            throw new \Exception('No puedes eliminarte a ti mismo');
        }

        $deleted = $this->repository->delete($id);

        // ✅ LOG: eliminación (WARNING)
        Log::warning('Usuario eliminado', [
            'admin_id'   => auth()->id(),
            'deleted_id' => $id,
            'ip'         => request()->ip()
        ]);

        return $deleted;
    }
}
