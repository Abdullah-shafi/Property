<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\User;

class LoginController extends Controller
{
    
    public function index(Request $req){
    	return view('login.index');
    }

    public function verify(Request $req){
 

       $validation = Validator::make($req->all(), [
            'uname'=>'bail|required|exists:customer,username',
            'password'=>'required|exists:customer,password'
            
        ]);

        if($validation->fails()){
            return back()
                    ->with('errors', $validation->errors())
                    ->withInput();

            return redirect()->route('/login')
                            ->with('errors', $validation->errors())
                            ->withInput();
            }

            else {
                 $user = DB::table('customer')
                    ->where('username', $req->uname)
                    ->where('password', $req->password)
                    ->first();
           
              if($user->type=='customer'){
                   $req->session()->put('uname', $req->uname);
                   return redirect()->route('website.r_customer_home');
               }
              
               else{
                   
                   return redirect()->route('login.index');
               }
             }

             


              }

                
            
          



}