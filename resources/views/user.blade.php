@extends('layouts.app')

@section('content')
 <div class="page-content">
    <div class="content" id="mantenedor-usuarios">
      <div class="section-title">
        <h1 class="title">Mantenedor Usuarios</h1>
        <div class="bredcrumbs"> <span>Home</span><span>Mantenedor</span><span class="current">Mantenedor Usuarios</span></div>
      </div>
      <div class="section-filter"><a class="btn btn-basic open_modal_new" style="margin-bottom:20px;" href="#" data-toggle="modal" data-target="#nuevoUsuario">Nuevo usuario</a>
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
                      <label>RUT</label>
                      <input class="form-control" type="text" name="filter[rut]" value="{{Input::get('filter')['rut']}}">
                    </div>
                    <div class="col">
                      <label>Nombre</label>
                      <input class="form-control" type="text" name="filter[name]" value="{{Input::get('filter')['name']}}">
                    </div>
                    <div class="col">
                      <label>Empresa</label>
                      {!! Form::select('filter[company_id]', array('' => 'Selecciona una Opción') + $companies, Input::get('filter')['company_id'], array('class' => 'form-control')) !!}
                    </div>
                    <div class="col">
                      <label>Estado</label>
                      {!! Form::select('filter[status]', array('' => 'Seleccione una Opción') + array('1'=>'Activo','0'=>'Inactivo'), Input::get('filter')['status'], array('class' => 'form-control')) !!}
                    </div>
                    <div class="col btn-col">
                      <button type="submit" class="btn btn-default btn-basic btn-psudolabel">Buscar</button>
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
        <h3 class="title">Usuarios registrados</h3>
        <div class="table-responsive">
          <table class="table table-striped table-hover table-sistema" id="tableUsuariosRegistrados">
            <thead>
              <tr> 
                <th>RUT</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Empresa</th>
                <th>Perfil</th>
                <th>Opciones </th>
              </tr>
            </thead>
            <tbody>
              <tr>                      
              @if (count($users)>0)
                @foreach($users as $item)
                <tr>
                    <td>{{$item->rut.'-'.$item->dv }}</td>
                    <td>{{$item->name.' '.$item->surname.' '.$item->second_surname}}</td>                         
                    <td>{{$item->email}}</td>
                    <td>{{$item->company->name}}</td>
                    <td>{{$item->profile->name}}</td>
                    <td>                              
                      <button class="btn btn-link open_modal_edit" type="button" url="user/{{$item->id}}/edit" >Editar</button>
                      @if($item->status == 1)
                        <button class="btn btn-link disabledUser" type="button" url="user/disabled/{{$item->id}}" >Deshabilitar</button>
                      @else
                        <button class="btn btn-link disabledUser" type="button" url="user/disabled/{{$item->id}}" >Habilitar</button>
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
        {!! $users->appends(['filter' => Input::get('filter')])->render() !!}
      </div>
    </div>
  </div>

  <!-- -modal-->
  <div class="modal fade bd-example-modal-lg" id="modalUser" tabindex="-1" role="dialog" aria-labelledby="nuevoUsuario" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content modal-user-info">
        
        <div class="modal-body">
          <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"> &times;</span></button>
          <section class="section-nuevo-usuario">
            <h3 class="modal-title">Nuevo Usuario</h3>
            {!! Form::open(['route' => ['user.store'], 'method' => 'POST']) !!}
            <input type="hidden" name = "id" type="text">
              <div class="row">
                <div class="col-lg-4 form-group">
                  <label>Rut</label>
                  <div class="input-group">
                    <input class="form-control required number noEdit" name="rut" type="text" maxlength="8">
                    <div class="input-group-append asterisk">*</div> 
                  </div>                  
                  <span class="help-block">
                      <strong class="error" id="rut_error"></strong>
                  </span>
                </div>
                <div class="col-lg-2 form-group">
                  <label class="" >DV</label>
                  <div class="input-group">
                    <input class="form-control required noEdit" name="dv" type="text" maxlength="1">
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
                    <input class="form-control required noEdit" name="name" type="text">
                    <div class="input-group-append asterisk">*</div> 
                  </div>
                  <span class="help-block">
                    <strong class="error" id="name_error"></strong>
                  </span>
                </div>
                <div class="col-lg-4 form-group">
                  <label>Apellido Paterno</label>
                  <div class="input-group">
                    <input class="form-control required noEdit" name="surname" type="text">
                    <div class="input-group-append asterisk">*</div> 
                  </div>
                  <span class="help-block">
                    <strong class="error" id="surname_error"></strong>
                  </span>
                </div>
                <div class="col-lg-4 form-group">
                  <label>Apellido Materno</label>
                  <div class="input-group">
                    <input class="form-control required noEdit" name="second_surname" type="text">
                    <div class="input-group-append asterisk">*</div> 
                  </div>                  
                  <span class="help-block">
                    <strong class="error" id="second_surname_error"></strong>
                  </span>
                </div>
                <div class="col-lg-4 form-group">
                  <label>Email</label>
                  <div class="input-group">
                    <input class="form-control required noEdit" name="email" type="email">
                    <div class="input-group-append asterisk">*</div> 
                  </div>
                  <span class="help-block">
                    <strong class="error" id="email_error"></strong>
                  </span>
                </div>
                <div class="col-lg-4 form-group">
                  <label>Empresa</label>
                  <div class="input-group">
                    {!! Form::select('company_id', array('' => 'Seleccione una Empresa') +  $companies, Input::get('company_id') , array('class' => 'form-control required','id' => 'company_id'))!!}
                    <div class="input-group-append asterisk">*</div> 
                  </div>
                  <span class="help-block">
                    <strong class="error" id="company_id_error"></strong>
                  </span>
                </div>
                <div class="col-lg-4 form-group">
                  <label>Perfil</label>
                  <div class="input-group">
                    {!! Form::select('profile_id', array('' => 'Seleccione un Perfil') +  $profiles, Input::get('profile_id') , array('class' => 'form-control required','id' => 'profile_id'))!!}
                    <div class="input-group-append asterisk">*</div> 
                  </div>
                  <span class="help-block">
                    <strong class="error" id="profile_id_error"></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-4">
                  <div class="form-group">
                    <label>Contraseña</label>
                    <div class="input-group">
                      <input class="form-control required" name="password" type="password">
                      <div class="input-group-append asterisk editNoRequired">*</div> 
                    </div>
                    <span class="help-block">
                      <strong class="error" id="password_error"></strong>
                    </span>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="form-group">
                    <label>Repetir Contraseña</label>
                    <div class="input-group">
                      <input class="form-control required" name="password_confirm" type="password">
                      <div class="input-group-append asterisk editNoRequired">*</div> 
                    </div>                    
                    <span class="help-block">
                        <strong class="error" id="password_confirm_error"></strong>
                      </span>
                  </div>
                </div>
                <div class="col-lg-3">
                  <label></label>
                  <div class="form-check" style="padding-top: 10px;">
                    <input type="checkbox" class="form-check-input showPassword">
                    <label class="form-check-label" for="showPassword">Mostrar Contraseña</label>
                  </div>
                </div>


              </div>
              <div class="row">
                <div class="col">
                  <h6>Estado</h6>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" value="1" checked="checked">
                    <label class="form-check-label" for="inlineRadio1">Activo</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" value="0">
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
  <script src= "assets/js/user.js"></script>
@endsection

