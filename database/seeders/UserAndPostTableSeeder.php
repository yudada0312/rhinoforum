<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
class UserAndPostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=1; $i<=10; $i++){
            DB::table('users')->insert([
                'id' => $i,
                'name' => Str::random(10),
                'email' => Str::random(10).'@gmail.com',
                'password' => Hash::make('password'),
            ]);
        }

        $startDate = new Carbon('2021-04-01');
        for($i=1; $i<=20; $i++){
            $category = ['A','B']; 
            DB::table('posts')->insert([
                'user_id' => rand(1,2),
                'content' => ($i%2==0) ? Str::random(10) : Str::random(10).'testPartString'.Str::random(10),
                'category' => ($i%2==0) ? $category[0] : $category[1],
                'published_at' => $startDate->format("Y-m-d H:i:s"),
            ]);
            $startDate->addDay();
        }
    }
}
