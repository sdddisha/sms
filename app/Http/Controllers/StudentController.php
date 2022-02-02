<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;  
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon; 
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Upload;
class StudentController extends Controller
{
  
    public function index(){

        return view('student.login');
    }
    public function check_login(Request $request)
    {
       

         $request->validate([
             'email' => 'required|string|email',
             'password' => 'required',
            
         ]);
        
       $credentials = $request->only('email', 'password');

       if (Auth::attempt($credentials)) {
        {
            $checkAuth = Auth::user()->role;
            if ($checkAuth == 2){
                return redirect()->intended('student/dashboard');
            }
        }
        
    }
         
        return redirect('/')->with('error', 'Oppes! You have entered invalid credentials');
    }
    public function dashboard(){

        $data=Auth::user(); //this returns all data of authorised student
        $data2= $data['cid'];
        $data3= $data['email']; //returns cid of authorised student
       
        $data1 = DB::table('users')
                ->where('role', '=', 1)
                ->where('cid', '=',$data2)
                ->count();

        $detail1=  DB::table('courses as cd')

                ->select('cd.*','ud.*')
                ->leftjoin('users as ud', 'cd.cid','=','ud.cid')
                ->where('email',$data3)
                ->where('ud.cid',$data2)
                ->get();

                //dd($detail1);

                $detail=$detail1[0]->cname;

            $announ=DB::table('announcements')->where('cid', '=',  $data2)
            ->get();   
            
            
              //dd($announ)  ;
        return view('student.dashboard',compact('data1','data2','detail','announ','data'));
    }
    public function logout(Request $request) {
        Auth::logout();
        return redirect('/');
      }

      public function show_assign(){
        $data=Auth::user(); //this returns all data of authorised student
        $data2= $data['cid'];
        $data3= $data['email'];
        
        //$assign=Assignment::all();

        //dd($assign);
        $assign=  DB::table('assignments as as')

        ->select('as.*')
        ->leftjoin('users as ud', 'as.cid','=','ud.cid')
        ->where('email',$data3)
        ->get();
       
        return view('student.assignment',compact('assign'));
    }
    
        public function upload_assign( Request $request,$id)
    {    $data=Auth::user();
        //dd($data);
       
        //return view('student.upload',compact('id','data'));
        return view('student.upload',compact('id','data'));
    }

    public function store_assign(Request $request)
    {
        $check_id = Auth::user()->id;
       // dd($request->get('aid'));
       
      // dd $check_cid);
     
       //if($check_cid>0)
       if(DB::table('uploads')
        ->where('sid', '=', $check_id)
        ->where('aid', '=', $request->get('aid'))
        ->count() >0)
       {
        return back()->with('success',' Assignment cant be uploaded twice !');
       }

       else{

      
        // ensure the request has a file before we attempt anything else.
        if ($request->hasFile('file')) {
            


            $request->validate([
                'image' => 'mimes:jpeg,bmp,png,jpg,pdf,txt.xlx' // Only allow .jpg, .bmp and .png file types.
            ]);
           
            // Save the file locally in the storage/public/ folder under a new folder named /product
            $request->file->store('assignment', 'public');

            // Store the record, using the new file hashname which will be it's new filename identity.
           
            $assign = new Upload([
                "cid" => $request->get('cid'),
                "aid" => $request->get('aid'),
                "sid" => $request->get('sid'),
                "file_path" => $request->file->hashName()
            ]);
            
            $assign->save(); // Finally, save the record.
        }
    
        return back()->with('success',' Assignment uploaded !');
    }
    }
    public function forget(Request $request){

        return view('student.forget-password');
            
    }
    public function submitForgetPasswordForm(Request $request)
    {
       //dd($request->all());

       $request->validate([
        'email' => 'required|email|exists:users',
       
    ]);

        $token = Str::random(64);
     //dd($token);
        DB::table('password_resets')->insert([
            'email' => $request->email, 
            'token' => $token, 
            'created_at' => Carbon::now()
          ]);
          
        $mail=Mail::send('student.email.forget-password', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        if(count(Mail::failures()) > 0){
            $errors = 'Failed to send password reset email, please try again.';
            ///dd($errors);
        }
        

        return back()->with('message', 'We have e-mailed your password reset link!');
    }
    
    public function showResetPasswordForm($token) { 
       return view('student.forgetPasswordLink', ['token' => $token]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function submitResetPasswordForm(Request $request)
    {
        
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:4',
           
        ]);
       

        $updatePassword = DB::table('password_resets')
                            ->where([
                              'email' => $request->email, 
                              'token' => $request->token
                            ])
                            ->first();
              
        if(!$updatePassword){
            return back()->withInput()->with('error', 'Invalid token!');
        }
        
        $user = User::where('email', $request->email)
                    ->update(['password' => Hash::make($request->password)]);
               

        DB::table('password_resets')->where(['email'=> $request->email])->delete();
        
        return redirect('/')->with('error', 'Your password has been changed!');
       
    }

    public function assign_status(){
        $data=Auth::user()->id;
        
        $status=Upload::select('*')->where('sid',$data)->get();
       
       // dd($status);

        return view('student/status',compact('status'));

    }
    public function show_announ(){
    $data=Auth::user(); //this returns all data of authorised student
    $data2= $data['cid'];
    $announ=DB::table('announcements')->where('cid', '=',  $data2)
    ->get();   
    
        return view('student.announcement',compact('announ'));
    }

}

