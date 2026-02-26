<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\api\BaseController;
use App\Mail\SendTemporaryPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;



class AuthController extends BaseController
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email',
            'dept_id' => 'nullable|string|exists:departments,id',
            'position' => 'required|string|max:255',
            'suffix' => 'nullable|string',
            'role' => "required|string",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input = $request->all();
        $temporary_password = Str::random(8);
        $input['password'] = $temporary_password;
        $input['required_change'] = true;
        $input['password_changed_date'] = null;
        $input['is_activated'] = false;

        $user = User::create($input);
        Mail::to($user->email)->send(new SendTemporaryPassword($user->name, $temporary_password));
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User Created Successfully');
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if ($user->account_status === 'archived') {
                return $this->sendError('Not Authorized', ['error' => 'Your Account is Archived, Please contact administrator'], 403);
            }

            $success['token'] = $user->createToken("Authenticated")->plainTextToken;
            $success['name'] = $user->name;
            $success['required_change_password'] = $user->required_change;
            $success['dept_id'] = $user->dept_id;
            $success['role'] = $user->role;
            return $this->sendResponse($success, 'User Login Successfully.');

        } else {
            return $this->sendError('Not Authorized', ['error' => 'Invalid Credentials.'], 403);
        }

    }

}
