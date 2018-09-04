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
        $credentials_api = array (
                                array('user' => "druvagtdfull@isc.cl",
                                      'password'=> '557-e3d13d740ff113481c197337ace58ed6cdb01ca01423b977af24a0d7435fbd83',
                                      'company' => 'GTD',
                                      'plan' => 'FULL'),
                                array('user' => "druvatelsurfull@isc.cl",
                                      'password'=> '556-d0f07f4688e0ffe5aeacbeb873ccc5a8cb818f35c87e0ba0f19b5073ffa607c0',
                                      'company' => 'TELSUR',
                                      'plan' => 'FULL')
                                );

        
        


        foreach ($credentials_api as $credential) {
            
            $customers = $this->loadDataApi($credential->user, $credential->password, 'users');
            
            $this->updateCustomers($customers, $credential->company, $credential->plan);  

            $devices = $this->loadDataApi($credential->user, $credential->password, 'devices');

            $first_backups = $this->loadDataApi($credential->user, $credential->password, 'firstbackupdetails');

            $last_backups = $this->loadDataApi($credential->user, $credential->password, 'lastbackupdetails');        

            $this->updateDevices($devices,  $first_backups, $last_backups);        
        }
       
    }

    public function loadDataApi($username, $password, $report) {
        $client = new Client(); //GuzzleHttp\Client
        $response = $client->request('GET', 'https://insyncapi-cloud.druva.com/api/reports/v2/'.$report, [
            'auth' => [
                $username,
                $password
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true)['data'];
    }

    public function updateCustomers($customers, $company, $plan) {
        $company = Company::where('name', $company)->first();
        foreach ($customers as $key => $array) {            
            $customer = Customer::where('email_id',$array['email_id'])->first();
            if (empty($customer)) {                
                $customer = new Customer();
                $customer->company_id = $company->id;
                $customer->plan = $plan;
                $rut = DB::connection('pgsql')
                                ->table('solicitud')
                                ->select('solRut','solIngreso', 'solRealizado')
                                ->where('solCorreo', '=', strtolower($array['email_id']))
                                ->where('solTipo', '=', 1)
                                ->where('solEstado', '=', 1)
                                ->first();
                if (count($rut) > 0) {                         
                    $customer->rut = $rut->solRut;
                    $customer->date_subscribed = $rut->solIngreso;
                    $customer->date_actived = $rut->solRealizado;
                }                   

                foreach ($array as $field => $value) {
                    if (Schema::hasColumn('customers', $field)) {
                        $customer[$field] = $value;
                    }
                }
                if (strtotime($array['added_on']))
                    $customer->added_on = Carbon::createFromFormat('m/d/y H:i', $array['added_on'])->format('Y-m-d H:i:s');
                $customer->save();
            }
            $history_customer = new HistoryCustomer();
            $history_customer->customer_id = $customer->id;
            $history_customer->user_status = $array['user_status'];
            $history_customer->total_usage = $array['total_usage'];
            $history_customer->allocated_quota = $array['allocated_quota'];
            $history_customer->save();
        }
    }

    public function updateDevices($devices, $first_backups, $last_backups) {
        foreach ($devices as $key => $array) {
            $customer = Customer::where('email_id',$array['email_id'])->first();            
            if (!empty($customer)) { 
                $device =  Device::where('customer_id', $customer->id)->where('device_name', $array['device_name'])->first();
                if (empty($device)) {                
                    $device = new Device();
                    $device->customer_id = $customer->id;
                    foreach ($array as $field => $value) {
                        if (Schema::hasColumn('devices', $field)) {                      
                            $device[$field] = $value;
                        }
                    }
                    if (strtotime($array['added_on']))
                        $device->added_on = Carbon::createFromFormat('m/d/y H:i', $array['added_on'])->format('Y-m-d H:i:s');
                    $device->save(); 
                }
            }
            $history_device = new HistoryDevice();
            $history_device->device_id = $device->id;
            $history_device->system_settings_backed_up = $array['system_settings_backed_up'];
            foreach ($first_backups as $data_first_backup) {
                if ($data_first_backup['device_id'] == $array['device_id']) {
                    $history_device->first_backup_status = $data_first_backup['first_backup_status'];
                    $history_device->first_backup_size = $data_first_backup['first_backup_size'];
                    $history_device->time_taken = $data_first_backup['time_taken'];
                }
            }
            foreach ($last_backups as $data_last_backup) {
                if ($data_last_backup['device_id'] == $array['device_id']) {
                    $history_device->last_backup_status = $data_last_backup['last_backup_status'];
                    if (strtotime($data_last_backup['backup_start_time']))
                        $history_device->backup_start_time = Carbon::createFromFormat('m/d/y H:i', $data_last_backup['backup_start_time'])->format('Y-m-d H:i:s');
                    if (strtotime($data_last_backup['backup_end_time']))
                        $history_device->backup_end_time = Carbon::createFromFormat('m/d/y H:i',$data_last_backup['backup_end_time'])->format('Y-m-d H:i:s');
                    $history_device->bytes_transferred = $data_last_backup['bytes_transferred'];
                }
            }
            $history_device->save();
        }
    }


    public function updateReportGeneralPeriodCurrency() {

        $companies = Company::select('id', 'id_intern')->get();

        foreach ($companies as $company) {

            $date_from = Carbon::now()->startOfMonth()->format('Y-m-d');
            $date_to = Carbon::now()->startOfMonth()->format('Y-m-d');

            $period_currency = Carbon::now()->startOfMonth()->format('Ym');

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
                'devices.id as device_id',
                'history_devices.last_backup_status as last_backup_status',
                'history_devices.backup_end_time as backup_end_time',
                'history_devices.created_at as his_created_at')            
            ->leftjoin('devices', function ($join) {
                $join->on('devices.customer_id', '=', 'customers.id');
            })
            ->leftjoin('history_devices', function ($join) use ($date_from, $date_to){
                $join->on('history_devices.device_id', '=', 'devices.id')
                ;                                    
            })                                            
            ->where('customers.company_id', '=', $company->id)
            ->whereRaw("((DATE(history_devices.created_at)>='".$date_from."' AND (DATE(history_devices.created_at)) <= '".$date_to."') OR  history_devices.created_at IS NULL)"   )
            ->orderBy('customers.id')
            ->orderBy('history_devices.created_at', 'desc')
            ->get()
            ;            
            
            
                $periods = array ( $period_currency => array(
                               "period" =>  $dt->format( "Ym" ),                               
                               "customers_subscribed" => 0,
                               "customers_active" => 0,
                               "customers_activo_used" => 0,
                               "customers_activo_unused" => 0,
                               "customers_unsubscribed" => 0));


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

            $reportGeneral = ReportGeneral::where('period', '=', '$period')->where('company_id', '=', $Company->id)->first();
            if (count($reportGeneral) == 0) {
                $reportGeneral = new ReportGeneral();
            }

            $reportGeneral->period   = $period['period'];
            $reportGeneral->customers_subscribed = $period['customers_subscribed'];
            $reportGeneral->customers_active = $period['customers_active'];
            $reportGeneral->customers_activo_used = $period['customers_activo_used'];
            $reportGeneral->customers_activo_unused = $period['customers_activo_unused'];
            $reportGeneral->customers_unsubscribed = $period['customers_unsubscribed'];
            $reportGeneral->company_id = $company->id;
            $reportGeneral->save();

        }

    }


   
}