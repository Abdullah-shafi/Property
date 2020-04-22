<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\User;

class RegistrationController  extends Controller
{
    
    public function index(Request $req){
    	return view('registation.index');
    }

                
            
          



}