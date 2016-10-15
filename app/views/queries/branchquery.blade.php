@extends('....layout')

@section('page-title')
    Queries - Branch Activity
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
      <center><h5 class="content-row-title" style="font-size:25px"><i class="glyphicon glyphicon-briefcase"></i>&nbsp Branch Activity
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
                  <option selected value="Code">Branch Code</option>
                  <option value="Name">Branch Name</option>
                  <option value="Transactions">Transactions</option>
                  <option value="Sale">Sales</option>
                </select>
              </div>
            </div>

            <div class="col-md-8">
              <label class="col-md-4 control-label">Ordering:</label>
              <div class="col-md-4">
                <select id="ord" class="form-control col-md-6">
                  <option selected value="DESC">Descending</option>
                  <option value="ASC">Ascending</option>
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
        <h4>E-BUYad<br>BRANCH ACTIVITY</h4>
      </center>
      <br>
      <p style="font-size: 18px">&nbsp&nbsp&nbsp Created by:<br>&nbsp&nbsp&nbsp Date:</p>
     

        <div class="col-md-12">
          <table id="example" class="table table-hover">
           <br>
           <caption></caption>
            <thead>
              <tr role="row">
                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Member ID: activate to sort column descending" style="width: 249px;">Branch Code</th>
                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Last Name: activate to sort column descending" style="width: 249px;">Branch Name</th>
                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Last Name: activate to sort column descending" style="width: 249px;">Transactions</th>
                <th class="" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Last Name: activate to sort column descending" style="width: 249px;">Sales</th>
              </tr>
            </thead>

            <tbody>
            </tbody>
          </table>
        
           
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
            url: '/queries/branchquery/generate',
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
                t.row.add([
                  data[i]['Code'],
                  data[i]['Name'],
                  data[i]['Transactions'],
                  data[i]['Sale'],
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