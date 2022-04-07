<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Users;

class UsersController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->all();
        // validation
        $this->validate($request, [ 
            'name'          => 'required',
            'phone_number'  => 'required',
            'email'         => 'required', 
            'password'      => 'required',
            'country_code' => 'required'
        ]);

        // cek email unique

        if(isset($request->password) && $request->password!=""){
            $data['password'] = md5($request->password);

            $users = Users::create($data);

            // return response()->json($users);
            if($users){
                return response()->json(['status'=>'success','message'=>'Register user successfull','code'=>200]);
            }else{
                return response()->json(['status'=>'failed','message'=>'Register user failed','code'=>400]);
            }
        }else{
            return response()->json(['status'=>'failed','message'=>'Register user failed, please change password','code'=>400]);
        }
    }
} 