<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\HomeRequest;
use Validator;
use DB;

class r_customer_homeController extends Controller
{


//home
   public function index(Request $req){
   	   
$pro = DB::table('property')
                       ->join('property_picture', 'property.property_id', '=', 'property_picture.property_id')

                       ->where('status', 'allowed')
                       ->orderby('no_of_clicks', 'desc')
                        ->get();

   	     $propertys = DB::table('property')
   	                   ->join('property_picture', 'property.property_id', '=', 'property_picture.property_id')

   	                   ->where('status', 'allowed')
   	                   ->orderby('no_of_clicks', 'desc')
   	                    ->paginate(3);



    	  return view('website.r_customer_home')
                ->with('propertys',$propertys)
                ->with('pro',$pro);
    }



//details
   public function details($id){

DB::Table('property')->whereproperty_id($id)->Increment('no_of_clicks');
                      

        $property = DB::table('property')
                   ->join('property_picture', 'property.property_id', '=', 'property_picture.property_id')
                    ->join('customer', 'property.username', '=', 'customer.username')

                   ->where ('property.property_id',$id)
                   ->get();
         
                   
       return view('website.customer_property_details',['propertys'=>$property]);
    }


//AllProperty
   public function customer_all_property(){
             $pro = DB::table('property')
                       ->join('property_picture', 'property.property_id', '=', 'property_picture.property_id')

                       ->where('status', 'allowed')
                       ->orderby('date', 'desc')
                        ->get();
   	      $propertys=DB::table('property')
                       ->join('property_picture', 'property.property_id', '=', 'property_picture.property_id')
                       ->where('status', 'allowed')
                       ->orderby('date', 'desc')
                       ->paginate(3);

      return view('website.customer_all_property')
                ->with('propertys',$propertys)
                ->with('pro',$pro);

    	
    }

//customer_contact
public function customer_contact(Request $req){
  $name=$req->session()->get('uname');

   $image = DB::table('customer')
                ->where('username',$name)
                ->get();
   $user = DB::table('message')
                ->whereIn('message.from', [$name,'admin'])
                ->whereIn('message.to', [$name,'admin'])
                ->orderby('message_id')
                ->get();
    	return view('website.customer_contact')
                  ->with('users',$user)
                  ->with('images',$image);
                  
    }
//customer_contact_save

public function customer_contact_save(Request $req){
      
       $validation = Validator::make($req->all(), [
            'message'=>'required'           
        ]);
            
       if($validation->fails()){
            return back()
                    ->with('errors', $validation->errors())
                    ->withInput();

            return redirect()->route('website.customer_contact')
                            ->with('errors', $validation->errors())
                            ->withInput();
            }

              else
             {
              $name=$req->session()->get('uname');
            DB::table('message')->insert(
             ['from' =>$name,'to'=>'admin','msg'=>$req->message ]
            );
           return redirect()->route('website.customer_contact');  
           }

    }


//search

   public function searcharea(Request $req){

       $term=$req->term;

      $iteams=DB::table('property')
                       ->join('property_picture', 'property.property_id', '=', 'property_picture.property_id')
                       ->where('status', 'allowed')
                       ->orwhere('property_area','like','%'.$term.'%')
                       ->get();

       foreach ($iteams as $key => $value) {
               $searchResult[]=$value->property_area;
            }

        return $searchResult;       
    }





 //profile
   public function customer_about_me(Request $req){
     $name=$req->session()->get('uname');
   	    $user = DB::table('customer')
   	               ->where ('username',$name)
                   ->get();

    	 return view('website.customer_about_me',['users'=>$user]);
    }
   
  //customer_delete_profile
      public function customer_delete_profile(Request $req){
      	$name=$req->session()->get('uname');
   	    $user = DB::table('customer')
   	               ->where ('username',$name)
                   ->get();

    	 return view('website.customer_delete_profile',['users'=>$user]);
    } 

 // change_password
     public function customer_change_password(){
      return view('website.customer_change_password');
       }

        // change_password_save
     public function customer_change_password_save(Request $req){
          
        $validation = Validator::make($req->all(), [
            'password'         => 'required',
            'confirm_password' => 'required|same:password'       
        ]);
            
       if($validation->fails()){
            return back()
                    ->with('errors', $validation->errors())
                    ->withInput();

            return redirect()->route('website.customer_change_password')
                            ->with('errors', $validation->errors())
                            ->withInput();
            }

              else
             {
               $req->session()->flash('msg', '* Your Password has been Changed Successfully');
               $name=$req->session()->get('uname');
               DB::table('customer')
                    ->where('username',$name)
                    ->update( ['password' =>$req->password]);


          
           return redirect()->route('website.customer_change_password');  


           }
       }



  //customer_edit_profile
     public function customer_edit_profile(Request $req){
   	   $name=$req->session()->get('uname');
   	    $user = DB::table('customer')
   	               ->where ('username',$name)
                   ->get();
    	 return view('website.customer_edit_profile',['users'=>$user]);
    }



//customer_edit_profile_save


public function customer_edit_profile_save(Request $req){
      
       $validation = Validator::make($req->all(), [
            'name'=>'required',   
             'email'=>'bail|required|email',
               'phone' => 'bail|required|size:11'       
        ]);
            
       if($validation->fails()){
            return back()
                    ->with('errors', $validation->errors())
                    ->withInput();

            return redirect()->route('website.customer_edit_profile')
                            ->with('errors', $validation->errors())
                            ->withInput();
            }

              else
             {
               $name=$req->session()->get('uname');
               DB::table('customer')
                    ->where('username',$name)
                    ->update( ['name' =>$req->name,'email'=>$req->email,'phone'=>$req->phone ]);


          
           return redirect()->route('website.customer_about_me');  


           }

    }






  //about
 public function r_about(){

        $results = DB::table('property')
                   //->pluck('date')
                   ->select(DB::raw('count(*) as number, date'))
                   ->groupBy('date')
                    ->orderByRaw('date DESC')
                    ->limit(7)
                   ->get();
         
       
//print_r($results);

      //return view('website.about-us',['results'=>$results]);
       return view('website.about',['results'=>$results]);
      //return view('website.about-us');
    }

//customer_edit_property
   public function customer_edit_property(Request $req){
       
      
       $name=$req->session()->get('uname');

         $propertys = DB::table('property')
                       ->join('property_picture', 'property.property_id', '=', 'property_picture.property_id')
                       ->where('status', '!=' ,'deleted')
                       ->where('username',$name)
                        ->paginate(3);

         return view('website.r_customer_edit_property ',['propertys'=>$propertys]);
      
     
    }

    //customer_edit_property_details
   public function customer_edit_property_details($id){

                      

        $property = DB::table('property')
                   ->join('property_picture', 'property.property_id', '=', 'property_picture.property_id')
                    ->join('customer', 'property.username', '=', 'customer.username')

                   ->where ('property.property_id',$id)
                   ->get();
         
                   
       return view('website.customer_edit_property_details',['propertys'=>$property]);
    }

 //customer_edit_property_details_save
   public function customer_edit_property_details_save($id,Request $req){

      $validation = Validator::make($req->all(), [
            'title'=>'required',   
            'property_price'=>'required',
            'property_area'=>'required',
            'style'=>'required',
            'bed'=>'required',
            'bath'=>'required',
            'feet'=>'required',
            'floor'=>'required',
            'description'=>'required'
                 
        ]);
            
       if($validation->fails()){
            return back()
                    ->with('errors', $validation->errors())
                    ->withInput();

            return redirect()->route('website.customer_edit_property_details')
                            ->with('errors', $validation->errors())
                            ->withInput();
            }

             else
             {
              
               DB::table('property')
                    ->where('property_id',$id)
                    ->update( ['title' =>$req->title,'property_price'=>$req->property_price,'style'=>$req->style,'bed' =>$req->bed,'bath' =>$req->bath,'feet' =>$req->feet,'floor' =>$req->floor,'description' =>$req->description]);


          
           return redirect()->route('website.r_customer_edit_property');  


           }

 }

 //customer_edit_property
   public function customer_delete_property(Request $req){
       
      
       $name=$req->session()->get('uname');

         $propertys = DB::table('property')
                       ->join('property_picture', 'property.property_id', '=', 'property_picture.property_id')
                       ->where('status', '!=' ,'deleted')
                       ->where('username',$name)
                        ->paginate(3);

         return view('website.r_customer_delete_property ',['propertys'=>$propertys]);
      
     
    }           
 //customer_delete_property_save
   public function customer_delete_property_save(Request $req,$id){
$name=$req->session()->get('uname');
            DB::Table('customer')->whereusername($name)->Decrement('total_posts',1);  
              DB::Table('customer')->whereusername($name)->Increment('sold_posts'); 
                DB::Table('customer')->whereusername($name)->Decrement('active_posts');   

                   DB::table('property')
                    ->where('property_id',$id)
                    ->update( ['status' =>'deleted']);
                    
          
           return redirect()->route('website.r_customer_delete_property');  
    }
}
