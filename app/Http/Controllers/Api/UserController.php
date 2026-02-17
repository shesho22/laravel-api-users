<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\DTOs\UserDTO;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

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

    public function store(StoreUserRequest $request)
    {
        $dto = new UserDTO($request->validated());

        return response()->json(
            $this->service->create($dto),
            201
        );
    }


    public function show($id)
    {
        return response()->json(
            $this->service->getById($id)
        );
    }

    public function update(UpdateUserRequest $request, $id)
    {
        return response()->json(
            $this->service->update($id, $request->validated())
        );
    }


    public function destroy($id)
    {
        return response()->json([
            'deleted'=>$this->service->delete($id)
        ]);
    }
}
