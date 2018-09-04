<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <title>Portal Informativo ISC - GTD</title>
    <!-- Bootstrap 4.0-->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Fuentes de iconos-->
    <!--<link href="bower_components/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css" rel="stylesheet">-->
    <!-- Tema personalizado Frontend-->
    <link href="assets/css/style.css" rel="stylesheet">
  </head>
  <body class="page-w-sidemenu">
    <nav class="navbar">
      <div class="container-fluid"><a class="navbar-brand col-sm-3 col-md-2" href="index.html"><img src="images/logo-portal.svg" alt="logo portal informativo"></a>
        <ul class="nav justify-content-end">
          <li class="nav-item"><a class="nav-link" href="{{ url('/account')}}" id="account" >Mi cuenta </a></li>
          <li class="nav-item"><a class="nav-link"  href="{{ url('/logout') }}" id="logout">Logout</a></li>          
        </ul>
      </div>
    </nav>
    <div class="content-wrap">
      <div class="right-col">
        <div id="sidenav-container">
          <div class="accordion" id="accordion-sidenav">
            <div class="card">
              <div class="card-header" id="headingOne">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> <span>Reportes</span><i class="fas fa-caret-down"></i></button>
              </div>
              <div class="collapse" id="collapseOne" aria-labelledby="headingOne" data-parent="#accordion-sidenav">
                <div class="card-body">
                  <ul class="nav">
                    <li class="nav-item"><a class="nav-link" id ="home" href="{{ url('/home') }}" >Reporte Usabilidad</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/reportGeneral')}}"  id ="reportGeneral">Reporte general</a></li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="card">
              <div class="card-header" id="headingTwo">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"> <span>Mantenedor</span><i class="fas fa-caret-down"></i></button>
              </div>
              <div class="collapse" id="collapseTwo" aria-labelledby="headingTwo" data-parent="#accordion-sidenav">
                <div class="card-body">
                  <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/user')}}" id="user">Mantenedor de usuarios</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/company')}}"  id="company">Mantenedor de empresa</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/loadMassive') }}" id="loadMassive">Carga Masiva</a></li>
                  </ul>
                </div>
              </div>
            </div>            
          </div>
        </div>
      </div>
      <div class="left-col">
        @yield('content')
      </div>
    </div>             
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/app.js"></script>    
    <script src="assets/js/bootbox.min.js"></script>    
    @yield('scripts')    
  </body>
</html>