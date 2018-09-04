<?php
 
namespace App\Console\Commands;
 
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use App\Models\Company;
use App\Models\Customer;
use App\Models\ReportGeneral;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use DatePeriod;
 
class LoadDataMasiveReportGeneral extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:data:masive:report:general';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Carga de los datos de en Reporte General';
 
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
 
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $companies = Company::select('id', 'id_intern')->get();

        foreach ($companies as $company) {

            Log::info(Carbon::now());
            Log::info('Primero '.$company->id);

            $date_from = DB::connection('pgsql')->table('solicitud')->select('solIngreso')->where('solIdEnEmpresa', '=', $company->id_intern)->orderBy('solIngreso','asc')->first()->solIngreso;        
            $date_to = DB::connection('pgsql')->table('solicitud')->select('solIngreso')->where('solIdEnEmpresa', '=', $company->id_intern)->orderBy('solIngreso','desc')->first()->solIngreso;

            $date_from = Carbon::createFromFormat('Y-m-d H:i:s.u', $date_from)->startOfMonth()->format('Y-m-d');
            $date_to = Carbon::createFromFormat('Y-m-d H:i:s.u', $date_to)->format('Y-m-d');
            
            $requests = DB::connection('pgsql')
                        ->table('solicitud')
                        ->whereRaw('"solIngreso"::timestamp::date >='."'".$date_from."'" )        
                        ->whereRaw('"solIngreso"::timestamp::date <='."'".$date_to."'" )
                        ->orderBy('solIngreso', 'asc')
                        ->where('solIdEnEmpresa', '=', $company->id_intern)
                        ->whereIn('solTipo', array(1, 2))
                        ->get();
                        ;

            $history_customers = Customer::select( 
                'customers.id as customer_id ',
                'customers.email_id as email_id',    
                /*'history_customers.created_at as his_cus_created_at',*/
                'devices.id as device_id',
                'history_devices.last_backup_status as last_backup_status',
                'history_devices.backup_end_time as backup_end_time',
                'history_devices.created_at as his_created_at')
            /*->join('history_customers', function ($join) {
                $join->on('history_customers.customer_id', '=', 'customers.id');
            })*/
            ->leftjoin('devices', function ($join) {
                $join->on('devices.customer_id', '=', 'customers.id');
            })
            ->leftjoin('history_devices', function ($join) use ($date_from, $date_to){
                $join->on('history_devices.device_id', '=', 'devices.id')
                ;                                    
            })                                
            /*->whereRaw("DATE(history_customers.created_at)>='".$date_from."'" )
            ->whereRaw("DATE(history_customers.created_at)<='".$date_to."'" )*/
            ->where('customers.company_id', '=', $company->id)
            ->whereRaw("((DATE(history_devices.created_at)>='".$date_from."' AND (DATE(history_devices.created_at)) <= '".$date_to."') OR  history_devices.created_at IS NULL)"   )
            ->orderBy('customers.id')
            ->orderBy('history_devices.created_at', 'desc')
            ->get()->groupBy(function($val) {
                return Carbon::parse($val->his_created_at)->format('Ym');
            });

            $periods = array();
            

            $begin = new DateTime( $date_from);
            $end = new DateTime(  $date_to );


            Log::info($date_from);

            Log::info($date_to);

            //Log::info($history_customers->toSql());

            $interval = DateInterval::createFromDateString('last next month');
            $period = new DatePeriod($begin, $interval, $end);

            
            foreach ($period as $dt) {
                $periods = $periods + array ($dt->format( "Ym" ) => array(
                               "period" =>  $dt->format( "Ym" ),                               
                               "customers_subscribed" => 0,
                               "customers_active" => 0,
                               "customers_activo_used" => 0,
                               "customers_activo_unused" => 0,
                               "customers_unsubscribed" => 0));
            }

             Log::info($periods);

            foreach ($requests as $request) {
                $period_entry = Carbon::createFromFormat('Y-m-d H:i:s.u', $request->solIngreso)->format('Ym');
                $period_realized = "";                
                if ($request->solEstado == 1) {
                    $period_realized = Carbon::createFromFormat('Y-m-d H:i:s.u', $request->solRealizado)->format('Ym');
                }
                if ($request->solTipo == 1) {
                    $periods[$period_entry]['customers_subscribed'] +=1;
                    if ($period_entry ==  $period_realized &&  $request->solEstado == 1){
                        $periods[$period_entry]['customers_active'] +=1;
                        if (isset($history_customers[$period_entry]) && !empty($history_customers[$period_entry])) {
                            $active = false;
                            $customer= $history_customers[$period_entry]->groupBy('email_id');
                            if(count($customer) > 0) {
                                foreach ($customer as $cus) {
                                    if($cus[0]->backup_end_time != NULL) {
                                        $period_backup = Carbon::createFromFormat('Y-m-d H:i:s', $cus[0]->backup_end_time)->format('Ym');
                                        if ($cus[0]->email_id == $request->solCorreo && $period_entry == $period_backup) {
                                            $periods[$period_entry]['customers_activo_used']+= 1; 
                                            $active = true;
                                            break;
                                        }
                                    }
                                }
                                if (!$active)
                                    $periods[$period_entry]['customers_activo_unused'] += 1;
                            }
                        }
                    }
                }
                else if ($request->solTipo == 2 && $request->solEstado == 1) {
                    if ( $period_entry == $period_realized)
                        $periods[$period_realized]['customers_unsubscribed'] += 1;
                }
            }
            
            foreach ($periods as $period) {
                $reportGeneral = new ReportGeneral();
                $reportGeneral->period   = $period['period'];
                $reportGeneral->customers_subscribed = $period['customers_subscribed'];
                $reportGeneral->customers_active = $period['customers_active'];
                $reportGeneral->customers_activo_used = $period['customers_activo_used'];
                $reportGeneral->customers_activo_unused = $period['customers_activo_unused'];
                $reportGeneral->customers_unsubscribed = $period['customers_unsubscribed'];
                $reportGeneral->company_id = $company->id;
                $reportGeneral->save();
            }

            Log::info(Carbon::now());
            Log::info('Final '.$company->id);

        }

      
    }
   
}