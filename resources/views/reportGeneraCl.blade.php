@extends('layouts.app')

@section('content')
    <div class="page-content">
          <div class="content" id="reporte-general">
            <div class="section-title">
              <h1 class="title">Reporte General</h1>
              <div class="bredcrumbs"> <span>Home</span><span>Reportes</span><span class="current">Reporte General</span></div>
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
                          <!-- <div class="col">
                            <label>Seleccione Mes</label>
                            <select class="form-control" name="month">
                              <option value=''>Seleccione un Mes</option>
                              <option value='01'>Enero</option>                              
                              <option value='02'>Febrero</option>
                              <option value='03'>Marzo</option>
                              <option value='04'>Abril</option>
                              <option value='05'>Mayo</option>
                              <option value='06'>Junio</option>
                              <option value='07'>Julio</option>
                              <option value='08'>Agosto</option>
                              <option value='09'>Septiembre</option>
                              <option value='10'>Octubre</option>
                              <option value='11'>Noviembre</option>
                              <option value='12'>Diciembre</option>
                            </select>
                          </div> -->
                          <div class="col">
                            <label> Seleccione Año</label>
                            <select class="form-control" name="filter[year]">                              
                              <option value="">Seleccione un año</option>
                              @foreach($years_available as $year)
                                <option value={{$year}}>{{$year}}</option>
                              @endforeach
                            </select>
                          </div>
                          <div class="col">
                            <label>Empresa</label>
                            <label>Empresa</label>
                            {!! Form::select('filter[company_id]', array('' => 'Selecciona una Opción') + $companies, Input::get('filter')['company_id'], array('class' => 'form-control')) !!}
                          </div>
                          <div class="col btn-col">
                            <button class="btn btn-default btn-basic btn-psudolabel">Buscar</button>
                            <button class="btn btn-default btn-clear btn-psudolabel">Limpiar</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="section-table">
              <section class="tables-horizantales">
                
                 @foreach($report as $item)
                  <div class="card-group">
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{item->period}}</h5>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{item->customers_subscribed}}</h5>
                        <p class="card-text">Clientes vendidos</p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{item->customers_active}}</h5>
                        <p class="card-text">Clientes Activos</p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{item->customers_activo_used}}</h5>
                        <p class="card-text">Clientes Respaldando</p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{item->period}}</h5>
                        <p class="card-text">Tasa Clientes Activos</p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">20</h5>
                        <p class="card-text">Tasa Clientes Respaldando </p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{item->customers_activo_unused}}</h5>
                        <p class="card-text">Clientes Sin Uso</p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{item->customers_unsubscribed}}</h5>
                        <p class="card-text">Bajas</p>
                      </div>
                    </div>
                  </div>
                @endforeach
              </section>  


              <!-- <section class="row">  
                  <div  class="col-md-2">
                     <div class="card-group-vertical">
                      <div class="card-vertical">
                        <div class="card-body-vertical">
                          <h5 class="card-title-vertical">Febrero</h5>
                        </div>
                      </div>
                      <div class="card-vertical">
                        <div class="card-body-vertical">
                          <h5 class="card-title-vertical">1988</h5>
                          <p class="card-text-vertical">Clientes vendidos</p>
                        </div>
                      </div>
                      <div class="card-vertical">
                        <div class="card-body-vertical">
                          <h5 class="card-title">201</h5>
                          <p class="card-text">Clintes Activos</p>
                        </div>
                      </div>
                      <div class="card-vertical">
                        <div class="card-body-vertical">
                          <h5 class="card-title-vertical">201</h5>
                          <p class="card-text-vertical">Clientes Activos con Uso</p>
                        </div>
                      </div>
                      <div class="card-vertical">
                        <div class="card-body-vertical">
                          <h5 class="card-title-vertical">5%</h5>
                          <p class="card-text-vertical">% Clientes Activos</p>
                        </div>
                      </div>
                      <div class="card-vertical">
                        <div class="card-body-vertical">
                          <h5 class="card-title-vertical">3%</h5>
                          <p class="card-text-vertical">% Clientes Activos con Uso </p>
                        </div>
                      </div>
                      <div class="card-vertical">
                        <div class="card-body-vertical">
                          <h5 class="card-title-vertical">3%</h5>
                          <p class="card-text-vertical">% Clientes Activos sin Uso </p>
                        </div>
                      </div>
                      <div class="card-vertical">
                        <div class="card-body-vertical">
                          <h5 class="card-title-vertical">20</h5>
                          <p class="card-text-vertical">Bajas</p>
                        </div>
                      </div>                      
                    </div>
                  </div>        
              </section> -->
            </div>
          </div>
        </div>
@endsection