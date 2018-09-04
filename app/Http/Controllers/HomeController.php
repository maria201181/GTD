<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Models\Company;
use App\Models\Customer;
use App\Models\HistoryCustomer;
use App\Models\Device;
use App\Models\HistoryDevice;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use \stdClass;

use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $params = Input::get('filter');
        
        $companies = Company::all()->lists('name', 'id')->toArray();

        return view('home', compact('companies')); 

        /*
            $valid = true;
            $report = null;
            if ($params) { 
            $rules = array(
                'date_from'=>'date|before_or_equal:date_to|before:tomorrow',
                'date_to'=>'date|before:tomorrow',
            );
            
            Validator::extend('before_or_equal', function($attribute, $value, $parameters, $validator) {
                return strtotime($validator->getData()[$parameters[0]]) >= strtotime($value);
            }, 'Fecha inicial debe ser inferior o igual a fecha final');

            $validator = Validator::make($params, $rules); 
            $validator->setAttributeNames($this->attributeNames());
            $valid = !$validator->fails();
        }

        if ($valid) {
            $date_from = ($params['date_from'] == '' ) ? Carbon::now()->format('Y-m-d') : $params['date_from'];
            $date_to = ($params['date_to'] == '') ? Carbon::now()->format('Y-m-d') : $params['date_to'];
            $date_from_origin = $date_from;

            $date_end_load = Carbon::createFromFormat('Y-m-d H:i:s', HistoryCustomer::max('created_at'))->format('Y-m-d');
            
            $max_date_load = Carbon::createFromFormat('Y-m-d H:i:s', HistoryCustomer::max('created_at'))->format('Y-m-d');

            if($date_from > $max_date_load) {
                $date_from = $max_date_load;
            }        
            else {
                $max_date_load = HistoryCustomer::select('created_at')->whereRaw("DATE(history_customers.created_at)>='".$date_from."'" )->whereRaw("DATE(history_customers.created_at)<='".$date_to."'" )->orderBy('created_at', 'desc')->first();
                $max_date_load = Carbon::createFromFormat('Y-m-d H:i:s',$max_date_load->created_at)->format('Y-m-d');
            }

            $report = HistoryCustomer::with('customer', 'customer.company');

            $report->whereRaw("DATE(created_at)>='".$date_from."'" );
            $report->whereRaw("DATE(created_at)<='".$date_to."'" );

            if($params['company_id']) {
                $key = 'company_id';
                $value = $params['company_id'];
                $report->whereHas('customer.company', function ($query) use ($key, $value) {
                    $query->where($key, '=', $value);
                });
            }
            
            $report->groupBy('customer_id');

            $report = $report->paginate(10);

            foreach ($report as $history_customer) {
                $customer_unsuscribe = false;            
                
                if (!$customer_unsuscribe) {
                    $devices = Device::where('customer_id', '=', $history_customer->customer_id)->get();             
                    $history_customer->number_devices = 0;
                    if (count($devices)) {
                        $devices_id = "";
                        foreach ($devices as $device) {
                            $devices_id .= $device->id.',';
                        }
                        $devices_id = rtrim($devices_id,", ");

                        $history_customer->number_devices = count(HistoryDevice::whereRaw('device_id IN('.$devices_id.')')->whereRaw("DATE(created_at)>='".$date_from."'" )->whereRaw("DATE(created_at)<='".$date_to."'" )->groupBy('device_id')->get([DB::raw('MAX(id)')]));
                        
                        $backed_successfully = count(HistoryDevice::whereRaw('device_id IN('.$devices_id.')')
                                                    ->where('last_backup_status', '=', 'Backed Up Successfully')
                                                    //->whereRaw("DATE(created_at)>='".$date_from."'" )
                                                    //->whereRaw("DATE(created_at)<='".$date_to."'" )
                                                    ->whereRaw("DATE(created_at)='".$max_date_load."'" )
                                                    ->whereRaw("ABS(DATEDIFF(backup_end_time,'" .$max_date_load. "')) <= 30")
                                                    ->first());


                        if($backed_successfully > 0) {
                            $history_customer->status = "Activo y usa el producto";                    
                        }
                        else {
                            $backed_successfully_old = count(HistoryDevice::whereRaw('device_id IN('.$devices_id.')')
                                                        ->where('last_backup_status', '=', 'Backed Up Successfully')
                                                        ->whereRaw("DATE(created_at)='".$max_date_load."'" )
                                                        ->orderBy('created_at', 'desc')
                                                        ->first());
                            if($backed_successfully_old > 0){
                                $history_customer->status = "Activo y no usa el producto";                        
                            }
                            else {
                                $history_customer->status = "No usa el servicio";
                            }
                        }
                    }
                    else {
                        $history_customer->status = "No usa el servicio";                
                    }
                }
            }
            return view('home', compact('report','companies')); 
        }
        else {
            $errors = $validator->errors();
            return view('home', compact('report','companies', 'errors')); 
        }*/
    }


    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */

    public function summary(Request $request) {

        $params = Input::get('filter');

        $summary = array(
            'total_users' =>  0,
            'total_users_active' =>  0,
            'total_users_unused' =>  0,
            'total_users_unsuscribe' =>  0
        );

        
        $unsuscribes = DB::connection('pgsql')->table('solicitud')
        ->select('solCorreo')
        ->where('solTipo', '=', '2')
        ->where('solEstado', '=', '1');

        if($params['company_id']) {
            $id_intern = Company::select('id_intern')->where('id', '=', $params['company_id'])->first()->id_intern;
            $unsuscribes= $unsuscribes->where('solIdEnEmpresa', '=', $id_intern);
        }

        $unsuscribes = $unsuscribes->groupBy("solCorreo")->get(); 
        
        $summary['total_users_unsuscribe'] = count($unsuscribes);
        
        $max_date_load = Carbon::createFromFormat('Y-m-d H:i:s', HistoryCustomer::max('created_at'))->format('Y-m-d');
        

        $report = Customer::select( 'customers.id as customer_id ',
                                    'customers.email_id as email_id',
                                    'devices.id as device_id',
                                    'history_devices.last_backup_status as last_backup_status',
                                    'history_devices.backup_end_time as backup_end_time',
                                    'history_devices.created_at as his_dev_created_at')
                                ->leftjoin('devices', function ($join) {
                                    $join->on('devices.customer_id', '=', 'customers.id');
                                })
                                ->leftjoin('history_devices', function ($join) {
                                    $join->on('history_devices.device_id', '=', 'devices.id')
                                    ;                                    
                                })                                    
                                ->whereRaw("((DATE(history_devices.created_at)= '".$max_date_load."') OR  history_devices.created_at IS NULL)" )
                                ->orderBy('customers.id')
                                ->orderBy('history_devices.created_at', 'desc')
                                ;

        if($params['company_id']) {         
            $report = $report->where("customers.company_id", "=", $params['company_id'] );
        }

        $report = $report->get();
        
        $grouped_customers = $report->groupBy('customer_id');
        
        $summary['total_users'] = count($grouped_customers);
        $day_max_load = Carbon::createFromFormat('Y-m-d', $max_date_load); 

        if (count($grouped_customers) > 0) {
            foreach ($grouped_customers as $history_customer) {
                $customer_unsuscribe = false;                
                if (!$customer_unsuscribe) {
                    if ($history_customer[0]->device_id == null) {
                        $summary['total_users_unused'] +=  1;    
                    }
                    else {                        
                        $grouped_devices = $history_customer->groupBy('device_id');
                        $active = false;
                        $active_unused= false; 
                        foreach ($grouped_devices as $devices) {
                            $active = true;
                            $summary['total_users_active'] +=  1; 
                        }
                        if(!$active) {                                
                            $summary['total_users_unused'] +=  1;    
                        }
                    }
                }
            }
        }
        return response()->json(['success' => true, 200, 'summary' => $summary]);              
   
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */

    public function reportExcel(Request $request) {

        $subscribes = DB::connection('pgsql')->table('solicitud')
            ->where('solTipo', '=', '1')->get();
        
        $max_date = Carbon::createFromFormat("Y-m-d H:i:s", HistoryDevice::max('created_at'))->format('Y-m-d');
        $report =Customer::select( 'customers.id as customer_id ',
                                   'customers.rut as rut', 
                                   'customers.email_id as email_id',     
                                   'customers.user_name as user_name',
                                   'customers.added_on as added_on',
                                   'customers.plan as plan',
                                   'customers.date_subscribed as date_subscribed',
                                   'customers.date_actived as date_actived',
                                   'companies.name as company_name',
                                   'devices.id as device_id',
                                   'history_devices.last_backup_status as last_backup_status',
                                   'history_devices.backup_end_time as backup_end_time',
                                   'history_devices.created_at as his_dev_created_at')
                                ->leftjoin('companies', function ($join) {
                                    $join->on('companies.id', '=', 'customers.company_id');
                                })
                                ->leftjoin('devices', function ($join) {
                                    $join->on('devices.customer_id', '=', 'customers.id');
                                })
                                ->leftjoin('history_devices', function ($join){
                                    $join->on('history_devices.device_id', '=', 'devices.id')
                                    ;                                    
                                })                              
                                ->whereRaw("(DATE(history_devices.created_at)= '".$max_date."' OR  history_devices.created_at IS NULL)"   )
                                ->orderBy("customers.id")
                                ->orderBy('history_devices.created_at', 'desc')
                                ->get()
                                ;

        $data= array();
        $array= array();
        
        $customers = $report->groupBy('customer_id');

        foreach ($customers as $customer) {
            $email = $customer[0]->email_id;            
            $array['RUT'] = $customer[0]->rut;
            $array['Nombre'] = strtoupper($customer[0]->user_name);
            $array['Email'] = $customer[0]->email_id;
            $array['Empresa'] = $customer[0]->company_name;
            $array['Plan'] = $customer[0]->plan;
            $array['Fecha de Alta'] = "";
            if ($customer[0]->date_subscribed != null)    
                $array['Fecha de Alta'] = Carbon::createFromFormat('Y-m-d H:i:s', $customer[0]->date_subscribed)->format('d-m-Y H:i:s');
            $array['Estatus de Habilitación'] = '';
            $array['Fecha de Habilitación'] = '';
            if ($customer[0]->date_actived != null)    
                $array['Fecha de Habilitación'] = Carbon::createFromFormat('Y-m-d H:i:s', $customer[0]->date_actived)->format('d-m-Y H:i:s');
            $array['Número de Dispositivos'] = 0;
            $array['Fecha de Último Respaldo'] = '';
            
            if($customer[0]->device_id != null) {
                $group_devices = $customer->groupBy('device_id');
                $array['Número de Dispositivos'] = count($group_devices);
                foreach ($customer as $device) {
                    if($device->last_backup_status == 'Backed Up Successfully') {
                        $array['Fecha de Último Respaldo'] = Carbon::createFromFormat('Y-m-d H:i:s', $device->backup_end_time)->format('d-m-Y H:i:s');
                        break;
                    }
                }
            }
            $data[]= $array;            
        }
        
        $nameFile = "Reporte General del Sistema";
        $cabecera = 'A6:J6';        

        $title = array();
        
        $title[] = ['REPORTE GENERAL DEL SISTEMA'];        
        //$title[] = ['Fecha:' , Carbon::now()->formatLocalized("%A, %d de %B de %Y")];
        $title[] = ['Fecha:' , Carbon::now()->format("d-m-Y H:i:s")];
        $title[] = ['Usuario:' ,  Auth::user()->name.' '.Auth::user()->surname.' '.Auth::user()->second_surname];
        
        Excel::create($nameFile, function($excel) use ($data , $title, $nameFile, $cabecera) {
            $excel->sheet($nameFile, function($sheet) use($data , $title, $cabecera  ) {
                $sheet->setStyle(array(
                        'font' => array(
                            'name'      =>  'Calibri',
                            'size'      =>  9)
                ));


                $sheet->cells('A1:J1', function($cells) {
                    $cells->setFontColor('#ffffff');
                    $cells->setBackground('#32345F');
                    $cells->setFont(array(
                                        'family'     => 'Calibri',
                                        'size'       => '9',
                                        'bold'       =>  true 
                                    ));
                });

                
                $sheet->row(1, $title[0]);
                $sheet->row(3, $title[1]);                
                $sheet->row(4, $title[2]);
                $sheet->mergeCells('A1:H1');


                $sheet->cells('A1:J1', function($cells) {
                $cells->setAlignment('center');
                    $cells->setFont(array(
                                        'family'     => 'Calibri',
                                        'size'       => '10',
                                        'bold'       =>  true 
                                    ));              
                });
                
                $sheet->fromArray($data, null, 'A6', true, true);

      
                $sheet->cells($cabecera, function($cells) {
                    $cells->setFontColor('#ffffff');
                    $cells->setBackground('#32345F');
                    $cells->setFont(array(
                                        'family'     => 'Calibri',
                                        'size'       => '9',
                                        'bold'       =>  true 
                                    ));
                });

                $sheet->setAutoSize(true);
            });
        })->download('xlsx');

    }


     public function attributeNames () 
    { 
        return $attributeNames = array(
           'date_from' => trans('content.date_from'),
           'date_to' => trans('content.date_to')
        );
    }

    /* ORIGINAL


    public function summary(Request $request) {

        $params = Input::get('filter');

        $summary = array(
            'total_users' =>  0,
            'total_users_active' =>  0,
            'total_users_active_unused' =>  0,
            'total_users_unused' =>  0,
            'total_users_unsuscribe' =>  0
        );


        $rules = array(
            'date_from'=>'date|before_or_equal:date_to|before:tomorrow',
            'date_to'=>'date|before:tomorrow',
        );

        $valid = true;

        if ($params['date_from'] != "" && $params['date_to'] != "") {    
            
            Validator::extend('before_or_equal', function($attribute, $value, $parameters, $validator) {
                return strtotime($validator->getData()[$parameters[0]]) >= strtotime($value);
            }, 'Fecha inicial debe ser inferior o igual a fecha final');

            $validator = Validator::make($params, $rules); 
            $validator->setAttributeNames($this->attributeNames());
            $valid = !$validator->fails();
        }

        if ($valid) {
            $date_from = ($params['date_from'] == '' ) ? Carbon::now()->format('Y-m-d') : $params['date_from'];
            $date_to = ($params['date_to'] == '') ? Carbon::now()->format('Y-m-d') : $params['date_to'];
            $date_from_origin = $date_to;

             $unsuscribes = DB::connection('pgsql')->table('solicitud')
            ->where('solTipo', '=', '2')
            ->where('solEstado', '=', '1')
            ->whereRaw('"solRealizado"::timestamp::date >='."'".$date_from."'" )        
            ->whereRaw('"solRealizado"::timestamp::date <='."'".$date_to."'" );

            if($params['company_id']) {
                $id_intern = Company::select('id_intern')->where('id', '=', $params['company_id'])->first()->id_intern;
                $unsuscribes= $unsuscribes->where('solIdEnEmpresa', '=', $id_intern);       
            }

            $unsuscribes = $unsuscribes->get(); 
            
            $summary['total_users_unsuscribe'] = count($unsuscribes);
            
            $max_date_load = Carbon::createFromFormat('Y-m-d H:i:s', HistoryCustomer::max('created_at'))->format('Y-m-d');

            if($date_from > $max_date_load) {
                $date_from = $max_date_load;
            }        
            else {
                $max_date_load = HistoryCustomer::select('created_at')->whereRaw("DATE(history_customers.created_at)>='".$date_from."'" )->whereRaw("DATE(history_customers.created_at)<='".$date_to."'" )->orderBy('created_at', 'desc')->first();
                $max_date_load = Carbon::createFromFormat('Y-m-d H:i:s',$max_date_load->created_at)->format('Y-m-d');
            }

            $report = Customer::select( 'history_customers.customer_id as customer_id ',
                                        'customers.email_id as email_id',    
                                        'history_customers.created_at as his_cus_created_at',
                                        'devices.id as device_id',
                                        'history_devices.last_backup_status as last_backup_status',
                                        'history_devices.backup_end_time as backup_end_time',
                                        'history_devices.created_at as his_dev_created_at')
                                    ->join('history_customers', function ($join) {
                                        $join->on('history_customers.customer_id', '=', 'customers.id');
                                    })
                                    ->leftjoin('devices', function ($join) {
                                        $join->on('devices.customer_id', '=', 'customers.id');
                                    })
                                    ->leftjoin('history_devices', function ($join) use ($date_from, $date_to){
                                        $join->on('history_devices.device_id', '=', 'devices.id')
                                        ;                                    
                                    })
                                    ->whereRaw("DATE(history_customers.created_at)='".$max_date_load."'" )
                                    ->whereRaw("(DATE(history_devices.created_at)= DATE(history_customers.created_at) OR  history_devices.created_at IS NULL)"   )
                                    ->orderBy('history_customers.customer_id')
                                    ->orderBy('history_devices.created_at', 'desc')
                                    ;

            if($params['company_id']) {         
                $report = $report->where("customers.company_id", "=", $params['company_id'] );
            }

            $report = $report->get();
            
            $grouped_customers = $report->groupBy('customer_id');
            
            $summary['total_users'] = count($grouped_customers);
            $day_max_load = Carbon::createFromFormat('Y-m-d', $max_date_load); 

            if (count($grouped_customers) > 0) {
                foreach ($grouped_customers as $history_customer) {
                    $customer_unsuscribe = false;                
                    if (!$customer_unsuscribe) {
                        if ($history_customer[0]->device_id == null) {
                            $summary['total_users_unused'] +=  1;    
                        }
                        else {                        
                            $grouped_devices = $history_customer->groupBy('device_id');
                            $active = false;
                            $active_unused= false; 
                            foreach ($grouped_devices as $devices) {                                
                                $day_backup= Carbon::createFromFormat('Y-m-d H:i:s', $devices[0]->backup_end_time);
                                $diff = $day_backup->diffInDays($day_max_load);
                                if($devices[0]->last_backup_status == 'Backed Up Successfully' ) {
                                    $active_unused = true;
                                    if ($diff <= 30) {                                            
                                        $active = true;
                                        $summary['total_users_active'] +=  1; 
                                        break;
                                    }   
                                }
                            }
                            if(!$active) {
                                if ($active_unused)
                                   $summary['total_users_active_unused'] +=  1; 
                                else
                                $summary['total_users_unused'] +=  1;    
                            }
                        }
                    }
                }
            }
            return response()->json(['success' => true, 200, 'summary' => $summary]);              
        }
        else {
            $errors = $validator->errors();
            return response()->json(['success' => false, 200, 'errors'=> $errors]);  
        }
    }*/






    
}
    