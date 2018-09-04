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
        
      </div>
    </div>
    
@endsection

@section('scripts')   
  <!-- Canvas JS-->    
  <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
  <script src="assets/js/home.js"></script>
@endsection





