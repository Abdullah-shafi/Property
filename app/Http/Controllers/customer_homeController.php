<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\HomeRequest;
use DB;


class customer_homeController extends Controller
{
  //home
   public function index(Request $req){
       
         

         $propertys=DB::table('property')
                       ->join('property_picture', 'property.property_id', '=', 'property_picture.property_id')
                       ->where('status', 'allowed')
                       ->orderby('date', 'desc')
                       ->paginate(3);

      return view('website.index')
                ->with('propertys',$propertys);

      
    }

    public function about(){

   	    $results = DB::table('property')
   	               //->pluck('date')
   	               ->select(DB::raw('count(*) as number, date'))
   	               ->groupBy('date')
   	                ->orderByRaw('date DESC')
   	                ->limit(7)
                   ->get();
         
       
//print_r($results);

    	//return view('website.about-us',['results'=>$results]);
       return view('website.ab',['results'=>$results]);
    	//return view('website.about-us');
    }
}
