<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;      
use Carbon\Carbon; 
use Illuminate\Support\Facades\Mail;
//use Mail; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Course;
use App\Models\Upload;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    
    public function index(){

        return view('teacher.login');
    }

    function dashboard(Request $request){
        $data=Auth::user(); //this returns all data of authorised user
        //dd($data);
        $data2= $data['cid']; //returns cid of authorised teacher
      
        $data1 = DB::table('users')
                ->where('role', '=', 2)
                ->where('cid', '=',$data2)
                ->count();
         $assign = DB::table('assignments')
                ->where('cid', '=',$data2)
                ->count();
         $ann = DB::table('announcements')
                ->where('cid', '=',$data2)
                ->count();
        
        return view('teacher.dashboard',compact('data1','data','assign','ann'));
    }

   
    public function check_login(Request $request)
    {
        
         $request->validate([
             'email' => 'required|string|email',
             'password' => 'required|string',
            
         ]);

       $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            {
                $checkAuth = Auth::user()->role;
                if ($checkAuth == 1){
                    return redirect()->intended('teacher/dashboard');
                }
            }
            
        }

        return redirect('/')->with('error', 'Oppes! You have entered invalid credentials');
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/');
      }

    public function view_announcement(){
        $course =DB::table('courses')->select('cname','cid')->get();
        return view('teacher.announcement',compact('course'));
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
       
        $checkAuth = Auth::user()->cid;
        if($checkAuth==$dataArray['cid']){
            Announcement::create($dataArray);
            return back()->with('success',' Announcement made!');
        }
        else{
            return back()->with('success',' you selected wrong course!');
        }
     
    
    } 

    public function upload_assign(){
        $course =DB::table('courses')->select('cname','cid')->get();
      
        return view('teacher.upload_assignment',compact('course'));
    }
    public function store_assign(Request $request)
        {
        
            // Validate the inputs
            $request->validate([
                'cid' => 'required',
                'atitle' => 'required',
                'aid' => 'required',
            ]);
            if(Auth::user()->cid==$request['cid']){

            // ensure the request has a file before we attempt anything else.
            if ($request->hasFile('file')) {
    
                $request->validate([
                    'image' => 'mimes:jpeg,bmp,png,jpg,pdf,txt,xlx' // Only allow .jpg, .bmp and .png file types.
                ]);
    
                // Save the file locally in the storage/public/ folder under a new folder named /product
                $request->file->store('assignment', 'public');
    
                // Store the record, using the new file hashname which will be it's new filename identity.
              
                
                $assign = new Assignment([
                    "cid" => $request->get('cid'),
                    "aid" => $request->get('aid'),
                    "atitle" => $request->get('atitle'),
                    "file_path" => $request->file->hashName()
                ]);
                $assign->save(); // Finally, save the record.
            }
    
            return back()->with('success',' Assignment uploaded !');

            }

              return back()->with('success',' enter correct course id !');
        }

        public function see_assign(Request $request){

            $data=Auth::user(); //this returns all cid of AUTHORISED teacher
            $data2= $data['cid'];
            
           
            
            //$assign=Assignment::all();
    
           
            $assign=  DB::table('uploads as up')
    
            ->select('up.*',)
            ->leftjoin('assignments as as', 'as.aid','=','up.aid')
            ->where('up.cid',$data2)
            ->get();

            //dd($assign);
           
            return view('teacher.see_assignment',compact('assign'));

        }
        public function forget(Request $request){
            //$role=DB::table('users')->where('role', 1)->get();

            return view('teacher.forget-password'   );
        }

        public function submitForgetPasswordForm(Request $request)
        {
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
              
            $mail=Mail::send('teacher.email.forget-password', ['token' => $token], function($message) use($request){
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
           return view('teacher.forgetPasswordLink', ['token' => $token]);
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
        public function check_assign($id) { 

       $data= Upload::where('aid', $id)
       ->update([
           'status' => 'check'
           
        ]);
       // dd($data);
        return back()->with('success',' assignment checked!');
        }
        public function uncheck_assign($id) { 

            $data= Upload::where('aid', $id)
            ->update([
                'status' => 'uncheck'
                
             ]);
            // dd($data);
             return back()->with('success',' assignment unchecked!');
             }

             public function see_announ(){

                $data1=Auth::user()->cid;
               $data= Announcement::where('cid',$data1)->get();
             //  dd( $data1);
                return view('teacher.see_announ',compact('data'));
             }
    
}
