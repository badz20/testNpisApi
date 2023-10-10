<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
class updateUserWithUserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::get();

        foreach ($users as $user) {
            switch (true) {
                case ($user->bahagian_id == null && $user->negeri_id == null && $user->daerah_id == null):
                    $user->user_type_id = 5;
                    $user->save();
                    break;
                
                case ($user->bahagian_id == null && $user->negeri_id != null && $user->daerah_id != null):
                    $user->user_type_id = 2;
                    $user->save();
                    break;
                
                case ($user->daerah_id == null && $user->negeri_id != null):
                    $user->user_type_id = 1;
                    $user->save();
                    break;
                
                case ($user->bahagian_id != null):
                    if($user->bahagian_id == 14) {
                        $user->user_type_id = 4;
                        $user->save();
                    }else {
                        $user->user_type_id = 3;
                        $user->save();
                    }
                    break;
                default:
                    $user->user_type_id = 5;
                    $user->save();
                    break;
            }
        }
    }
}
