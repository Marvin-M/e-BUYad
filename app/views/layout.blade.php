<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>@yield('page-title')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- site css -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,800,700,400italic,600italic,700italic,800italic,300italic" rel="stylesheet" type="text/css">
    

   <link rel="shortcut icon" type="image/x-icon" href="/images/logo.png" />

    {{HTML::style('dist/css/site.min.css')}}

    {{HTML::script('js/jquery.min.js')}}
    
    @yield('other-scripts')
    

    <script>
      $(document).ready(function(){
        <?php
          $access = DB::table('tblJobAccess')
                        ->select('tmem','tsale','trelo','tegc','trepo','tquery','tutil','tmaint')
                        ->where('strJobId', Session::get('jobcode'))
                        ->first();

          if(sizeof($access) > 0){
          if($access->tmem == 0){
            echo 'document.getElementById("tmem").className="hidden";';
          }

          if($access->tsale == 0){
            echo 'document.getElementById("tsale").className="hidden";';
          }

          if($access->trelo == 0){
            echo 'document.getElementById("trelo").className="hidden";';
          }

          if($access->tegc == 0){
            echo 'document.getElementById("tegc").className="hidden";';
          }

          if($access->trepo == 0){
            echo 'document.getElementById("trepo").className="hidden";';
          }

          if($access->tquery == 0){
            echo 'document.getElementById("tquery").className="hidden";';
          }

          if($access->tutil == 0){
            echo 'document.getElementById("tutil").className="hidden";';
          }

          if($access->tmaint == 0){
            echo 'document.getElementById("tmaint").className="hidden";';
          }
        }else{
          Redirect::to("/logout");
        }
        ?>
      });
    </script>
    <style>
      body {      
        background-image: url('/images/small-logo2.png');
        min-height: 500px;
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        }
    </style>

    <!-- <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'> -->
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
      {{HTML::script('bootflat-admin/js/html5shiv.js')}}
      {{HTML::script('bootflat/js/respond.min.js')}}
    <![endif]-->
  </head>
  <body>
    <!--nav-->
    <nav role="navigation" class="navbar navbar-custom">
        <div class="container-fluid">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header">
            <button data-target="#bs-content-row-navbar-collapse-5" data-toggle="collapse" class="navbar-toggle" type="button">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand">&nbsp<img style="padding-bottom:10px;" src="{{('/images/nav-logo.png')}}"/>&nbsp <strong style="font-size:1.5em">e-BUYad</strong></a>
          </div>

          <!-- Collect the nav links, forms, and other content for toggling -->
          <div id="bs-content-row-navbar-collapse-5" class="collapse navbar-collapse" style="padding-bottom:10px;">
            <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="{{URL::to('/logout')}}"><b>SIGN OUT</b></a></li>
            </ul>

          </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    <!--header-->
    <div class="container-fluid">
    <!--documents-->
        <div class="row row-offcanvas row-offcanvas-left">
          <div class="col-xs-6 col-sm-3 sidebar-offcanvas" role="navigation" style="margin-left:10px">
            <ul class="list-group panel">
                <li class="list-group-item"><i class="glyphicon glyphicon-align-justify"></i> <b>NAVIGATION</b></li>
                <li>
                  <a href="#demo4" class="list-group-item " data-toggle="collapse"><i class="fa fa-money"></i> Transaction  <span class="glyphicon glyphicon-chevron-right"></span></a>
                  <div class="collapse" id="demo4">
                    
                    <a id="tmem" href="#submem" class="list-group-item" data-toggle="collapse">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-user"></i> Membership <span class="glyphicon glyphicon-chevron-right"></span></a>
                    <div class="collapse list-group-submenu" id="submem">
                      <a id="treg" href="{{URL::to('transaction/registration')}}" class="list-group-item">&nbsp &nbsp &nbsp &nbsp &nbsp<i class="glyphicon glyphicon-pencil"></i> Registration </a>
                      <a id="tcpin"  href="{{URL::to('/transaction/change-pin')}}" class="list-group-item">&nbsp &nbsp &nbsp &nbsp &nbsp<i class="glyphicon glyphicon-edit"></i> Change Pin </a>
                      <a id="tgcard" href="{{URL::to('transaction/generate-card')}}" class="list-group-item">&nbsp &nbsp &nbsp &nbsp &nbsp<i class="glyphicon glyphicon-credit-card"></i> Generate Card </a>
                      <a id="tdcard" href="{{URL::to('transaction/deactivate-card')}}" class="list-group-item">&nbsp &nbsp &nbsp &nbsp &nbsp<i class="glyphicon glyphicon-remove-sign"></i> Deactivate Card </a>
                    </div>


                    <a id="tsale" href="#subsale" class="list-group-item" data-toggle="collapse">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-shopping-cart"></i> Sale <span class="glyphicon glyphicon-chevron-right"></span></a>
                    <div class="collapse list-group-submenu" id="subsale">
                    <a id="tmsale" href="{{URL::to('transaction/sell')}}" class="list-group-item">&nbsp &nbsp &nbsp &nbsp &nbsp<i class="glyphicon glyphicon-shopping-cart"></i> Make A Sale </a>
                    <a id="tret" href="{{URL::to('transaction/return')}}" class="list-group-item">&nbsp &nbsp &nbsp &nbsp &nbsp<i class="glyphicon glyphicon-transfer"></i> Accept Return </a>
                    </div>

                    <a id="trelo" href="{{URL::to('transaction/reload')}}" class="list-group-item">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-refresh"></i> Reload </a>
                    
                    <a id="tegc" href="{{URL::to('transaction/egc')}}" class="list-group-item">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-gift"></i> Electronic Gift Check</a>
                    
                  </div>
                </li>

                <li>
                <a id="trepo" href="#report" class="list-group-item " data-toggle="collapse"><i class="glyphicon glyphicon-list-alt"></i> Reports <span class="glyphicon glyphicon-chevron-right"></span></a>
                  <div class="collapse" id="report">

                    <a href="{{URL::to('reports/salesreport')}}" class="list-group-item">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-list-alt"></i> Sales Summary Report </a>
                    <a href="{{URL::to('reports/creditreport')}}" class="list-group-item">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-list-alt"></i> E-Buyad Cash Deposit Report </a>
                  </div>
                </li>
                <a id="tquery" href="#query" class="list-group-item" data-toggle="collapse"><i class="glyphicon glyphicon-question-sign"></i> Queries <span class="glyphicon glyphicon-chevron-right"></span></a>
                  <div class="collapse" id="query">
                    <a href="{{URL::to('queries/memberquery')}}" class="list-group-item">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-user"></i> Member Activity </a>
                    <a href="{{URL::to('queries/branchquery')}}" class="list-group-item">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-briefcase"></i> Branch Activity </a>
                  </div>
                <li>
                  <a id="tutil" href="#utilmenu" class="list-group-item " data-toggle="collapse"><i class="glyphicon glyphicon-cog"></i> Utilities <span class="glyphicon glyphicon-chevron-right"></span></a>
                  <div class="collapse" id="utilmenu">
                    <a href="{{URL::to('utils/set-branch')}}" class="list-group-item">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-tasks"></i> Set Branch </a>
                    <a href="{{URL::to('utils/pointload')}}" class="list-group-item">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-tasks"></i> Point/Load Mechanics </a>
                    <a href="{{URL::to('utils/jobaccess')}}" class="list-group-item">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-tasks"></i> Job Access </a>
                    
                  </div>
                </li>

                <li>
                  <a id="tmaint" href="#demo3" class="list-group-item " data-toggle="collapse"><i class="glyphicon glyphicon-th"></i> Maintenance  <span class="glyphicon glyphicon-chevron-right"></span></a>
                  <div class="collapse" id="demo3">
                    <a href="{{URL::to('/maintenance/members')}}" class="list-group-item">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-user"></i> Members </a>
                    <a href="{{URL::to('/maintenance/branchprod/add-product')}}" class="list-group-item">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-barcode"></i> Branch Products </a>
                    <a href="#SubMenu1" class="list-group-item" data-toggle="collapse">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-barcode"></i> Products  <span class="glyphicon glyphicon-chevron-right"></span></a>
                    <div class="collapse list-group-submenu" id="SubMenu1">
                      <a href="{{URL::to('/maintenance/products/med')}}" class="list-group-item">&nbsp &nbsp &nbsp &nbsp &nbsp <i class="fa fa-medkit"></i>Medicine</a>
                      <a href="{{URL::to('/maintenance/products/nonmed')}}" class="list-group-item">&nbsp &nbsp &nbsp &nbsp &nbspNon-medicine</a>
                      <a href="{{URL::to('/maintenance/fpu/forms')}}" class="list-group-item" >&nbsp &nbsp &nbsp &nbsp &nbsp Forms, Packaging and UOM</a>
                    </div>
                    <a href="{{URL::to('/maintenance/employee')}}" class="list-group-item">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-briefcase"></i>Company</a>
                    <a href="{{URL::to('maintenance/ppd/discount')}}" class="list-group-item">&nbsp &nbsp &nbsp<i class="glyphicon glyphicon-tags"></i>Discount</a>
                  </div>
                </li>
            </ul>
          </div>

          <div class="col-xs-12 col-sm-9 content">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title"><a href="javascript:void(0);" class="toggle-sidebar"><span class="fa fa-angle-double-left" data-toggle="offcanvas" title="Maximize Panel"></span></a> Hide Navigation </h3>

                <script> 
                $(document).ready(function(){
                  $('[data-toggle=offcanvas]').click(function () {
                    $('.row-offcanvas').toggleClass('active');
                    $(this).toggleClass('fa-angle-double-left fa-angle-double-right');
                  });
                })
              </script>

              </div>
              @yield('content')
            </div>
          </div>
        </div><!-- content -->
    </div>

    
    @yield('internal-scripts')

    /*{{HTML::script('js/validator.js')}}*/
  </body>

</html>
