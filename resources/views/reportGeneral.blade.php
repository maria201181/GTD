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
                        
                          <div class="col">
                            <label> Seleccione Año</label>
                            {!! Form::select('filter[year]', array('' => 'Selecciona una Opción') + $years_available, Input::get('filter')['year'], array('class' => 'form-control')) !!}
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
                
                @if (count($report)>0)
                 @foreach($report as $item)
                  <div class="card-group">
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{$month[substr($item->period, 4, 2)]}} </h5>
                        <h5 class="card-title">{{substr($item->period, 0, 4)}} </h5>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{$item->customers_subscribed}}</h5>
                        <p class="card-text">Clientes vendidos</p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{$item->customers_active}}</h5>
                        <p class="card-text">Clientes Activos</p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{$item->customers_activo_used}}</h5>
                        <p class="card-text">Clientes Respaldando</p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{round(($item->customers_active*100)/$item->customers_subscribed,2)}}%</h5>
                        <p class="card-text">Tasa Clientes Activos</p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{round(($item->customers_activo_used*100)/$item->customers_active, 2)}}%</h5>
                        <p class="card-text">Tasa Clientes Respaldando </p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{$item->customers_activo_unused}}</h5>
                        <p class="card-text">Clientes Sin Uso</p>
                      </div>
                    </div>
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{$item->customers_unsubscribed}}</h5>
                        <p class="card-text">Bajas</p>
                      </div>
                    </div>
                  </div>
                @endforeach
                @else
                <div>
                     No se encontraron resultados.
                </div>
              @endif
          
              </section>  


             
            </div>
          </div>
        </div>
@endsection

@section('scripts')     
  <script src="assets/js/reportGeneral.js"></script>
@endsection