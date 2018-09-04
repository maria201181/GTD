@extends('layouts.app')

@section('content')
    <div class="page-content">
      <div class="content" id="reporte-uso">
        <div class="section-title">
          <h1 class="title">Reporte de Usabilidad del Sistema</h1>
          <div class="bredcrumbs"> <span>Home</span><span>Reportes</span><span class="current">Reporte de Usabilidad del Sistema</span></div>
        </div>
        <div class="section-filter">
          <div class="blue-acordion" id="accordion-filter">
            <div class="card">
              <div class="card-header" id="filterHeader">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#filterBody" aria-expanded="true" aria-controls="filterBody"> <span>Filtros</span><i class="fas fa-caret-down"></i></button>
              </div>
              <div class="collapse show" id="filterBody" aria-labelledby="filterHeader" data-parent="#accordion-filter">
                <div class="card-body">
                  <form action="">
                    <div class="form-row">
                      <!---<div class="col">
                        <label>Fecha Desde</label>
                        <input class="form-control" type="date" id = "date_from" name="filter[date_from]"  value="{{ Input::get('filter')['date_from'] }}">
                       </div>
                      <div class="col">
                        <label>Fecha Hasta</label>
                        <input class="form-control" type="date" id = "date_to" name="filter[date_to]"  value="{{ Input::get('filter')['date_to'] }}">
                      </div>-->
                      <div class="col">
                        <label>Empresa</label>
                        {!! Form::select('filter[company_id]', array('' => 'Selecciona una Opción') + $companies, Input::get('filter')['company_id'], array('class' => 'form-control')) !!}
                      </div>
                      <div class="col btn-col">
                        <button class="btn btn-default btn-basic btn-psudolabel">Buscar</button>
                        <button class="btn btn-default btn-clear btn-psudolabel">Limpiar</button>
                      </div>
                      <div class="col download">
                          <img src="images/icon-excel.png">
                          <a href="{{url('reportExcel')}}"> Descargar Reporte </a>
                      </div>
                    </div>
                     <span class="help-block">
                      <strong class="error" id="date_error"></strong>
                    </span>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="section-pai-graph">
          <div class="row">            
            <div class="col-md-8" style="text-align: center">              
              <img src="images/loading.gif" id="loading" style="padding-top: 50px;" />
              <div id="chartContainer">
              </div>
            </div>
            <div class="col-md-4">
              <div class="card card-info card-border-all">
                <div class="card-body">
                  <h5 class="bold" id="total_users"></h5>
                  <p>Total de usuarios</p>
                </div>
              </div>
              <div class="card card-info card-border-active-used">
                <div class="card-body">
                  <h5 class="bold" id="total_users_active"></h5>
                  <p>Total Usuarios Activos </p>
                </div>
              </div>
              <!--<div class="card card-info card-border-active-unused">
                <div class="card-body">
                  <h5 class="bold" id="total_users_active_unused"></h5>
                  <p>Activos y no usan productos</p>
                </div>
              </div>-->
              <div class="card card-info card-border-inactive">
                <div class="card-body">
                  <h5 class="bold" id="total_users_unused"></h5>
                  <p> Total Usuarios no usan el Servicio  </p>
                </div>
              </div>
              <div class="card card-info card-border-unsuscribe">
                <div class="card-body">
                  <h5 class="bold" id="total_users_unsuscribe"></h5>
                  <p>Bajas</p>
                </div>
              </div>
              
            </div>
          </div>
        </div>
        <!--<div class="section-table">
          <div class="form-row">
            <div class="col download">
              <img src="images/icon-excel.png">
              <a href="{{url('reportExcel')}}"> Descargar Reporte </a>
            </div>
          </div>
          <div class="blue-acordion" id="accordion-table">
            <div class="card">
              <div class="card-header" id="tableACHeader">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#tableACbody" aria-expanded="true" aria-controls="tableACbody"> <span>Detalle de registros</span><i class="fas fa-caret-down"></i></button>
              </div>
              <div class="collapse show" id="tableACbody" aria-labelledby="tableACHeader" data-parent="#accordion-table">
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped table-hover table-sistema" id="table-usuarios">
                      <thead>
                        <tr>                           
                          <th>Nombre</th>
                          <th>Email</th>
                          <th>Empresa</th>
                          <th>Plan</th>
                          <th>Estado </th>
                          <th>Cantidad de dispositivos</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                            @if (count($report)>0)
                              @foreach($report as $item)
                              <tr>
                                  <td>{{$item->customer->user_name}}</td>                         
                                  <td>{{$item->customer->email_id}}</td>
                                  <td>{{$item->customer->company->name}}</td>
                                  <td>{{$item->customer->plan}}</td>
                                  <td>{{$item->status}}</td>
                                  <td> <a href="#" data-toggle="modal" data-target="">{{$item->number_devices}}</a></td>
                              </tr>
                              @endforeach
                            @else
                              <tr>
                                  <td colspan="6" class="text-center"> No se encontraron resultados.</td>
                              </tr>
                            @endif 
                        </tr>                        
                      </tbody>
                    </table>
                  </div> 
                  @if (count($report)>0)                
                    {!! $report->appends(['filter' => Input::get('filter')])->render() !!}                  
                  @endif   
                </div>
              </div>
            </div>
          </div>
        </div>-->
      </div>
    </div>

    <!-- -modal-->
    <!--<div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-user-info">
          <div class="modal-body">
            <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"> &times;</span></button>
            <section class="modal-user-detail">
              <div class="row">
                <div class="col"> 
                  <h3 class="modal-title">Detalle de dispositivos usuario</h3>
                </div>
              </div>
              <div class="row card-row">
                <div class="col">
                  <div class="card">
                    <div class="card-body"><span class="p-icon-icon-user"></span>
                      <h5 class="card-title">Marcelo Torres Sandoval</h5>
                      <p class="card-text">Activo y no usa producto</p>
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="card">
                    <div class="card-body"><span class="p-icon-icon-email"></span>
                      <h5 class="card-title">mtorres@gmail.com</h5>
                      <p class="card-text">Correo</p>
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="card">
                    <div class="card-body"><span class="p-icon-icon-devices"></span>
                      <h5 class="card-title">5 </h5>
                      <p class="card-text">Disposistivos activos</p>
                    </div>
                  </div>
                </div>
              </div>
            </section>
            <section class="modal-device-detail">
              <div class="row"> 
                <div class="col"> 
                  <h3 class="modal-title">Detalle de Dispositivos</h3>
                </div>
              </div>
              <div class="row card-row">
                <div class="col-md-4">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">iMac de ARKU</h5>
                      <p class="card-text">última Actividad: 15/05/2018 09:12</p>
                      <p class="card-text bold">2GB</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">SM-G930F</h5>
                      <p class="card-text">última Actividad: 15/05/2018 09:12</p>
                      <p class="card-text bold">2GB</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">Desktop-SU5E9FS</h5>
                      <p class="card-text">última Actividad: 15/05/2018 09:12</p>
                      <p class="card-text bold">2GB</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">iMac de ARKU</h5>
                      <p class="card-text">última Actividad: 15/05/2018 09:12</p>
                      <p class="card-text bold">2GB</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">SM-G930F</h5>
                      <p class="card-text">última Actividad: 15/05/2018 09:12</p>
                      <p class="card-text bold">2GB</p>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>-->
@endsection

@section('scripts')   
  <!-- Canvas JS-->    
  <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
  <script src="assets/js/home.js"></script>
@endsection





