<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getAll()
    {
        return User::all();
    }

    public function findById($id)
    {
        return User::findOrFail($id);
    }

    public function findByEmail($email)
    {
        return User::where('email',$email)->first();
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update($id,array $data)
    {
        return User::where('id',$id)->update($data);
    }

    public function delete($id)
    {
        return User::destroy($id);
    }
}
