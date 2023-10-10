<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->seedRoles();
        // $this->seedPermissions();
        Schema::disableForeignKeyConstraints();
        DB::unprepared('SET IDENTITY_INSERT permissions ON');
        //DB::table('users')->truncate();
        $json = file_get_contents(storage_path('/data/permissions.json'));
        $json_data = json_decode($json,true);
        
        foreach ($json_data['permissions'] as $datum) {
            \App\Models\Permission::create($datum);
        }
        DB::unprepared('SET IDENTITY_INSERT permissions OFF');
        Schema::enableForeignKeyConstraints();
    }

    // private function seedRoles()
    // {
    //     $roles = collect(\App\Models\Role::DEFAULT_ROLES)->transform(function ($role) {
    //         return ['name' => $role, 'guard_name' => 'web'];
    //     });

    //     Role::insert($roles->all());
    // }

    // private function seedPermissions()
    // {
    //     $permissions = collect(\App\Models\Permission::DEFAULT_PERMISSIONS);
    //     Role::query()->each(function ($role) use ($permissions) {
    //         $permissions->each(function ($permission) use ($role) {
    //             $permission = \Spatie\Permission\Models\Permission::updateOrCreate([
    //                 'name' => $permission.' '.$role->name,
    //                 'guard_name' => $role->guard_name,
    //             ]);

    //             if ($role && ! $role->hasPermissionTo($permission)) {
    //                 $role->givePermissionTo($permission);
    //             }
    //         });
    //     });
    // }
}
