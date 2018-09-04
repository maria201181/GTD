<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>GTD</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">   

    <!-- Styles -->
    <!-- Bootstrap 4.0-->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tema personalizado Frontend-->
    <link href="assets/css/style.css" rel="stylesheet">
    
</head>
<body class="page-login">

<div class="login-modal-container">
  <div class="login-left">
    <div class="login-image-container"></div>
  </div>
  <div class="login-right">
    <div class="login-form-contianer">
      <div class="login-form-header"><img class="logo" src="images/logo-portal.svg" alt="logo"></div>
      <div class="login-form-body">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
        {{ csrf_field() }}
          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label for="#user-login">Usuario</label>
            <input class="form-control" type="email" id="user-login" name="email" value="{{ old('email') }}">
            @if ($errors->has('email'))
              <span class="help-block">
                <strong class= "error">{{ $errors->first('email') }}</strong>
              </span>
            @endif
          </div>
          <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <label for="#user-password">Contraseña</label>
            <input class="form-control" type="password" id="user-password"  name="password">
            @if ($errors->has('password'))
              <span class="help-block">
                <strong class= "error">{{ $errors->first('password') }}</strong>
              </span>
            @endif
          </div>
          <div class="form-btn-group"><a class="link" href="{{ url('/password/reset') }}">¿Olvido su contraseña?</a>
            <button class="btn btn-basic btn-block" type="submit">Ingresar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="partner-rows">
  <div class="partner-logo"><img src="images/logo-isc.svg" alt="logo-1"></div>
  <div class="partner-logo"><img src="images/logo-gtd.png" alt="logo-2"></div>
  <div class="partner-logo"><img src="images/logo-tls.svg" alt="logo-3"></div>
</div>


<!-- JavaScripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    
</body>
</html>





