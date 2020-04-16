<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OauthClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
         public function run()
    {
        DB::table('oauth_clients')->insert([
		    [
		    	'name' => 'Laravel Personal Access Client',
		    	'secret' => 'BqQ1PGA86aunzIfjK0Nnd7QmqwBT3FoOfKkWTVy6' ,
		    	"redirect" => "http://localhost",
		    	"personal_access_client" => 1,
		    	"password_client" => 0 ,
		    	"revoked" => 0
		    ],
		    [
		    	'name' => 'Laravel Password Grant Client',
		    	'secret' => '8Qcz8kgX35Pdg5JD6A5Xd7GvpLpwOPaKI7xOdIGk' ,
		    	"redirect" => "http://localhost",
		    	"personal_access_client" => 0,
		    	"password_client" => 1 ,
		    	"revoked" => 0
		    ]
		]);
    }
    }
}
