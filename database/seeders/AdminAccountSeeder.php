<?php
namespace Database\Seeders;

use App\Laravel\Models\User;
use Illuminate\Database\Seeder;

class AdminAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::find(1);

        if(!$user){
            $user = new User;
        }

        $user->setConnection(env('WRITER_DB_CONNECTION'));
        $user->type = "super_user";
        $user->firstname = "Super User";
        $user->email = "admin@gmail.com";
        $user->username = "master_admin";
        $user->password = bcrypt("admin");
        $user->save();
    }
}
