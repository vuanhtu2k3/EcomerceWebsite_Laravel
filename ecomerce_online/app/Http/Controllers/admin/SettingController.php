<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function showChangePasswordForm()
    {
        return view('admin.change-password');
    }

    public function processChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password'

        ]);
        $id = Auth::guard('admin')->user()->id;
        $admin = User::where('id', $id)->first();

        if ($validator->passes()) {
            if (!Hash::check($request->old_password, $admin->password)) {
                Session::flash('error', 'Your old password is incorrect');
                return response()->json([
                    'status' => true,
                    'message' => 'Old password is incorrect'
                ]);
            }
            User::where('id', $id)->update([
                'password' => Hash::make($request->new_password)

            ]);
            Session::flash('success', 'Password changed successfully');
            return response()->json([
                'status' => true,
                'message' => 'Password changed successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}
