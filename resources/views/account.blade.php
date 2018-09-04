@extends('layouts.app')

@section('content')
 <div class="page-content">
    <div class="content" id="mantenedor-usuarios">
      
       <div class="card">
          <div class="card-header card-header-title">
             Datos de la Cuenta 
          </div>
          <div class="card-body" style="padding-left: 50px;">
              <div class="row">
                <div class="col-sm-6">
                  <div class="row">
                    <h5 class="dataAccount" >{{$user->name}} {{$user->surname}} {{$user->second_surname}}</h5>
                  </div>                                        
                  <div class="row">                                    
                    <h5 class="dataAccount">{{$user->company->name}}</h5>
                  </div>
                  <div class="row">                                    
                    <h5 class="dataAccount">{{$user->profile->name}}</h5>
                  </div>
                  <div class="row">
                    <a type= "submit" class="btn btn-default btn-basic btn-psudolabel" href="{{ url('/password/reset') }}" >Resetear Contraseña</> </a>
                  </div>
                </div>
                <div class="col-sm-6">
                 <!--  <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Email</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-envelope"></i> Enviar Email
                                </button>
                            </div>
                        </div>
                    </form>                   -->
                    
                </div>  
              </div>
          </div>  
      </div>      
  </div>
</div>
      
@endsection

@section('scripts')  
@endsection


