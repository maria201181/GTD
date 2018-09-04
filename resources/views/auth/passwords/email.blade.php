@extends('layouts.app2')

<!-- Main Content -->
@section('content')
<div class="container">
    <div class="row containerReset">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header card-header-title">Resetear Contraseña</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Email</label>

                            <div class="col-md-12">
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
                                <a href="{{ url('/login') }}" class="btn btn-basic">
                                    <i class="fa fa-btn fa-envelope"></i> Volver a Inicio
                                </a>
                            </div>
                             
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
