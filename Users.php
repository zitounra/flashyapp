<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model {
	/**
	* This private function returns the full JSON path
	* @param string $table - name of the table
	* @return string - full path of the JSON file
	*/
	private static function getFullPath($table = "") {
		return $_SERVER["DOCUMENT_ROOT"] . "/@core/storage/json/" . $table . "." . "json";
	}

	/**
	* Create a JSON file for a specific table
	* @param string $table: name of the table
	* @return true if the JSON file was successfully created
	*/
	public static function createFile($table = "") {
		// Full path of the JSON file
		$path = Users::getFullPath($table);
		
		// Stop the operation if the table name is incorrect or if the file already exists
		if (!isset($table) || $table == "" || file_exists($path)) return false;
		
		// Create an initialized empty JSON file, with the name of the table
		return file_put_contents($path, json_encode(array()));
	}
	
	/**
	* Return the full list of users
	* @param string $table - name of the table
	* @return array - parsed list of users
	*/
	public function getAllUsers($table = "") {
		// Full path of the JSON file
		$path = Users::getFullPath($table);

		// Stop the operation if the table name is incorrect or if the file doesn't exist
		if (!isset($table) || $table == "" || !file_exists($path)) return false;

		// Parse and return the JSON file into an array
		$content = json_decode(file_get_contents($path), true);
		return (count($content) > 0) ? $content[0] : array();
	}
	
	/**
	* Add a new user to the JSON file
	* @param string $table - name of the table
	* @param array $data - user data (ID, User Name, First Name and Last Name)
	* @return boolean - true or false if saving failed
	*/
	public static function insertUser($table = "", $data = array()) {
		// Check the data
		if (!isset($data["id"]) || !isset($data["user_name"]) || !isset($data["first_name"]) || !isset($data["last_name"])) return false;
		
		// Get all the users of the table
		$users = Users::getAllUsers($table);

		// Stop the operation if the table doesn't exist, or if the same ID already exists in the table
		if (!is_array($users) || array_key_exists($data["id"], $users)) return false;
		
		// Add the new user
		$users[$data["id"]] = array("user_name" => $data["user_name"], "first_name" => $data["first_name"], "last_name" => $data["last_name"]);

		// Save the new JSON
		return file_put_contents(Users::getFullPath($table), json_encode(array($users)));
	}
	
	/**
	* Update data of an existing user
	* @param string $table - name of the table
	* @param array $data - user data (ID is mandatory + User Name and/or First Name and/or Last Name)
	* @return boolean - true if the user has correctly been updated, false alternatively
	*/
	public static function updateUser($table = "", $data = array()) {	
		// Get all the users of the table
        $users = Users::getAllUsers($table);
		
		// Stop the operation if the user ID is not found
		if (!is_array($users) || !isset($data["id"]) || !array_key_exists($data["id"], $users)) return false;
		
		// Update by overriding the relevant user data
		$fields = array("user_name", "first_name", "last_name");
		foreach ($fields AS $value) {
			if (isset($data[$value])) $users[$data["id"]][$value] = $data[$value];
		}
		
		// Save the new JSON
		return (file_put_contents(Users::getFullPath($table), json_encode(array($users)))) ? true: false;
    }
	
	/**
	* Remove a user from the table
	* @param string $table - name of the table
	* @param array $data - user ID
	* @return boolean - true if the user has correctly been deleted, false alternatively
	*/
	public static function deleteUser($table = "", $data = array("id" => 0)) {
		// Get all the users from the table
        $users = Users::getAllUsers($table);
		
		// Stop the operation if the user ID is not found
		if (!is_array($users) || !isset($data["id"]) || !array_key_exists($data["id"], $users)) return false;
		
		// Delete the user data
		unset($users[$data["id"]]);
		
		// Save the final JSON
		return (file_put_contents(Users::getFullPath($table), json_encode(array($users)))) ? true : false;
    }
		
	/**
	* Get data of a specific user
	* @param string $table - name of the table
	* @param array $data - user ID
	* @return array - data of the specific user (or false)
	*/
    public static function getUserById($table = "", $data = array("id" => 0)) {
		// Get all the users of the table
		$users = Users::getAllUsers($table);
		
		// Stop the operation if the table or the user ID is not found
		if (!is_array($users) || !array_key_exists($data["id"], $users)) return false;
		
		// Return data of the specific user
		return $users[$data["id"]];
    }

	/**
	* Return specific fields for all the users
	* @param string $table - name of the table
	* @param array $fields - list of the required fields
	* @return array $return - requested list of users (or false)
	*/
    public static function getUsersByFields($table = "", $fields = array()) {
		// Check authorized fields
		$authorized_fields = array("id", "user_name", "first_name", "last_name");
		foreach ($fields AS $key => $value) {
			if (!in_array($value, $authorized_fields)) unset($fields[$key]);
		}
		
		// Make sure there is at least one field to display
		if (!count($fields) > 0) return false;
		
		// Get all the users of the table
		$users = Users::getAllUsers($table);
		
		// Prepare the relevant list of users
		$return = array();
		$i = 0;
		foreach ($users AS $key => $value) {
			foreach ($fields AS $field) {
				if ($field != "id") $return[$i][$field] = $value[$field];
				else $return[$i][$field] = $key;
			}
			$i++;
		}
        return $return;
    }
}