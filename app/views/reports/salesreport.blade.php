@extends('....layout')

@section('page-title')
    Sales Reports
@stop

@section('other-scripts')
    {{HTML::script('bootflat-admin/js/datatables.min.js')}}
    {{HTML::style('bootflat-admin/css/custom.css')}}

    <script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
      $('#example').DataTable( {
        "searching": false,
        "lengthChange": false
      }  );
    } );
  </script>

@stop

@section('content')
<div class="panel-body">
  <div class="content-row">  
    <center>
      <h5 class="content-row-title" style="font-size:25px"><i class="glyphicon glyphicon-list-alt"></i>&nbsp Sales Summary Report
      <hr>
      </h5>
    </center>

    <input type="hidden" id="current_date" value="<?php echo date('Y-m-d') ?>">
    <input type="hidden" id="current_year" value="<?php echo date('Y') ?>">
    <input type="hidden" id="current_month" value="<?php echo date('m') ?>">
    
    <div class="col-md-12">
      <form id="creditform" role="form" class="form-horizontal" method="post" action="">
        <div class="col-md-4 pull-right">
          <div class="form-group"><div class="col-md-2"></div>
            <div class="col-md-2">
              <input type="button" onclick="getDateRange()" class="btn btn-info" style="width:300px;height:100px" value="Generate Report"><br>
              <button id="btnpdf" disabled class="btn btn-danger" style="width:300px;height:100px">Generate PDF</button>
            </div>
          </div>
        </div>

        <input type="hidden" id="reptype" name="reptype">
        <input type="hidden" id="dailydate" name="dailydate">
        <input type="hidden" id="weeklydate" name="weeklydate">
        <input type="hidden" id="monthlydate" name="monthlydate">
        <input type="hidden" id="quarterlydate" name="quarterlydate">
        <input type="hidden" id="yearlydate" name="yearlydate">
        <input type="hidden" id="customdate1" name="customdate1">
        <input type="hidden" id="customdate2" name="customdate2">

        <div class="col-md-8 pull-left">
          <div class="radio">
            <label class="control-label col-md-2" style="padding-right:3.2%">
              <input id="rdfix" onclick="radioClicked('a')" type="radio" checked>Fixed Range:
            </label>
            <div class="col-md-6">
              <select class = "form-control" required onchange="range()" id="selfix" name="selfix">
                <option disabled selected value="x"> -- SELECT RANGE -- </option>
                <option value="0">Daily</option>
                <option value="1">Weekly</option>
                <option value="2">Monthly</option>
                <option value="3">Quarterly</option>
                <option value="4">Annual</option>                    
              </select>
            </div>
          </div>
         </div> 
       
        <div class="col-md-8" id="daily" hidden>
          <label class="control-label col-md-8">
            <div id="daily" class="col-md-4 pull-left">
              <input onclick="enable(this,'dailystart','dailystart')" id="chkday" type="checkbox"> Set Date:
            </div>
            <div id="daily" class="col-md-8 pull-right">
              <input disabled type="date" id="dailystart" class="form-control" name="start" min="1900-01-01" max="<?php echo date('Y-m-d') ?>" required>
            </div>
          </label>
        </div>
        
        <div class="col-md-8" id="weekly" hidden>
          <label class="control-label col-md-8">
            <div id="weekly" class="col-md-4 pull-left">
              <input  onclick="enable(this,'weeklyend','weeklyend')" id="chkweek" type="checkbox"> Set Last Date of the Week:
            </div>
            <div id="weekly" class="col-md-8 pull-right">
              <input disabled type="date" id="weeklyend" class="form-control" name="start" min="1900-01-01" max="<?php echo date('Y-m-d') ?>">
            </div>
          </label>
        </div>

        <div class="col-md-8" id="monthly" hidden>
          <label class="control-label col-md-8">
            <div id="monthly" class="col-md-4 pull-left">
              <input  onclick="enable(this,'monthmonth','monthyear')" id="chkmonth" type="checkbox"> Set Month:
            </div>
            <div id="monthly" class="col-md-8 pull-right">
              <select id="monthmonth" disabled class = "form-control" required>
                <option disabled selected value="x"> -- SELECT MONTH -- </option>
                <option value="01">January</option>
                <option value="02" >February</option>     
                <option value="03">March</option> 
                <option value="04">April</option> 
                <option value="05">May</option>              
                <option value="06">June</option> 
                <option value="07">July</option> 
                <option value="08">August</option> 
                <option value="09">September</option> 
                <option value="10">October</option> 
                <option value="11">November</option> 
                <option value="12">December</option> 
              </select>
              <select id="monthyear" disabled class = "form-control" required>
                <option disabled selected value="x"> -- SELECT YEAR -- </option>
                <?php
                    $startyear = date('Y');
                    $startyear = intval($startyear);
                    $endyear = $startyear - 100;

                    for($i = $startyear; $i > $endyear; $i--){
                      echo '<option value=\''.$i.'\'>'.$i.'</option>';
                    }
                ?>     
              </select>
            </div>
          </label>
        </div>

        <div class="col-md-8" id="quarterly" hidden>
          <label class="control-label col-md-8">
            <div id="quarterly" class="col-md-4 pull-left">
              <input  onclick="enable(this,'qmonth','qyear')" id="chkquarter" type="checkbox"> Set Quarter:
            </div>
            <div id="quarterly" class="col-md-8 pull-right">
              <select id="qmonth" disabled class = "form-control" required>
                <option disabled selected value="x"> -- SELECT QUARTER MONTH -- </option>
                <option value="1">January-March</option>
                <option value="4">April-June</option>  
                <option value="7">July-September</option> 
                <option value="10">October-December</option> 
              </select>
              <select  id="qyear" disabled class = "form-control" required>
                <option disabled selected value="x"> -- SELECT YEAR -- </option>
                <?php
                    $startyear = date('Y');
                    $startyear = intval($startyear);
                    $endyear = $startyear - 100;

                    for($i = $startyear; $i > $endyear; $i--){
                      echo '<option value=\''.$i.'\'>'.$i.'</option>';
                    }
                ?>   
              </select>
            </div>
          </label>
        </div>
        
        <div class="col-md-8" id="yearly" hidden>
          <label class="control-label col-md-8">
            <div id="yearly" class="col-md-4 pull-left">
              <input onclick="enable(this,'yyearly','yyearly')"  id="chkyear" type="checkbox"> Set Year:
            </div>
            <div id="yearly" class="col-md-8 pull-right">
              <select id="yyearly" disabled class = "form-control" required>
                <option disabled selected value="x"> -- SELECT YEAR -- </option>
                <?php
                    $startyear = date('Y');
                    $startyear = intval($startyear);
                    $endyear = $startyear - 100;

                    for($i = $startyear; $i > $endyear; $i--){
                      echo '<option value=\''.$i.'\'>'.$i.'</option>';
                    }
                ?>   
              </select>
            </div>
          </label>
        </div>
        
        <br>
        <div class="col-md-8 pull-left">
          <div class="radio">
            <label class="control-label col-md-2">
              <input id="rdcust" onclick="radioClicked('b')" type="radio" required>Custom Range:
            </label><br>
            <div class="col-md-6">

              <label class="col-md-3 control-label">Date From:</label>
              <div class="col-md-9">
                <input disabled type="date" id="dtstart" class="form-control" name="start" min="1900-01-01" max="<?php echo date('Y-m-d') ?>">
              </div>

              <label class="col-md-3 control-label">Date To:</label>
              <div class="col-md-9">
                <input disabled type="date" id="dtend" class="form-control" name="start" min="1900-01-01" max="<?php echo date('Y-m-d') ?>">
              </div>

            </div>
          </div>
         </div>

        
      </form>
    </div>


    
    <div class="col-md-12">
      <hr>
      <center>
        <img src="{{('/images/logo.png')}}" style="height: 5%; width:5%">
        <h4>E-BUYad<br>SALES SUMMARY REPORT</h4>
      </center>
      <br>
      <p style="font-size: 18px">&nbsp&nbsp&nbspDate: <?php echo date('Y-m-d')?></p>
      <br>
      <p style="font-size: 18px" id="reporttotal"><b>&nbsp&nbsp&nbsp TOTAL SALES: <b></p>

      <br>
              <!-- datatables -->
        <div class = "row">
          <div class="col-md-12">
                <div class="table-responsive">
                  <table id="example" class="table table-bordered table-hover">
                    <thead>
                      <tr role="row">
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Last Name: activate to sort column descending">Transaction ID</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending">Gross Amount</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Address: activate to sort column ascending">VAT</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="OSCA ID: activate to sort column ascending">VATable</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Home Number: activate to sort column ascending">VAT Exempt</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending">Discount</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending">Total Sales</th>
                      </tr>
                    </thead>

                    <tbody>
                    </tbody>
                  </table>
                </div>
              </div>
          </div>
    </div>
  </div>
</div>


 <script>
    function range(){
      var temp = document.getElementById('fixed');
      var value = temp.options[temp.selectedIndex].value;

      $("#daily").hide();
      $("#weekly").hide();
      $("#monthly").hide();
      $("#quarterly").hide();
      $("#yearly").hide();

        if(document.getElementById('fixed').value == "0")
          {
            $("#daily").show();
          }
        else if(document.getElementById('fixed').value == "1")
          {
            $("#weekly").show();
          }
        else if(document.getElementById('fixed').value == "2")
          {
            $("#monthly").show();
          }
        else if(document.getElementById('fixed').value == "3")
          {
            $("#quarterly").show(); 
          }
        else if(document.getElementById('fixed').value == "4")
          {
            $("#yearly").show();
          }
    }

   </script>


@stop

@section('internal-scripts')
  <script>
    function radioClicked(rd){
      if(rd == 'a'){
        document.getElementById('rdfix').checked = true;
        document.getElementById('rdcust').checked = false;
        document.getElementById('selfix').removeAttribute('disabled');
        document.getElementById('dtstart').setAttribute('disabled','');
        document.getElementById('dtend').setAttribute('disabled','');
        document.getElementById('dtstart').value = "";
        document.getElementById('dtend').value = "";
        $(":checkbox").click();
      }else{
        document.getElementById('rdfix').checked = false;
        document.getElementById('rdcust').checked = true;
        document.getElementById('selfix').setAttribute('disabled','');
        document.getElementById('dtstart').removeAttribute('disabled');
        document.getElementById('dtend').removeAttribute('disabled');
        document.getElementById('selfix').value="x";
        $("#daily").hide();
        $("#weekly").hide();
        $("#monthly").hide();
        $("#quarterly").hide();
        $("#yearly").hide();
      }
    }
  </script>
  <script>
    @if(Session::get('message') != null)
      $('#prompt').modal('show');
    @endif
  </script>
  <script>
    function range(){
      $(":checkbox").checked = false;
      var temp = document.getElementById('selfix');
      var value = temp.options[temp.selectedIndex].value;

      $("#daily").hide();
      $("#weekly").hide();
      $("#monthly").hide();
      $("#quarterly").hide();
      $("#yearly").hide();

        if(document.getElementById('selfix').value == "0")
          {
            $("#daily").show();
          }
        else if(document.getElementById('selfix').value == "1")
          {
            $("#weekly").show();
          }
        else if(document.getElementById('selfix').value == "2")
          {
            $("#monthly").show();
          }
        else if(document.getElementById('selfix').value == "3")
          {
            $("#quarterly").show(); 
          }
        else if(document.getElementById('selfix').value == "4")
          {
            $("#yearly").show();
          }
    }
  </script>
  <script>
      function enable(id, id1, id2){
        if(id.checked){
          document.getElementById(id1).removeAttribute('disabled');
          document.getElementById(id2).removeAttribute('disabled');
        }else{
          document.getElementById(id1).setAttribute('disabled','');
          document.getElementById(id2).setAttribute('disabled','');
          document.getElementById(id1).value="x";
          document.getElementById(id2).value="x";
        }
      }
  </script>
  <script>
    function getDateRange(){
      var date1;
      if(document.getElementById('rdfix').checked){
          if(document.getElementById('selfix').value == '0'){
              if(document.getElementById('chkday').checked){
                date1 = document.getElementById('dailystart').value;
              }else{
                date1 = document.getElementById('current_date').value;
              }

              showDailyReport(date1);
          }else if(document.getElementById('selfix').value == '1'){
              if(document.getElementById('chkweek').checked){
                date1 = document.getElementById('weeklyend').value;
              }else{
                date1 = document.getElementById('current_date').value;
              }

              showWeeklyReport(date1);
          }else if(document.getElementById('selfix').value == '2'){
              if(document.getElementById('chkmonth').checked){
                showMonthlyReport(
                  document.getElementById('monthmonth').value,
                  document.getElementById('monthyear').value);
              }else{
                showMonthlyReport(
                  document.getElementById('current_month').value,
                  document.getElementById('current_year').value);
              }
          }else if(document.getElementById('selfix').value == '3'){
              if(document.getElementById('chkquarter').checked){
                showQuarterlyReport(
                  getQuarter(document.getElementById('qmonth').value),
                  document.getElementById('qyear').value);
              }else{
                showQuarterlyReport(
                  getQuarter(document.getElementById('current_month').value),
                  document.getElementById('current_year').value);
              }
          }else if(document.getElementById('selfix').value == '4'){
              if(document.getElementById('chkyear').checked){
                showYearlyReport(document.getElementById('yyearly').value);
              }else{
                showYearlyReport(document.getElementById('current_year').value);
              }
          }else{
            alert('invalid date');
          }
      }else{
        showCustomReport(
          document.getElementById('dtstart').value,
          document.getElementById('dtend').value);
      }
    }
  </script>
  <script>
    function getQuarter(month){
      var mon = parseInt(month);

      if(mon <= 3){
        return '01';
      }else if(mon <= 6){
        return '04';
      }else if(mon <= 9){
        return '07';
      }else{
        return '10';
      }
    }
  </script>
  <script>
    function showDailyReport(date){

       $.ajax({
            url: '/reports/salesreport/get-daily-data',
            type: 'GET',
            data: {
                repdate: date
            },
            success: function(data){
              if(data.length > 0){

              document.getElementById('btnpdf').removeAttribute('disabled');
                document.getElementById('dailydate').value = date;
                document.getElementById('creditform').action = "{{URL::to('/reports/salesreport/dailyPDF')}}";

                var t = $("#example").DataTable();
                var total = 0;
                t.clear().draw();
                
                for(var i = 0; i < data.length; i++){

                  if(data[i]['strTransDiscCode'] == 'DSC00001'){
                    var vatable = 0;
                    var vatex = data[i]['Total'] - (data[i]['Total'] * data[i]['TAX']);
                    var totals = parseFloat(vatex) -  data[i]['Discount'];
                  }else{
                    var vatable = data[i]['Total'] - (data[i]['Total'] * data[i]['TAX']);
                    var vatex = 0;
                    var totals =  data[i]['Total'] -  data[i]['Discount'];
                  }
                  t.row.add([
                    data[i]['Transaction'],
                    data[i]['Total'],
                    data[i]['TAX'],
                    vatable,
                    vatex,
                    data[i]['Discount'],
                    parseFloat(totals).toFixed(2)
                    ]).draw(false);
                  total = total + totals;
                }

                document.getElementById('reporttotal').innerHTML = "<b>&nbsp&nbsp&nbsp TOTAL SALES: <b>" + (parseFloat(total).toFixed(2)).toString();
              }else{
                alert('NO DATA WAS RECEIVED');
                document.getElementById('reporttotal').innerHTML = "<b>&nbsp&nbsp&nbsp TOTAL SALES: <b>";
                var t = $("#example").DataTable();
                t.clear().draw();
              }
            }, 
            error: function(){
              alert('ERROR FETCHING DATA');
            }
        });
    }

    function showWeeklyReport(date){

       $.ajax({
            url: '/reports/salesreport/get-weekly-data',
            type: 'GET',
            data: {
                repdate: date
            },
            success: function(data){
              if(data.length > 0){
                var t = $("#example").DataTable();

          document.getElementById('btnpdf').removeAttribute('disabled');
                document.getElementById('weeklydate').value = date;
                document.getElementById('creditform').action = "{{URL::to('/reports/salesreport/weeklyPDF')}}";

                var total = 0;
                t.clear().draw();

                for(var i = 0; i < data.length; i++){

                  if(data[i]['strTransDiscCode'] == 'DSC00001'){
                    var vatable = 0;
                    var vatex = data[i]['Total'] - (data[i]['Total'] * data[i]['TAX']);
                    var totals = parseFloat(vatex) -  data[i]['Discount'];
                  }else{
                    var vatable = data[i]['Total'] - (data[i]['Total'] * data[i]['TAX']);
                    var vatex = 0;
                    var totals =  data[i]['Total'] -  data[i]['Discount'];
                  }
                  t.row.add([
                    data[i]['Transaction'],
                    data[i]['Total'],
                    data[i]['TAX'],
                    vatable,
                    vatex,
                    data[i]['Discount'],
                    parseFloat(totals).toFixed(2)
                    ]).draw(false);
                  total = total + totals;
                }

                document.getElementById('reporttotal').innerHTML = "<b>&nbsp&nbsp&nbsp TOTAL SALES: <b>" + (parseFloat(total).toFixed(2)).toString();
              }else{
                alert('NO DATA WAS RECEIVED');
                document.getElementById('reporttotal').innerHTML = "<b>&nbsp&nbsp&nbsp TOTAL SALES: <b>";
                var t = $("#example").DataTable();
                t.clear().draw();
              }
            }, 
            error: function(){
              alert('ERROR FETCHING DATA');
            }
        });
    }

    function showMonthlyReport(mon, yr){

       $.ajax({
            url: '/reports/creditreport/get-monthly-data',
            type: 'GET',
            data: {
                month: mon,
                year: yr
            },
            success: function(data){
              if(data.length > 0){
                var t = $("#example").DataTable();

          document.getElementById('btnpdf').removeAttribute('disabled');
                document.getElementById('monthlydate').value = yr + "-" + mon + "-01 00:00:00";
                document.getElementById('creditform').action = "{{URL::to('/reports/creditreport/monthlyPDF')}}";

                var total = 0;
                t.clear().draw();

                for(var i = 0; i < data.length; i++){

                  if(data[i]['strTransDiscCode'] == 'DSC00001'){
                    var vatable = 0;
                    var vatex = data[i]['Total'] - (data[i]['Total'] * data[i]['TAX']);
                    var totals = parseFloat(vatex) -  data[i]['Discount'];
                  }else{
                    var vatable = data[i]['Total'] - (data[i]['Total'] * data[i]['TAX']);
                    var vatex = 0;
                    var totals =  data[i]['Total'] -  data[i]['Discount'];
                  }
                  t.row.add([
                    data[i]['Transaction'],
                    data[i]['Total'],
                    data[i]['TAX'],
                    vatable,
                    vatex,
                    data[i]['Discount'],
                    parseFloat(totals).toFixed(2)
                    ]).draw(false);
                  total = total + totals;
                }

                document.getElementById('reporttotal').innerHTML = "<b>&nbsp&nbsp&nbsp TOTAL SALES: <b>" + (parseFloat(total).toFixed(2)).toString();
              }else{
                alert('NO DATA WAS RECEIVED');
                document.getElementById('reporttotal').innerHTML = "<b>&nbsp&nbsp&nbsp TOTAL SALES: <b>";
                var t = $("#example").DataTable();
                t.clear().draw();
              }
            }, 
            error: function(){
              alert('ERROR FETCHING DATA');
            }
        });
    }

    function showQuarterlyReport(mon, yr){
       $.ajax({
            url: '/reports/salesreport/get-quarterly-data',
            type: 'GET',
            data: {
                month: mon,
                year: yr
            },
            success: function(data){
              if(data.length > 0){
                var t = $("#example").DataTable();

          document.getElementById('btnpdf').removeAttribute('disabled');
                document.getElementById('quarterlydate').value = yr + "-" + mon + "-01 00:00:00";
                document.getElementById('creditform').action = "{{URL::to('/reports/salesreport/quarterlyPDF')}}"

                var total = 0;
                t.clear().draw();

                for(var i = 0; i < data.length; i++){

                  if(data[i]['strTransDiscCode'] == 'DSC00001'){
                    var vatable = 0;
                    var vatex = data[i]['Total'] - (data[i]['Total'] * data[i]['TAX']);
                    var totals = parseFloat(vatex) -  data[i]['Discount'];
                  }else{
                    var vatable = data[i]['Total'] - (data[i]['Total'] * data[i]['TAX']);
                    var vatex = 0;
                    var totals =  data[i]['Total'] -  data[i]['Discount'];
                  }
                  t.row.add([
                    data[i]['Transaction'],
                    data[i]['Total'],
                    data[i]['TAX'],
                    vatable,
                    vatex,
                    data[i]['Discount'],
                    parseFloat(totals).toFixed(2)
                    ]).draw(false);
                  total = total + totals;
                }

                document.getElementById('reporttotal').innerHTML = "<b>&nbsp&nbsp&nbsp TOTAL SALES: <b>" + (parseFloat(total).toFixed(2)).toString();
              }else{
                alert('NO DATA WAS RECEIVED');
                document.getElementById('reporttotal').innerHTML = "<b>&nbsp&nbsp&nbsp TOTAL SALES: <b>";
                var t = $("#example").DataTable();
                t.clear().draw();
              }
            }, 
            error: function(){
              alert('ERROR FETCHING DATA');
            }
        });
    }

    function showYearlyReport(yr){
       $.ajax({
            url: '/reports/salesreport/get-annually-data',
            type: 'GET',
            data: {
                year: yr
            },
            success: function(data){
              if(data.length > 0){
                var t = $("#example").DataTable();

          document.getElementById('btnpdf').removeAttribute('disabled');
                document.getElementById('yearlydate').value = yr + "-01-01 00:00:00";
                document.getElementById('creditform').action = "{{URL::to('/reports/salesreport/yearlyPDF')}}"

                var total = 0;
                t.clear().draw();

                for(var i = 0; i < data.length; i++){

                  if(data[i]['strTransDiscCode'] == 'DSC00001'){
                    var vatable = 0;
                    var vatex = data[i]['Total'] - (data[i]['Total'] * data[i]['TAX']);
                    var totals = parseFloat(vatex) -  data[i]['Discount'];
                  }else{
                    var vatable = data[i]['Total'] - (data[i]['Total'] * data[i]['TAX']);
                    var vatex = 0;
                    var totals =  data[i]['Total'] -  data[i]['Discount'];
                  }
                  t.row.add([
                    data[i]['Transaction'],
                    data[i]['Total'],
                    data[i]['TAX'],
                    vatable,
                    vatex,
                    data[i]['Discount'],
                    parseFloat(totals).toFixed(2)
                    ]).draw(false);
                  total = total + totals;
                }

                document.getElementById('reporttotal').innerHTML = "<b>&nbsp&nbsp&nbsp TOTAL SALES: <b>" + (parseFloat(total).toFixed(2)).toString();
              }else{
                alert('NO DATA WAS RECEIVED');
                document.getElementById('reporttotal').innerHTML = "<b>&nbsp&nbsp&nbsp TOTAL SALES: <b>";
                var t = $("#example").DataTable();
                t.clear().draw();
              }
            }, 
            error: function(){
              alert('ERROR FETCHING DATA');
            }
        });
    }

    function showCustomReport(date1, date2){
       $.ajax({
            url: '/reports/salesreport/get-custom-data',
            type: 'GET',
            data: {
                dtfir: date1,
                dtnex: date2
            },
            success: function(data){
              if(data.length > 0){
                var t = $("#example").DataTable();

                
          document.getElementById('btnpdf').removeAttribute('disabled');
                document.getElementById('customdate1').value = date1;
                document.getElementById('customdate2').value = date2;
                document.getElementById('creditform').action = "{{URL::to('/reports/salesreport/customPDF')}}"

                var total = 0;
                t.clear().draw();

                for(var i = 0; i < data.length; i++){

                  if(data[i]['strTransDiscCode'] == 'DSC00001'){
                    var vatable = 0;
                    var vatex = data[i]['Total'] - (data[i]['Total'] * data[i]['TAX']);
                    var totals = parseFloat(vatex) -  data[i]['Discount'];
                  }else{
                    var vatable = data[i]['Total'] - (data[i]['Total'] * data[i]['TAX']);
                    var vatex = 0;
                    var totals =  data[i]['Total'] -  data[i]['Discount'];
                  }
                  t.row.add([
                    data[i]['Transaction'],
                    data[i]['Total'],
                    data[i]['TAX'],
                    vatable,
                    vatex,
                    data[i]['Discount'],
                    parseFloat(totals).toFixed(2)
                    ]).draw(false);
                  total = total + totals;
                }

                document.getElementById('reporttotal').innerHTML = "<b>&nbsp&nbsp&nbsp TOTAL SALES: <b>" + (parseFloat(total).toFixed(2)).toString();
              }else{
                alert('NO DATA WAS RECEIVED');
                document.getElementById('reporttotal').innerHTML = "<b>&nbsp&nbsp&nbsp TOTAL SALES: <b>";
                var t = $("#example").DataTable();
                t.clear().draw();
              }
            }, 
            error: function(){
              alert('ERROR FETCHING DATA');
            }
        });
    }
  </script>
@stop