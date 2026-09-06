<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\DB;
use App\Models\APIClient;

class APIClientLogin implements UserProvider
{

	/**
	 * The active database connection.
	 *
	 * @var \Illuminate\Database\ConnectionInterface
	 */
	protected $conn;

	/**
	 * The hasher implementation.
	 *
	 * @var \Illuminate\Contracts\Hashing\Hasher
	 */
	protected $hasher;

	/**
	 * The table containing the users.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * Create a new SCL user provider.
	 *
	 * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
	 * @param  string  $table
	 * @return void
	 */
	public function __construct(HasherContract $hasher, $table)
	{
		$this->table = $table;
		$this->hasher = $hasher;
	}

	/**
	 * Retrieve a user by their unique identifier.
	 *
	 * @param  mixed  $identifier
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveById($identifier)
	{

		$query = DB::table($this->table.' as u')
					->where([['u.id', '=', $identifier]])
					->where([['u.status', '=', 'ACTIVE']])
					->select('u.id','u.category','u.username','u.password','u.shortName','u.name','u.channel',
									'u.mobileNumber','u.email','u.service_provider_id','u.payments_provider_id')
					->get();

		if(\count($query)>0){
			$user = new APIClient;
			$query = $query->first();

			$user->payments_provider_id = $query->payments_provider_id;
			$user->service_provider_id  = $query->service_provider_id;
			$user->category = $query->category;

			$user->mobileNumber = $query->mobileNumber;
			$user->shortName = $query->shortName;
			$user->password  = $query->password;
			$user->username = $query->username;
			$user->email = $query->email;
			$user->name = $query->name;
			$user->id = $query->id;
		}else{
			$user = null;
		}
		return $user;

	}

	/**
	 * Retrieve a user by their unique identifier and "remember me" token.
	 *
	 * @param  mixed  $identifier
	 * @param  string  $token
	 */
	public function retrieveByToken($identifier, $token)
	{
		//Not Used
	}

	/**
	 * Update the "remember me" token for the given user in storage.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function updateRememberToken(UserContract $user, $token)
	{
		//Not Used
	}

	/**
	 * Retrieve a user by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveByCredentials(array $credentials)
	{

		if (empty($credentials) ||
			(count($credentials) === 1 &&
			array_key_exists('password', $credentials))) {
			return;
		}

		$query=DB::table($this->table.' as u')
					->where([['u.username', '=', $credentials['username']]])
					->where([['u.status', '=', 'ACTIVE']])
					->select('u.id','u.category','u.username','u.password','u.shortName','u.name','u.channel',
									'u.mobileNumber','u.email','u.service_provider_id','u.payments_provider_id')
					->get();

		if(\count($query)>0){
			$user = new APIClient;
			$query = $query->first();

			$user->payments_provider_id = $query->payments_provider_id;
			$user->service_provider_id  = $query->service_provider_id;
			$user->category = $query->category;

			$user->mobileNumber = $query->mobileNumber;
			$user->shortName = $query->shortName;
			$user->password  = $query->password;
			$user->username = $query->username;
			$user->email = $query->email;
			$user->name = $query->name;
			$user->id = $query->id;

		}else{
			$user = null;
		}
		return $user;

	}


	/**
	 * Validate a user against the given credentials.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
	 * @param  array  $credentials
	 * @return bool
	 */
	public function validateCredentials(UserContract $user, array $credentials)
	{
		return $this->hasher->check(
			$credentials['password'], $user->getAuthPassword()
		);
	}

}
