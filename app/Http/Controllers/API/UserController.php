<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
class UserController extends Controller 
{
    public $successStatus = 200;
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'email' => 'required|email', 
            'password' => 'required', 
        ]);
        if ($validator->fails()) 
        { 
            return response()->json($validator->errors(), 401);            
        }
        
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['admin'] = $user->admin;
            return response()->json($success, $this-> successStatus); 
        }
        $mail = User::where('email', request('email') )->get();

        if($mail->count())
        {
            return response()->json(['password'=> ['wrong password']], 401); 
        }
        else{ 
            return response()->json(['email'=> ['wrong mail']], 401); 
        } 
    }

    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 

    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required|min:8', 
            'email' => 'required|email|unique:users', 
            'password' => 'required|min:6', 
            'c_password' => 'required|same:password',
            'phone' => 'required|min:11|max:11',
            'address' => 'required',
            'city' => 'required',
        ]);
/*

            $table->string('name', 25);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('personalImageUrl')->nullable()->default('https://thumbs.dreamstime.com/b/user-icon-orange-round-sign-vector-illustration-isolated-white-background-user-icon-orange-round-sign-112808533.jpg');
            $table->string('city', 20)->nullable();
            $table->string('phone', 11);
            $table->string('address', 100)->nullable();
            $table->boolean('admin')->default(0);

*/
        
        
        if ($validator->fails()) 
        { 
            return response()->json($validator->errors(), 401);            
        }

        $checkImage = false;
        $user = new User();
        //if($request->hasFile('personalImageUrl'))
        //{
            $image = $request->file('personalImageUrl');
            $name = str_slug($request->email) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $imagePath = $destinationPath . '/' . $name;
            $image->move($destinationPath, $name);
            $user->personalImageUrl = $name;
            $checkImage = true;
        //}
        $user->name = $request->get('name');
        $user->password = bcrypt($request->get('password'));
        $user->email = $request->get('email');
        $user->city = $request->get('city');
        $user->phone = $request->get('phone');
        $user->address = $request->get('address');
        $user->save();
 
        $success['token'] =  $user->createToken('MyApp')-> accessToken; 
        $success['hasFile'] = $checkImage;
        $success['imageName'] = $user->personalImageUrl;
        return response()->json($success, $this-> successStatus); 
    }


    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 

    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json($user, $this-> successStatus); 
    } 

    public function findUserById($id)
    {
        $user = User::find($id);
        if(!$user)
        {
            return response()->json('User Not Found', 401); 
        }
        return response()->json($user, $this-> successStatus);
    }

    public function update(Request $request, $id)
    {


        $user = User::findOrFail($id);
        $validator = Validator::make($request->all(), [ 
            'name' => 'min:8', 
            'email' => 'email', 
            'password' => 'min:6', 
            'c_password' => 'same:password',
            'phone' => 'min:11|max:11',
        ]);

        if ($validator->fails()) 
        { 
            return response()->json($validator->errors(), 401);            
        }
        $users = User::where('email', $request->input('email') )->get();
        foreach($users as $currentUser)
        {
            if($currentUser->id != $id)
            {
                return response()->json(['email'=> ['This email has been already token.']], 401);
            }
        }
        


        $input = $request->all();
        if(isset($input['password']))
            $input['password'] = bcrypt($input['password']); 
        

        if($request->hasFile('personalImageUrl'))
        {
            $image = $request->file('personalImageUrl');
            $name = str_slug($user->email) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $imagePath = $destinationPath . '/' . $name;
            $image->move($destinationPath, $name);
            $input['personalImageUrl'] = $name;
        }
        
        
        $user->update($input);
        
        return response()->json($user, $this-> successStatus); 

    }




    public function destroy(Request $request, $id)
    {
        //
        $user = User::find($id);
        if(!$user)
        {
            return response()->json(['error'=>'Empty User'], 401); 
        }
        $user->delete();

        return response()->json(['success'=>'Deleted Successfully'], $this-> successStatus);
    }

    public function logoutApi()
    { 
        if (Auth::check()) {
            Auth::user()->AauthAcessToken()->delete();
            return response()->json(['success'=>'Logout Successfully'], $this-> successStatus);
        }
        return response()->json(['success'=>'null'], $this-> successStatus);
    }

}