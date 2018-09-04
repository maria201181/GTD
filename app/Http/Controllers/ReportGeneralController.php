<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Company;
use App\Models\Customer;
use App\Models\ReportGeneral;
use Carbon\Carbon;

class ReportGeneralController extends Controller
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
        $month= array("01" => "Enero",
                    "02" => "Febrero",
                    "03" => "Marzo",
                    "04" => "Abril",
                    "05" => "Mayo",
                    "06" => "Junio",
                    "07" => "Julio",
                    "08" => "Agosto",
                    "09" => "Septiembre",
                    "10" => "Octubre",
                    "11" => "Noviembre",
                    "12" => "Diciembre"
                    );    
        
        $params = Input::get('filter');

        $company_id = "";

        if (isset($params['company_id']) && !empty($params['company_id'] )) {
            $company_id = $params['company_id'];
        }

        if (isset($params['year']) && !empty($params['year'] )) {
            $year = $params['year'];
        }
        else {
            $year = Carbon::Now()->format('Y');
        }
        
               
        // begin content filters
        $max_fecha = DB::connection('pgsql')->table('solicitud')->max('solIngreso');        
        $min_fecha = DB::connection('pgsql')->table('solicitud')->min('solIngreso');

        $min_year = Carbon::createFromFormat('Y-m-d H:i:s.u', $min_fecha)->format('Y');        
        $max_year = Carbon::createFromFormat('Y-m-d H:i:s.u', $max_fecha)->format('Y');

        $years = range($min_year, $max_year);
        $years_available = array();
        foreach ($years as $y) {
            $years_available = $years_available + array($y =>$y);
        }
        
        $companies = Company::all()->lists('name', 'id')->toArray();
        // end content filters

        if($company_id == '')
           $report = ReportGeneral::selectRaw('MAX(period) as period , 
                                            SUM(customers_subscribed) as customers_subscribed,
                                            SUM(customers_active) as customers_active ,
                                            SUM(customers_activo_used) as customers_activo_used ,
                                            SUM(customers_activo_unused) as customers_activo_unused ,
                                            SUM(customers_unsubscribed) as customers_unsubscribed ')
                                        ->groupBy('period')
                                        ->orderBy('period', 'desc');
        else {
            $report = ReportGeneral::where('company_id', '=', $company_id );
        }

        if ($year) {
             $report->where('period', 'LIKE', "%".$year."%");
        }

        $report = $report->get();
        

        return view('reportGeneral', compact('report','companies', 'years_available', 'month'));
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
    public function store(Request $request)
    {
        //
    }
    
}


/*$company_id = "";
        if (isset($params['company_id']) && !empty($params['company_id'] )) {
            $company_id = $params['company_id'];
        }
        
        $year = Carbon::now()->format('Y');
        if (isset($params['year']) && !empty($params['year'])) {
            $year = $params['year'];    
        }

        $date_from = $year."-01-01";
        $date_to = $year."-12-31";        
            
        $request = DB::connection('pgsql')
                    ->table('solicitud')
                    ->whereRaw('"solIngreso"::timestamp::date >='."'".$date_from."'" )        
                    ->whereRaw('"solIngreso"::timestamp::date <='."'".$date_to."'" )
                    ->orderBy('solIngreso', 'asc');
                    ;

        if ($company_id != "") {
            $id_intern = Company::select('id_intern')->where('id', '=', $params['company_id'])->first()->id_intern;
            $request = $request->where('solIdEnEmpresa', '=', $id_intern);
        }

        $requests =  $request->get();            

        $history_customers = Customer::history($date_from, $date_to, $company_id);
        
        $group_month_customers = $history_customers->get()->groupBy(function($val) {
            return Carbon::parse($val->his_cus_created_at)->format('m');
        });

        $months = array();

        $month_ini = Carbon::createFromFormat('Y-m-d H:i:s.u', $requests[0]->solIngreso)->format('m');
        $month_fin = Carbon::createFromFormat('Y-m-d H:i:s.u', $requests[count($requests)-1]->solIngreso)->format('m');

        $range_month = range($month_ini, $month_fin, 1);
        foreach ($range_month as $num_month) {
            $months = $months + array (str_pad($num_month, 2, "0", STR_PAD_LEFT) => array(
                           "customer_sold" => 0,
                           "customer_active" => 0,
                           "customer_activo_used" => 0,
                           "customer_activo_unused" => 0,
                           "customer_unsuscribe" => 0));
        }

        foreach ($requests as $request) {
            $month_entry = Carbon::createFromFormat('Y-m-d H:i:s.u', $request->solIngreso)->format('m');
            $year_entry = Carbon::createFromFormat('Y-m-d H:i:s.u', $request->solIngreso)->format('Y');
            $month_realized = "";
            $year_realized = "";
            if ($request->solEstado == 1) {
                $month_realized = Carbon::createFromFormat('Y-m-d H:i:s.u', $request->solRealizado)->format('m');
                $year_realized = Carbon::createFromFormat('Y-m-d H:i:s.u', $request->solRealizado)->format('Y');
            }

            if ($request->solTipo == 1) {
                $months[$month_entry]['customer_sold'] +=1;
                if ($month_entry ==  $month_realized &&  $request->solEstado == 1){
                    $months[$month_entry]['customer_active'] +=1;
                    if (isset($group_month_customers[$month_entry]) && !empty($group_month_customers[$month_entry])) {
                        $email = $request->solCorreo;
                        $active = false;
                        foreach ($group_month_customers[$month_entry] as $month_customers) {
                            if ($month_customers->email_id && $month_customers->last_backup_status == 'Backed Up Successfully') {
                                $months[$month_entry]['customer_activo_used']+= 1; 
                                $active = true;
                                break;
                            }
                        }
                        if (!$active)
                            $months[$month_entry]['customer_activo_unused'] += 1;
                    }
                }
            }
            else if ($request->solTipo == 2 && $request->solEstado == 1) {
                if ( $year_entry == $year_realized)
                    $months[$month_realized]['customer_unsuscribe'] += 1;
            }
        }*/





