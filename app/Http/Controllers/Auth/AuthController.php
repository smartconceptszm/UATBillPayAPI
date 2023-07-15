<?php

namespace App\Http\Controllers\Auth;

// use Illuminate\Support\Facades\Event;
// use App\Events\UserHasLoggedInEvent;
use App\Http\Controllers\Contracts\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use  App\Models\UserGroup;
use  App\Models\User;


class AuthController extends Controller
{

    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */

    public function register(Request $request, User $user, UserGroup $user_group)
    {

        try {
            //validate incoming request 
            $this->validate($request, [
                'username' => 'required|string|unique:users',
                'fullnames' => 'required|string',
                'mobileNumber' => 'required|string|size:12|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|string',
            ]);
            $logedInUser = Auth::user();

            DB::transaction(function () use ($request,$user,$user_group,$logedInUser){
                $user->password = app('hash')->make($request->input('password'));
                $user->fullnames = $request->input('fullnames');
                $user->mobileNumber = $request->input('mobileNumber');
                $user->username = $request->input('username');
                $user->email = $request->input('email');
                if ($request->input('status')!='') {
                    $user->status = $request->input('status');
                }
                $user->client_id = $logedInUser->client_id;
                $user->save();
                if ($request->input('group_id')!='') {
                    $user_group->user_id = $user->id;
                    $user_group->group_id = $request->input('group_id');
                    $user_group->save();
                }
            });

            //return successful response
            return response()->json([
                    'status'=>[
                            'code' => 201,
                            'message' => 'CREATED'
                    ],
                    'data'=>$user
                ], 201);

        } catch (\Exception $e) {
            //return error message

            return response()->json([
                    'status'=>[
                        'code' => 409,
                        'message' => $e->getMessage()
                    ],
                    'data'=>$user
                ]);
        }

    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
          //validate incoming request 
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['username', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json([
                                    'status'=>[
                                            'code' => 401,
                                            'message' => 'Unauthorized'
                                    ],
                                    'data'=>[]
                        ], 401);
        }

        $request->request->add(['params' => $credentials]);

        //Event::dispatch(new UserHasLoggedInEvent);

        return $this->respondWithToken($token);
        
    }

    /**
     * Inavlidate the JWT for login user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function logout(Request $request) {
        try {
            Auth::logout();
            return response()->json([
                'status'=>[
                        'code' => 200,
                        'message' => 'OK'
                ],
                'data'=>[]
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status'=>[
                        'code' => 500,
                        'message' => 'Failed to logout, please try again.'
                ],
                'data'=>[]
            ]);
        }
    }

}