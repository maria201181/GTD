<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Http\Requests;

class CompanyController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $companies = Company::query();

        if ($params = Input::get('filter')) {                    
            foreach ($params as $key => $value) {
                if ($value == '0' || $value) {
                    if ($key == 'name') {
                        $companies->where('name', 'like', '%' . $value . '%');
                    } else if ($key == 'status') {
                        $companies->where('status', '=', $value);
                    }                   
                }
            }
        }
        $companies = $companies->paginate(10);
        return view('company', compact('companies'));
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
     * @return Response
     */
    public function store(Request $request)
    {
        $post = Input::All();
        $validator = Validator::make($post, $this->rules()); 
        $validator->setAttributeNames($this->attributeNames());
        if ($validator->fails()) {

            return response()->json(['success' => false, 200, 'errors' => $validator->errors()]);
        }
        else {
            $data = new Company();
            $data->rut = $post['rut'];
            $data->dv = $post['dv'];
            $data->name = $post['name'];        
            $data->contact_phone = $post['contact_phone'];
            $data->contact_name = $post['contact_name'];
            $data->status = $post['status'];

            if($data->save()) {
                return response()->json(['success' => true, 200, "message"=> "Empresa creada satisfactoriamente."]);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::find((int) $id);
        return response()->json([
            'passes' => true,
            'data' => $company
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
        $data = Company::find((int) $id);

        $data->rut = $post['rut'];
        $data->dv = $post['dv'];
        $data->name = $post['name'];        
        $data->contact_phone = $post['contact_phone'];
        $data->contact_name = $post['contact_name'];
        $data->status = $post['status'];

        if($data->update()) {
            return response()->json(['success' => true, 200, "message"=> "Empresa actualizada satisfactoriamente."]);
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
        $data = Company::find((int) $id);
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
           'contact_phone' => trans('content.contact_phone'),
           'contact_name' => trans('content.contact_name')
        );
    }

    public function rules()
    { 
        return array(
            'rut'             => 'required|numeric|min:1',
            'dv'              => 'required',                        
            'name'            => 'required',
            'contact_phone'   => 'required',
            'contact_name'    => 'required'
        );
    }    

}
