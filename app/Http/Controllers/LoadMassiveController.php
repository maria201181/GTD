<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Device;
use App\Models\HistoryCustomer;
use App\Models\HistoryDevice;

use App\Http\Requests;
use Carbon\Carbon;

class LoadMassiveController extends Controller
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
        $companies = Company::all()->lists( 'name', 'id')->toArray();

        return view("loadMassive", compact('companies'));
    }

      
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store() {

        if(Auth::check()) {
            $post = Input::All();        

            $date = Input::get('date');
            $company_id = Input::get('company_id');
            $plan = Input::get('plan');
            
            $validator = Validator::make($post, $this->rules()); 
            $validator->setAttributeNames($this->attributeNames());
            if (Input::file('csvfile')) {
                $extension = Input::file('csvfile')->getClientOriginalExtension();
                $bool = in_array($extension, array('xls', 'xlsx', 'csv'));
                $validator->after(function ($validator) use ($bool) {
                    if (!$bool) {
                        $validator->errors()->add('csvfile', 'Archivo con extensión inválida, estan permitidos sólo los archivos con extensión .csv, .xls, .xlsx.');
                    }
                });
            } 
            if ($validator->fails()) {
                return response()->json(['success' => false, 200, 'errors' => $validator->errors()]);  
            }
            else {
                $file = array('csvfile' => Input::file('csvfile'));
                $extension = Input::file('csvfile')->getClientOriginalExtension();

                $newname = rand(1, 1000000) . '.' . $extension;

                $succes = Input::file('csvfile')->move(public_path('uploads') . '/', $newname);
                if ($succes) {
                    $input = Input::file('csvfile');
                    $filename = public_path('uploads') . '/' . $newname;
                }

                Excel::filter('chunk')->load($filename)->chunk(100, function ($rows) use($date, $company_id, $plan){
                    /*
                    User Name : miguel garrido pino; 
                    Email : magp_max@hotmail.com;   
                    Added On : 21-02-2018 15:38;    
                    Profile : Residencial1; 
                    User Status : Active; 
                    Data Source : MAX; 
                    Activated On : 13-06-2018 23:32;  
                    System Settings : Not Backed Up"; 
                    Last Backup Status : Backup Failed;  
                    Last Backup Time : 14-06-2018 22:08;    
                    First Backup Status : Complete; 
                    First Backup Size (MB): Complete;  
                    Bytes Transferred (MB) : 11789.73;  
                    Time Taken (hh:mm) : 1:47*/
                    
                    $rows->each(function ($cell) use ($date, $company_id, $plan) {                
                        $valuesRow = explode(';',  array_values($cell->toArray())[0]); 

                        $values['user_name'] = $valuesRow[0]; //Customer                
                        $values['email_id'] = $valuesRow[1]; //Customer                
                        $values['added_on'] = $valuesRow[2]; //Customer
                        $values['profile'] = $valuesRow[3]; //Customer
                        $values['user_status'] = $valuesRow[4]; //Customer
                        $values['device_name'] = $valuesRow[5]; //Device 
                        $values['added_on_device'] = $valuesRow[6];//Device
                        $values['system_settings_backed_up'] = $valuesRow[7]; //History Devices                
                        $values['last_backup_status'] = $valuesRow[8]; //History Devices
                        $values['backup_end_time'] = $valuesRow[9]; //History Devices
                        $values['first_backup_status'] = $valuesRow[10]; //History Devices
                        $values['first_backup_size'] = $valuesRow[11]; //History Devices
                        $values['bytes_transferred'] = $valuesRow[12]; //History Devices
                        $values['time_taken'] = $valuesRow[13]; //History Devices
                        $customer_new = false;


                        $customer = Customer::where('email_id', '=', $values['email_id'])->first(); 
                        if ($customer) {
                            $customer->user_name = $values['user_name'];
                        }
                        else {
                            $customer = new Customer(); 
                            $rut = DB::connection('pgsql')
                                            ->table('solicitud')
                                            ->select('solRut','solIngreso', 'solRealizado')
                                            ->where('solCorreo', '=', strtolower($values['email_id']))
                                            ->where('solTipo', '=', 1)
                                            ->where('solEstado', '=', 1)
                                            ->first();
                            if (count($rut) > 0) {                         
                                $customer->rut = $rut->solRut;
                                $customer->date_subscribed = $rut->solIngreso;
                                $customer->date_actived = $rut->solRealizado;
                            }                   
                            $customer->user_name = $values['user_name'];
                            $customer->email_id = $values['email_id'];                        
                            $customer->added_on = Carbon::createFromFormat('d-m-Y H:i' ,$values['added_on'])->format('Y-m-d H:i:s');
                            $customer->profile = $values['profile'];           
                            $customer->company_id = $company_id;
                            $customer->plan = $plan;  
                            $customer->save(); 
                            $customer_new = true;
                        }

                        $history_customer = new HistoryCustomer(); 
                        $history_customer->customer_id = $customer->id;
                        $history_customer->user_status = $values['user_status'];                
                        $history_customer->created_at = Carbon::createFromFormat('Y-m-d' , $date)->format('Y-m-d H:i:s'); 
                        $history_customer->save();

                        if ($values['device_name']!= "" ) {
                            $device = false;
                            if (!$customer_new)
                                $device = Device::where('device_name', '=', $values['device_name'])->where('customer_id', '=', $customer->id)->first(); 
                            if (!$device) {
                                $device = new Device();
                                $device->device_name = $values['device_name'];
                                $device->customer_id = $customer->id;
                                $device->added_on = Carbon::createFromFormat('d-m-Y H:i', $values['added_on_device'])->format('Y-m-d H:i:s');
                                $device->save();
                            }

                            $history_device = new HistoryDevice();
                            $history_device->device_id  = $device->id;
                            $history_device->last_backup_status = $values['last_backup_status']; 
                            if ($values['backup_end_time'] != "")
                                $history_device->backup_end_time = Carbon::createFromFormat('d-m-Y H:i', $values['backup_end_time'])->format('Y-m-d H:i:s');
                            $history_device->first_backup_status = $values['first_backup_status'];
                            $history_device->first_backup_size = $values['first_backup_size'];
                            $history_device->bytes_transferred = $values['bytes_transferred'];
                            $history_device->time_taken = $values['time_taken'];
                            $history_device->created_at = Carbon::createFromFormat('Y-m-d' , $date)->format('Y-m-d H:i:s'); 
                            $history_device->save();
                        }
                        
                    });
                    
                });
                unlink($filename);

               return response()->json(['success' => true, 200]);  
            }

            //$companies = Company::all()->lists( 'name', 'id')->toArray();

            //return view("loadMassive", compact('companies'));
        }
        else {
            Session::flush();
            return Redirect::route('login');

        }
        
    }


    public function attributeNames () 
    { 
        return $attributeNames = array(           
           'date' => trans('content.date'), 
           'company_id'=> trans('content.company_id'),
           'plan' => trans('content.plan'),
           'csvfile' => trans('content.file')
        );
    }

    public function rules()
    { 
        return array(
            'date'             => 'required|before:tomorrow',
            'company_id'       => 'required',
            'plan'             => 'required',
            'csvfile'          => 'required'
        );
    } 

   
}
