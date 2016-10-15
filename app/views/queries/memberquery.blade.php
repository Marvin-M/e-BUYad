@extends('....layout')

@section('page-title')
    Queries - Member Activity
@stop

@section('other-scripts')
    {{HTML::script('bootflat-admin/js/datatables.min.js')}}

    <script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
      $('#example').DataTable({
        "searching": false,
        "lengthChange": false,
        "bSort": false

      });
    } );
  </script>
@stop

@section('content')
  <div class="panel-body">
    <div class="content-row">  
      <center><h5 class="content-row-title" style="font-size:25px"><i class="glyphicon glyphicon-user"></i>&nbsp Member Activity
      <hr>
      </h5></center>
      

    <div class="col-md-12">
      <form role="form" class="form-horizontal">
        <div class="row">
          <div class="col-md-12">
            <p>Transactions Within:</p>
          </div>

          <div class="row">
            <div class="col-md-8">
              <label class="col-md-4 control-label">Date From:</label>
              <div class="col-md-4">
                <input type="date" id="start" class="form-control" name="start" value="1900-01-01" min="1900-01-01" max="2016-08-29">
              </div>
            </div>

            <div class="col-md-8">
              <label class="col-md-4 control-label">Date To:</label>
              <div class="col-md-4">
                <input type="date" id="end" class="form-control" name="start" value="<?php echo date('Y-m-d') ?>" min="1900-01-01" max="<?php echo date('Y-m-d') ?>">
              </div>
            </div>
          </div>

          <div class="col-md-12">
            <p>Sorting:</p>
          </div>
          </div>
          <div class="row">
            <div class="col-md-8">
              <label class="col-md-4 control-label">Order By:</label>
              <div class="col-md-4">
                <select id="ordby" class="form-control col-md-6">
                  <option selected value="Code">Member Code</option>
                  <option value="FName">First Name</option>
                  <option value="LName">Last Name</option>
                  <option value="Age">Age</option>
                  <option value="Transaction">Transactions</option>
                  <option value="Balance">Balance Load</option>
                  <option value="Points">Points Earned</option>
                </select>
              </div>
            </div>

            <div class="col-md-8">
              <label class="col-md-4 control-label">Ordering:</label>
              <div class="col-md-4">
                <select id="ord" class="form-control col-md-6">
                  <option value="DESC">Descending</option>
                  <option selected value="ASC">Ascending</option>
                </select>
              </div>
            </div>
            </div>
            <div class="row">
            <div class="col-md-8">
              <div class="col-md-4"></div>
              <div class="col-md-4">
                <button onclick="getQuery()" type="button" class="btn btn-block btn-success">GENERATE</button>
              </div>

              <div class="col-md-4"></div>
            </div>
         </div> 
        </div>
      </form>
    </div>

      <div class="col-md-12">
      <hr>
      <center>
        <img src="{{('/images/logo.png')}}" style="height: 5%; width:5%">
        <h4>E-BUYad<br>MEMBER ACTIVITY</h4>
      </center>
      <br>
      <p style="font-size: 18px">&nbsp&nbsp&nbsp Created by:<br>&nbsp&nbsp&nbsp Date:</p>
     

        <div class="col-md-12">
          <table id="example" class="table table-hover">
           <br>
           <caption></caption>
            <thead>
              <tr role="row">
                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Member ID: activate to sort column descending" style="width: 249px;">Member Code</th>
                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Last Name: activate to sort column descending" style="width: 249px;">Member Last Name</th>
                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Last Name: activate to sort column descending" style="width: 249px;">Member First Name</th>
                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Last Name: activate to sort column descending" style="width: 249px;">Age</th>
                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Last Name: activate to sort column descending" style="width: 249px;">Transactions</th>
                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Last Name: activate to sort column descending" style="width: 249px;">Balance Load</th>
                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Last Name: activate to sort column descending" style="width: 249px;">Points Earned</th>
              </tr>
            </thead>

            <tbody>
            </tbody>
          </table>
        
            <!--div class="col-md-8; pull-right">
              <button class="btn btn-info">Choose Data</button>
              <button class="btn btn-info">Restore Data</button>
            </div-->
          </div>
      
        </div>
    </div>
  </div>
@stop

@section('internal-scripts')
  <script>
    @if(Session::get('message') != null)
      $('#prompt').modal('show');
    @endif
  </script>
  <script>
    function getQuery(){
      $.ajax({
            url: '/queries/memberquery/generate',
            type: 'GET',
            data: {
                start: document.getElementById('start').value,
                end: document.getElementById('end').value,
                col: document.getElementById('ordby').value,
                ord: document.getElementById('ord').value
            },
            success: function(data){
              if(data.length > 0){
                var t = $("#example").DataTable();
                t.clear().draw();

                for(var i = 0; i < data.length; i++){

                  if(data[i]['Points'] == null) data[i]['Points'] = 0;
                t.row.add([
                  data[i]['Code'],
                  data[i]['LName'],
                  data[i]['FName'],
                  data[i]['Age'],
                  data[i]['Transaction'],
                  data[i]['Balance'],
                  data[i]['Points'],
                  ]).draw(false);
                }
              }else{
              alert('NO DATA WAS RETURNED');
              }
            }, 
            error: function(){
              alert('ERROR FETCHING DATA');
            }
        });
    }
  </script>
@stop

@section('added-scripts')
  
@stop