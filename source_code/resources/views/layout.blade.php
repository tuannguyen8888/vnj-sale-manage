
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">           
    <title>:: {{CRUDBooster::getSetting('appname') }} ::</title>
    <!-- Bootstrap core CSS -->
    <link href="{{asset('vendor/crudbooster/assets/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('vendor/crudbooster/assets/select2/dist/css/select2.min.css')}}" />
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
      .navbar {
        background: #00a65a;
      }
      .navbar a {
        color: #ffffff;
      }
      .nav>li>a:hover, .nav>li>a:focus {
        background: #00a65a;
      }
      .toolbar-bottom {
  background: #006d3a;
  border-top: 1px solid #dddddd;
  position: fixed;
  bottom: 0px;
  left: 0px;
  width: 100%;
  /*box-shadow: 0px -3px 5px #eeeeee;*/
}
.toolbar-bottom .content {
  padding: 5px 15px 5px 15px;
  font-size: 12px;
  color: #ffffff;
  text-align: right;
}
    </style>
    @stack('head')
  </head>

  <body>

    <nav class="navbar navbar-static-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="{{url('/')}}">{{CRUDBooster::getSetting('appname') }}</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="{{url('/')}}"><i class="glyphicon glyphicon-dashboard"></i> POS</a></li>
            <li><a href="{{url('admin')}}"><i class="glyphicon glyphicon-dashboard"></i> Admin Area</a></li>
                        
          </ul>
          <ul class="nav navbar-nav navbar-right">
              @if(Session::has('employees_id'))
                <li><a href="{{url('logout')}}"><i class="glyphicon glyphicon-off"></i> Sign Out</a></li>
              @endif
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container-fluid">
		@yield('content')
    </div><!-- /.container -->
    <div class="toolbar-bottom">
  <div class="content">
    <span id="date_time"></span> &nbsp;&bull;&nbsp; Powered By CB POS<sup>Lite</sup>  
  </div>
  <!-- /.content -->
</div>
<!-- /.toolbar-bottom -->
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="{{asset('vendor/crudbooster/assets/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('vendor/crudbooster/assets/select2/dist/js/select2.min.js')}}"></script>
    <script src="{{asset('vendor/crudbooster/jquery.price_format.2.0.min.js')}}"></script> 
    <script src="{{asset('js/notify.min.js')}}"></script> 
    <script>
      $(function() {
        $('.select2').select2();
        date_time('date_time');
      })
      function date_time(id)
{
        date = new Date;
        year = date.getFullYear();
        month = date.getMonth();
        months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'Jully', 'August', 'September', 'October', 'November', 'December');
        d = date.getDate();
        day = date.getDay();
        days = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        h = date.getHours();
        if(h<10)
        {
                h = "0"+h;
        }
        m = date.getMinutes();
        if(m<10)
        {
                m = "0"+m;
        }
        s = date.getSeconds();
        if(s<10)
        {
                s = "0"+s;
        }
        result = ''+days[day]+' '+months[month]+' '+d+' '+year+' '+h+':'+m+':'+s;
        document.getElementById(id).innerHTML = result;
        setTimeout('date_time("'+id+'");','1000');
        return true;
}
    </script>
    @stack('bottom')
  </body>
</html>
