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
use App\Mail\ForgotPassword;



class AuthController extends BaseController
{
    public function employeeRegistration(Request $request): JsonResponse
    {
        $creator = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email',
            'employee_id' => 'nullable|string|unique:users,employee_id',
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

        $logs = [
            'user_id' => $creator->id,
            'action' => "{$creator->name} Create {$user->name} Account"

        ];

        $this->insertSystemLogs($logs);

        return $this->sendResponse($success, 'User Created Successfully');
    }

    public function clientRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'department' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validator Error', $validator->errors());
        }

        $role = 'client';

        $input = $request->all();
        $input['role'] = $role;
        $input['required_change'] = false;
        $input['password_changed_date'] = null;
        $input['is_activated'] = false;
        $input['account_status'] = 'archived';
        $input['employee_id'] = '';
        $input['position'] = '';

        $user = User::create($input);

        $success['name'] = $user->name;

        $logs = [
            'user_id' => $user->id,
            'action' => "{$user->name} Create a New Account"

        ];

        $this->insertSystemLogs($logs);

        return $this->sendResponse($success, 'User Created Successfully');

    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = Auth::user();

            if (!$user->account_status === "archived") {
                return $this->sendError('Not Authorized', ['error' => 'Your Account is Archived, Please contact administrator'], 403);
            }

            $success['token'] = $user->createToken("Authenticated")->plainTextToken;
            $success['name'] = $user->name;
            $success['required_change_password'] = $user->required_change;
            $success['dept_id'] = $user->dept_id;
            $success['role'] = $user->role;

            $logs = [
                'user_id' => $user->id,
                'action' => "{$user->name}  Logged In"

            ];

            $this->insertSystemLogs($logs);

            return $this->sendResponse($success, 'User Login Successfully.');

        } else {
            return $this->sendError('Not Authorized', ['error' => 'Invalid Credentials.'], 403);
        }

    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validator Error', $validator->error());
        }

        $user = User::where('email', $request->email)->first();

        $temporary_password = Str::random(8);

        $user->password = bcrypt($temporary_password);
        $user->required_change = 1;
        $user->save();

        Mail::to($user->email)->send(new ForgotPassword($user->name, $temporary_password));

        $logs = [
            'user_id' => $user->id,
            'action' => "{$user->name} Request new password"

        ];

        $this->insertSystemLogs($logs);

        return $this->sendResponse([], 'Temporary Password sent to your email');

    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return $this->sendResponse(null, "User logged out successfully");
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'temp_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        if (!Hash::check($request->input('temp_password'), $user->password)) {
            return $this->sendError('Invalid temporary password', ['error' => 'Temporary password is incorrect.']);
        }

        if (Hash::check($request->input('new_password'), $user->password)) {
            return $this->sendError('Detected Reusing of Password', ['error' => 'New password must be different from the old password']);
        }

        $user->password = bcrypt($request->input('new_password'));
        $user->required_change = false;
        $user->password_changed_date = now();
        $user->save();

        $logs = [
            'user_id' => $user->id,
            'action' => "{$user->name} Change their password"

        ];

        $this->insertSystemLogs($logs);
        return $this->sendResponse(null, 'Password changed successfully.');
    }

}
