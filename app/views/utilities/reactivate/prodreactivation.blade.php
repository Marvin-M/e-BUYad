@extends('....layout')

@section('page-title')
    Utilities - Data Reactivation
@stop

@section('other-scripts')
    {{HTML::script('bootflat-admin/js/datatables.min.js')}}

    <script type="text/javascript" charset="utf-8">
    $(document).ready(function() {
      $('#example').DataTable();
    } );
  </script>
@stop

@section('content')
  <div class="panel-body">
    <div class="content-row">  
      <center><h5 class="content-row-title" style="font-size:25px"><i class="glyphicon glyphicon-barcode"></i>&nbsp Product Data Reactivation
      <hr>
      </h5></center>
      
      <div class= "row">
        <div class="btn-group btn-group-justified">
          <a href="therareactivation" class="btn btn-primary" style="font-size:15px">Therapeautic Data Reactivation</a>
          <a href="genreactivation" class="btn btn-primary">Generic Data Reactivation</a>
          <a href="brandreactivation" class="btn btn-primary">Brand Data Reactivation</a>
          <a href="manureactivation" class="btn btn-primary" style="font-size:14px">Manufacturer Data Reactivation</a>
          <a href="prodreactivation" class="btn btn-info">Product Data Reactivation</a>
        </div>
      
        <br>

        <div class="col-md-12"><br>
          <table id="example" class="table table-bordered table-hover">
           <br>
           <caption></caption>
            <thead>
              <tr role="row">
                <th class="sorting_asc" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Member ID: activate to sort column descending" style="width: 249px;">Code</th>
                <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Type</th>
                <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Class</th>
                <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Brand</th>
                <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Generic</th>
                <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Manufacturer</th>
                <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Form</th>
                <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Size</th>
                <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Dosage</th>
                <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Packaging</th>
                <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;"><p>₱/Pc</p></th>
                <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;"><p>₱/Pack</p></th>
                <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Description</th>
              </tr>
            </thead>

            <tbody>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        
            <div class="col-md-8; pull-right">
              <button class="btn btn-primary"  data-toggle="modal" data-target="#member">Choose Data</button>
              <button class="btn btn-info">Restore Data</button>
            </div>
          </div>
      
        <div class="modal fade " id="member" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title" id="myModalLabel"><span><i class= "glyphicon glyphicon-user"></i></a></span>&nbspMember Data Reactivation<hr></h4>
              </div>
              <div class="modal-body">
                <div class="table-responsive">
                  <table id="example" class="table table-bordered table-hover">
                    <caption></caption>
                    <thead>
                      <tr role="row">
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;"></th>
                        <th class="sorting_asc" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Member ID: activate to sort column descending" style="width: 249px;">Code</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Type</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Class</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Brand</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Generic</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Manufacturer</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Form</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Size</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Dosage</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Packaging</th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;"><p>₱/Pc</p></th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;"><p>₱/Pack</p></th>
                        <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Description</th>
                      <tr>
                    </thead>

                    <tbody>
                      <tr>
                        <td><input type="checkbox"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="modal-footer">
                <a href="">
                  <button type="button" class="btn btn-primary">Restore</button>
                </a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              </div>
            </div>             
          </div>
        </div>

        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title" id="myModalLabel" style="text-align:center;color:#DA4453">!</h2>
              </div>
              <div class="modal-body">
                <p> <h4 style="text-align:center">{{Session::get('message')}} </h4> </p><br><br>
              </div>
            </div>
          </div>
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
@stop

@section('added-scripts')
  
@stop