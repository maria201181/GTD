<?php
 
namespace App\Console\Commands;
 
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Schema;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Device;
use App\Models\HistoryCustomer;
use App\Models\HistoryDevice;
use Carbon\Carbon;
 
class LoadData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:data';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Carga de los datos de Druva. Una vez por semana';
 
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
        }
      
    }

    public function loadDataApi($username, $password, $report) {
        
    }

   
}