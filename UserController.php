<?php

namespace App\Http\Controllers;

use App\Users;

class UserController extends Controller {
	
    public function getList()
    {
		// Create the JSON file
		$table = "my_top_members";
		Users::createFile($table);
		
		// Add 3 users
		$user = array();
		$user[1] = array("user_name" => "raf88", "first_name" => "Rafael", "last_name" => "Mor");
		$user[2] = array("user_name" => "dikla96", "first_name" => "Dikla", "last_name" => "Cohen");
		$user[10] = array("user_name" => "zitoun555", "first_name" => "Raphael", "last_name" => "Zitoun");
		foreach ($user AS $key => $value) Users::insertUser($table, array_merge(array("id" => $key), $value));
		
		// Change first_name from Raphael to Rafi
		Users::updateUser($table, array("id" => 10, "first_name" => "Rafi"));
		
		// Delete the last user
		Users::deleteUser($table, array("id" => array_key_last($user)));
		
		// Get ID and User Name
		echo"<pre>";
		print_r(Users::getUsersByFields($table, array("id", "user_name")));
		echo"</pre>";
    }
}