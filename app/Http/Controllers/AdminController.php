<?php

namespace App\Http\Controllers;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;  
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Announcement;
use App\Models\Upload;
use App\Models\User;

class AdminController extends Controller
{
    
    function dashboard(Request $request){
        $teacher= User::where('role', 1 )->count();
        $student= User::where('role', 2)->count();
        $course=Course::count();
      
        return view('admin.dashboard',compact('teacher','student','course'));
    }

    public function login(){

        return view('admin.login');
    }

    public function check_login(Request $request){
         $request->validate([
         'username'=>'required',
         'password'=>'required'
         ]);
     
         $admin=Admin::where(['username'=>$request->username,'password'=>sha1($request->password)])
         ->count();
         if($admin>0){
             $adminData=Admin::where(['username'=>$request->username,'password'=>sha1($request->password)])
             ->get();

         session(['adminData'=>$adminData]);
             return redirect('admin/dashboard');
     }
     else{
         return redirect('admin/login')->with('msg','Wrong Credentials');
     }
     }
     
    public function logout(Request $request){

        session()->forget(['adminData']);
        return redirect('admin/login');
    }   
    public function course(){
        
        return view('admin.course');
    }
    public function add_course(Request $request){
        Validator::make($request->all(), [
            'cname' => 'required|min:1|max:35',
            'cid' =>'required'
        ])->validate();
    
        $dataArray      =       array(
        "cname"          =>          $request->cname,
        "cid"            =>          $request->cid, 
        );
      Course::create($dataArray);
    return back()->with('success','Courses successfully registered!');
    } 
    
    
public function user(){
    $course =DB::table('courses')->select('cname','cid')->get();
    return view('admin.users',compact('course'));
}
public function store_userdata(Request $request)
{
       // dd($request->all());
Validator::make($request->all(), [
    'fname' => 'required|min:3|max:35',
    'lname' =>'required|min:3|max:35',
    'password'=> 'required|min:3',
    'email'=>'required|email',
    'role'=> 'required', 
    'cid'=> 'required',
])->validate();

$dataArray      =       array(
"fname"          =>          $request->fname,
"lname"          =>          $request->lname,
"email"          =>          $request->email,
'password'       =>          Hash::make($request->password),
"role"          =>          $request->role,
"cid"          =>          $request->cid
);


$users= User::create($dataArray);


return back()->with('success','Successfully registered!');

}
public function teacher(Request $request){
    //$data=User::all()->toArray();
        // $data=User::all()->toJson();
        // $data=json_decode($data);
        // var_dump($data);
  
    $teacher= User::where('role', 1 )->get();

    return view('admin.teacher',compact('teacher'));

}
public function teacher_edit($id)
{
    
    $prodID=base64_decode($id);
    return view('admin.teacher_edit')->with('teacher',User::find($prodID ));
}


public function teacher_update(Request $request,$id)
{
    Validator::make($request->all(), [
        'fname' => 'required',
        'lname' =>'required'    
         
    ])->validate();

    $res= User::find($request->id);
    //dd($request->id);
    $res->fname=$request->input('fname');
    $res->lname=$request->input('lname');
    $res->email=$request->input('email');
    $res->cid=$request->input('cid');
    $res->save();

    $request->session()->flash('success','Data updated');
    return redirect('admin/dashboard');
}

public function destroy(user $user,$id)
{
    //
   User::destroy(array('id',$id));
  
    return back()->with('msg','Deleted');
}
public function manage_course(Request $request){
    $course= Course::all();
    return view('admin.manage_course',compact('course'));

}
public function course_edit($id)
{
    
    $prodID=base64_decode($id);
    return view('admin.course_edit')->with('course',Course::find($prodID ));
}


public function course_update(Request $request,$id)
{
    Validator::make($request->all(), [
        'cname' => 'required',
        'cid' =>'required'    
         
    ])->validate();

    $res= Course::find($request->id);
    //dd($request->id);
    $res->cname=$request->input('cname');
    $res->cid=$request->input('cid');
    $res->save();

    $request->session()->flash('success','Data updated');
    return redirect('admin/dashboard');
}

public function course_destroy(user $user,$id)
{
    //
  Course::destroy(array('id',$id));
    return back()->with('msg','Deleted');;
}
public function manage_announ(Request $request){
    $announ= Announcement::all();
    return view('admin.manage_announ',compact('announ'));

}
public function announ_edit($id)
{
    
    $prodID=base64_decode($id);
    return view('admin.announ_edit')->with('announ',Announcement::find($prodID ));
}


public function announ_update(Request $request,$id)
{
  

    $res= Announcement::find($request->id);
    //dd($request->id);
    $res->aname=$request->input('aname');
    $res->atext=$request->input('atext');
    $res->cid=$request->input('cid');
    $res->save();

    $request->session()->flash('success','Announcement updated');
    return redirect('admin/dashboard');
}

public function announ_destroy(user $user,$id)
{
    //
   Announcement::destroy(array('id',$id));
   return back()->with('msg','Announcement Deleted successfully');;

}
public function see_assign(Request $request){

   $assign=Upload::all();

    return view('admin.see_assign',compact('assign'));

}
public function add_announ(){
    $course =DB::table('courses')->select('cname','cid')->get();

    return view('admin.add_announ',compact('course'));

}
public function post_announcement(Request $request){
    Validator::make($request->all(), [
        'aname' => 'required|min:1|max:35',
        'atext' => 'required|min:5|max:200',
        'cid' =>'required'
    ])->validate();

    $dataArray      =       array(
    "aname"          =>          $request->aname,
    "cid"            =>          $request->cid,
    "atext"            =>          $request->atext, 
    );
    Announcement::create($dataArray);
    return back()->with('success',' Announcement made!');
}
}