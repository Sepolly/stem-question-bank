<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class UserService
{

    public function __construct(
        protected RoleService $roleService,
        protected EventService $eventService,
    ){}

    public function getAllUsers()
    {
        try {
            return $this->eventService
                ->getCurrentEvent()
                ->members()
                ->paginate(50);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return [];
        }
    }

    public function addUser(array $data, ?string $role = '')
    {

        try {
            return DB::transaction(function() use($data,$role) {
                $data['password'] = generateUniquePassword();
    
                $user =  User::create(
                    collect($data)->except('role')->toArray()
                );
    
                if($role){
                    $role = $this->roleService->getRoleByName($role);
                    $user->assignRole($role);
                }
    
                if($data['role']){
                    $user->assignRole($data['role']);
                }
    
                $this->eventService->getCurrentEvent()->members()->attach($user);
    
                Password::sendResetLink([
                    'email' => $user->email,
                ]);
    
                return $user;
            },2);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    public function updateUser(array $data, User $user)
    {
        try {
            $user->update(collect($data)->except('role')->toArray());

            if($data['role']){
                $user->syncRoles($data['role']);
            }
            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function changeRole(User $user, string $role){
        try {
            $role = $this->roleService->getRoleByName($role);
            if($user->hasRole($role)){
                return true;
            }
            $user->syncRoles($role);
            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
    
    public function deleteUser(User $user)
    {
        try {
            DB::transaction(function()use($user){
                $user->roles()->detach();
                $user->events()->detach();
                $user->delete();
            });
            return true;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
}
