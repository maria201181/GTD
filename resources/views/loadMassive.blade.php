@extends('layouts.app')

@section('content')
 <div class="page-content">
    <div class="content" id="mantenedor-usuarios">
      <div class="section-title">
        <h1 class="title">Carga Masiva</h1>
        <div class="bredcrumbs"> <span>Home</span><span>Mantenedor</span><span class="current">Carga Masiva</span></div>
      </div>
       <div class="card">
          <div class="card-header card-header-title">
             <h5>Carga Masiva</h5>
          </div>
          
            <div class="card-body">
              <section class="section-nuevo-usuario">
                {!! Form::open(['method' => 'POST',  'files' => true, 'class'=>'loader']) !!} 
                  <div class= "loadingInactive" id="loading">
                    <img src="images/loading.gif" id="loading" style="padding-top: 50px;" />
                  </div>               
                  <div class="row">                
                    <div class="col-lg-6 form-group">
                      <label>Ingrese Fecha</label>
                      <input class="form-control" type="date" id = "date" name="date"  value="{{ Input::get('date') }}" >
                      <span class="help-block">
                        <strong class="error" id="date_error"></strong>
                      </span>
                    </div>

                  </div>
                  <div class="row">    
                    <div class="col-lg-6 form-group">
                      <label>Empresa</label>
                      {!! Form::select('company_id', array('' => 'Seleccione una Empresa') +  $companies, Input::get('company_id') , array('class' => 'form-control','id' => 'company_id'))!!}
                      <span class="help-block">
                        <strong class="error" id="company_id_error"></strong>
                      </span>
                    </div>
                  </div>

                  <div class="row">    
                    <div class="col-lg-6 form-group">
                      <label>Plan</label>
                     {!! Form::select('plan', array('' => 'Seleccione una Opción') + array('FULL'=>'FULL','BÁSICO'=>'BÁSICO'), Input::get('plan'), array('class' => 'form-control', 'id'=> 'plan')) !!}
                      <span class="help-block">
                        <strong class="error" id="plan_error"></strong>
                      </span>
                    </div>
                  </div>
                 
                  <div class="row">
                    <label class="fileContainer">
                          Selecione un Archivo
                        <input type="file" id= "csvfile" name="csvfile"/>
                    </label>                          
                    <label class="fileName"> Ningún archivo Seleccionado </label>
                    <span class="help-block col-lg-12">
                      <strong class="error" id="csvfile_error"></strong>
                    </span>

                  </div>

                  <div class="row">
                    <div class="col-lg-6" style="text-align: right;">
                      <button type= "submit" class="btn btn-default btn-basic btn-psudolabel">Cargar</button>
                      <button class="btn btn-default btn-clear btn-psudolabel">Limpiar</button>
                    </div>
                  </div>
                  {!! Form::close() !!}
              </section>
            </div>
          
        </div>
  </div>
</div>

      
@endsection

@section('scripts')
  <script src= "assets/js/loadMassive.js"></script>
@endsection


