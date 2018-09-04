@extends('layouts.app')

@section('content')
  <div class="page-content">
    <div class="content" id="mantenedor-empresas">
      <div class="section-title">
        <h1 class="title">Mantenedor Empresas</h1>
        <div class="bredcrumbs"> <span>Home</span><span>Mantenedor</span><span class="current">Mantenedor Empresas</span></div>
      </div>
      <div class="section-filter">
        <a class="btn btn-basic open_modal_new" href= "#" style="margin-bottom:20px;" >Nueva Empresa</a>
        <div class="blue-acordion" id="accordion-filter">
          <div class="card">
            <div class="card-header" id="filterHeader">
              <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#filterBody" aria-expanded="true" aria-controls="filterBody"> <span>Filtros</span><i class="fas fa-caret-down"></i></button>
            </div>
            <div class="collapse show" id="filterBody" aria-labelledby="filterHeader" data-parent="#accordion-filter">
              <div class="card-body">
                {!! Form::open(['method' => 'GET', 'id' => 'filter-form']) !!}
                  <div class="form-row">
                    <div class="col">
                      <label>Nombre de Empresa</label>
                      <input class="form-control" type="text" name="filter[name]" value="{{Input::get('filter')['name']}}">
                    </div>
                    <div class="col">
                      <label>Estado</label>
                      {!! Form::select('filter[status]', array('' => 'Seleccione un Estado') + array('1'=>'Activo','0'=>'Inactivo'), Input::get('filter')['status'], array('class' => 'form-control'))!!}
                    </div>
                    <div class="col btn-col">
                      <button type = "submit" class="btn btn-default btn-basic btn-psudolabel">Buscar</button>
                      <button class="btn btn-default btn-clear btn-psudolabel">Limpiar</button>
                    </div>
                  </div>
                 {!! Form::close() !!} 
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="section-table">
        <h3 class="title">Empresas registradas</h3>
        <div class="table-responsive">
          <table class="table table-striped table-hover table-sistema" id="tableEmpresasRegistradas">
            <thead>
              <tr> 
                <th>RUT</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Teléfono Contacto</th>
                <th>Nombre Contacto</th>
                <th>Opciones</th>
              </tr>
            </thead>
            <tbody>                   
              @if (count($companies)>0)
                @foreach($companies as $item)
                <tr>
                    <td>{{$item->rut.'-'.$item->dv}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->status ? 'Activo' : 'Inactivo'}}</td>
                    <td>{{$item->contact_phone}}</td>
                    <td>{{$item->contact_name}}</td>
                    <td>                              
                      <button class="btn btn-link open_modal_edit" type="button" url="company/{{$item->id}}/edit">Editar</button>
                      @if($item->status == 1)
                        <button class="btn btn-link disabledCompany" type="button" url="company/disabled/{{$item->id}}">Deshabilitar </button>
                      @else
                        <button class="btn btn-link disabledCompany" type="button" url="company/disabled/{{$item->id}}">Habilitar</button>  
                      @endif        
                      
                    </td>                          
                </tr>
                @endforeach
              @else
                <tr>
                    <td colspan="6" class="text-center"> No se encontraron resultados.</td>
                </tr>
              @endif                     
            </tbody>
          </table>
        </div>
        {!! $companies->appends(['filter' => Input::get('filter')])->render() !!}
      </div>
    </div>
  </div>
  <div class="modal fade bd-example-modal-lg" id="modalCompany" tabindex="-1" role="dialog" aria-labelledby="nuevoUsuario" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">      
      <div class="modal-content modal-user-info">
        <div class="modal-body">
          <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"> &times;</span></button>
          <section class="section-nueva-empresa">
            <h3 class="modal-title">Nueva Empresa</h3>
              {!! Form::open(['route' => ['company.store'], 'method' => 'POST']) !!}
              <input type="hidden" name = "id" type="text">
              <div class="row">
                <div class="col-lg-4 form-group">
                  <label>Rut</label>
                  <div class="input-group">
                    <input class="form-control required number" name="rut" type="text" maxlength="8">
                    <div class="input-group-append asterisk">*</div> 
                  </div>
                  <span class="help-block">
                      <strong class="error" id="rut_error"></strong>
                   </span>
                </div>
                <div class="col-lg-2 form-group">
                  <label>DV</label>
                  <div class="input-group">
                    <input class="form-control required" name="dv" type="text" maxlength="1">
                    <div class="input-group-append asterisk">*</div> 
                  </div>
                  <span class="help-block">
                      <strong class="error" id="dv_error"></strong>
                  </span>
                </div>

              </div>
              <div class="row">
                <div class="col-lg-4 form-group">
                  <label>Nombre</label>
                  <div class="input-group">
                    <input class="form-control required" name="name" type="text">
                    <div class="input-group-append asterisk">*</div> 
                  </div>
                  <span class="help-block">
                      <strong class="error" id="name_error"></strong>
                  </span>
                </div>
                <div class="col-lg-4 form-group">
                  <label>Teléfono de Contacto</label>
                  <div class="input-group">
                    <input class="form-control required" name="contact_phone" type="text">
                    <div class="input-group-append asterisk">*</div>
                  </div>
                  <span class="help-block">
                      <strong class="error" id="contact_phone_error"></strong>
                  </span>
                </div>
                <div class="col-lg-4 form-group">
                  <label>Nombre de Contacto</label>
                  <div class="input-group">
                    <input class="form-control required" name="contact_name" type="text">
                    <div class="input-group-append asterisk">*</div>
                  </div>                  
                  <span class="help-block">
                      <strong class="error" id="contact_name_error"></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <h6>Estado</h6>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="inlineRadio1" value="1" checked="checked">
                    <label class="form-check-label" for="inlineRadio1">Activo</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" id="inlineRadio2" value="0">
                    <label class="form-check-label" for="inlineRadio2">Inactivo</label>
                  </div>
                  <div class="form-check form-check-inline asterisk"> *
                  </div>  
                </div>
              </div>
              {!! Form::close() !!}              
          </section>
        </div>
        <div class="modal-footer">
          <button class="btn btn-basic btn-save">Guardar</button>
          <button class="btn btn-clear">Limpiar</button>
        </div>
      </div>      
    </div>
  </div>
@endsection

@section('scripts')
  <script src= "assets/js/company.js"></script>
@endsection