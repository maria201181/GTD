<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <title>Portal Informativo ISC - GTD</title>
    <!-- Bootstrap 4.0-->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Fuentes de iconos-->
    <!--<link href="bower_components/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css" rel="stylesheet">-->
    <!-- Tema personalizado Frontend-->
    <link href="../assets/css/style.css" rel="stylesheet">
  </head>
  <body class="page-w-sidemenu">
    <nav class="navbar">
      <div class="container-fluid"><a class="navbar-brand col-sm-3 col-md-2" href="index.html"><img src="../images/logo-portal.svg" alt="logo portal informativo"></a>
        <ul class="nav justify-content-end">
           @if (Auth::guest())
                <li class="nav-item"> <a href="{{ url('/login') }}" class="nav-link" >Login</a></li>           
           @else
                <li class="nav-item"><a class="nav-link" href="{{ url('/account')}}" id="account" >Mi cuenta </a></li>
                <li class="nav-item"><a class="nav-link"  href="{{ url('/logout') }}" id="logout">Logout</a></li>
           @endif
        </ul>
      </div>
    </nav>
    
      
        @yield('content')
      
    
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/app.js"></script>    
    <script src="../assets/js/bootbox.min.js"></script>    
    @yield('scripts')    
  </body>
</html>