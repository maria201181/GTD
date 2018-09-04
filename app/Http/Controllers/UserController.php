<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Http\Requests;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function __construct()
    {
       $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users= User::with('company', 'profile');

        if ($params = Input::get('filter')) {                    
            foreach ($params as $key => $value) {
                if ($value == '0' || $value) {
                    if ($key == 'rut') {
                        $value = str_replace("-", "", $value);
                        $users->where(DB::raw('concat_ws("",rut, dv)'), 'like', '%' . $value . '%');
                    }
                    else if ($key == 'name') {
                        $users->where(DB::raw('concat_ws(" ", name, surname, second_surname)'), 'like', '%' . $value . '%');
                    } 
                    else if ($key == 'company_id') {
                        $users->where('company_id', '=', $value);
                    }
                    else if ($key == 'status') {
                        $users->where('status', '=', $value);
                    }                   
                }
            }
        }

        $companies = Company::all()->lists( 'name', 'id')->toArray();
        $profiles = Profile::all()->lists('name', 'id')->toArray();

        $users = $users->paginate(10);

        return view('user', compact('users','companies', 'profiles') );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $post = Input::All();   
        $validator = Validator::make($post, $this->rules(null, $post['password'])); 
        $validator->setAttributeNames($this->attributeNames());
        if ($validator->fails()) {
            return response()->json(['success' => false, 200, 'errors' => $validator->errors()]);  
        }
        else {            
            $data = new User;
            $data->rut = $post['rut'];
            $data->dv = $post['dv'];
            $data->name = $post['name'];        
            $data->surname = $post['surname'];
            $data->second_surname = $post['second_surname'];
            $data->email = $post['email'];
            $data->password =  Hash::make($post['password']);
            $data->company_id = $post['company_id'];
            $data->profile_id = $post['profile_id'];
            $data->status = $post['status'];        

            if($data->save()) {
                return response()->json(['success' => true, 200, "message" => "Usuario creado exitosamente."]);            
            }
        }       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find((int) $id);

         return response()->json([
            'data' => $user,
            'success' => true,
            
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Input::All();
        $validator = Validator::make($post,  $this->rules($id, $post['password'])); 
        $validator->setAttributeNames($this->attributeNames());
        if ($validator->fails()) {
            return response()->json(['success' => false, 200, 'errors' => $validator->errors()]);
        }
        else {            
            $data = user::find((int) $id);
            /*$data->rut = $post['rut'];
            $data->dv = $post['dv'];
            $data->name = $post['name'];
            $data->surname = $post['surname'];
            $data->second_surname = $post['second_surname'];
            $data->email = $post['email'];*/
            if ($post['password'])
                $data->password =  Hash::make($post['password']);
            $data->company_id = $post['company_id'];
            $data->profile_id = $post['profile_id'];
            $data->status = $post['status'];        

            if($data->save()) {
                return response()->json(['success' => true, 200, "message" => "Usuario actualizado exitosamente."]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function disabled($id)
    {
        $data = user::find((int) $id);
        $data->status = $data->status==1?0:1;

        $data->save();

        return response()->json(['success' => true, 200]);
        
    }

    public function attributeNames () 
    { 
        return $attributeNames = array(
           'rut' => trans('content.rut'),
           'dv' => trans('content.dv'),
           'name' => trans('content.name'),
           'surname' => trans('content.surname'),
           'second_surname' => trans('content.second_surname'),
           'email' => trans('content.email'),
           'password' => trans('content.password'),
           'password_confirm' => trans('content.password_confirm'),
           'company_id'=> trans('content.company_id'),
           'profile_id' => trans('content.profile_id')
        );
    }

    public function rules($id, $password)
    { 
        $array = array(            
            'company_id'       => 'required',
            'profile_id'       => 'required'
        );

        if (!isset($id) || empty($id)) { 
            $array += array('rut'  => 'required|numeric|min:1');
            $array += array('dv'  => 'required');
            $array += array('name' => 'required');
            $array += array('surname' => 'required');
            $array += array('second_surname' => 'required');             
            $array += array('email'  => 'required|email|unique:users');
            $array +=  array ('password'  => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/');
            $array +=  array ( 'password_confirm' => 'required|same:password');
        }
        else {
            if ($password) {
                $array +=  array ('password'  => 'min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/');
                $array +=  array ( 'password_confirm' => 'same:password');
            }
        }

        return $array;

    }
}
