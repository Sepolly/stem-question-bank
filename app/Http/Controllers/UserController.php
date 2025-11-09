<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
    ) {}

    public function index()
    {
        return Inertia::render('users/index', [
            'users' => UserResource::collection($this->userService->getAllUsers()),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->addUser($request->validated());

        return back()->with('success', 'user added.');
    }

    public function update(int $eventId, UpdateUserRequest $request, User $user)
    {
        $this->userService->updateUser($request->validated(), $user);

        return back()->with('success', 'user updated.');
    }

    public function destroy(int $eventId, User $user)
    {
        $this->userService->deleteUser($user);

        return back()->with('success', 'user deleted.');
    }
}
