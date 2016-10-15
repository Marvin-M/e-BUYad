@extends('....layout')

@section('page-title')
    Job Access
@stop

@section('other-scripts')
  {{HTML::script('bootflat-admin/js/datatables.min.js')}}
{{HTML::script('js/select2/js/select2.min.js')}}
{{HTML::style('js/select2/css/select2.min.css')}}
{{HTML::script('js/icheck/icheck.min.js')}}
{{HTML::style('js/icheck/flat/blue.css')}}

	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$('#example').DataTable();
		} );
    $("input").iCheck({
      checkboxClass: 'icheckbox_flat'
    });
	</script>
@stop

@section('content')
  <div class="panel-body">
    <div class="content-row">
    <center><h5 class="content-row-title" style="font-size:25px"><i class="glyphicon glyphicon-credit-card"></i>&nbsp Generate E-Buyad Card
      <hr>
      </h5></center>
      
      <div class = "panel pane-default">
        <div id="memlist" class="collapse in">
          <div class = "row">
            <div class="col-md-12">
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"> MEMBERS LIST </h3>
                </div>
                
                <div class="panel-body">
                  <div class="table-responsive">
                    <table id="example" class="table table-bordered table-hover">
                      <thead>
                        <tr role="row">
                          <th class="sorting_asc" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Member ID: activate to sort column descending" style="width: 249px;">Job Code</th>
                          <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Last Name: activate to sort column descending" style="width: 249px;">Job Name</th>
                          <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Description</th>
                          <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Options</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $counter = 0;

                          $resultMembers = DB::select("SELECT 
                                                        strEJCode, strEJName, strEJDescription
                                                        FROM tblEmpJobDesc
                                                        WHERE intStatus = 1");

                          foreach($resultMembers as $data){
                              if($counter%2 == 0){
                                  $trClass="even";
                              }else{
                                  $trClass="odd";
                              }
                              $counter++;


                              echo '<tr role="row" class="'.$trClass.'">';
                              echo '<td class="sorting_1">'.$data->strEJCode.'</td>';
                              echo '<td>'.$data->strEJName.'</td>';
                              echo '<td>'.$data->strEJDescription.'</td>';
                              echo '
                                  <td align="center">
                                          <button type="button" class="btn btn-success btn-block" href="#mem_form"
                                          data-toggle="collapse" onClick="genCard(\''.$data->strEJCode.'\')"><span class="glyphicon glyphicon-pencil"></span></button>
                                  </td>
                              ';
                              echo '</tr>';
                            }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="memform" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <div class="modal-dialog modal-md">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form  class="form-horizontal" role="form" method="post" action="{{URL::to('/utils/jobaccess/save')}}">
                  <div class="modal-body">
                    <div class="col-md-offset-2 col-md-8">
                      <input type="hidden" id="code" name="code">
                      <input type="hidden" id="mem" name="mem" value="0">
                      <input type="hidden" id="sale" name="sale"  value="0">
                      <input type="hidden" id="relo" name="relo" value="0">
                      <input type="hidden" id="egc" name="egc" value="0">
                      <input type="hidden" id="repo" name="repo" value="0">
                      <input type="hidden" id="query" name="query" value="0">
                      <input type="hidden" id="util" name="util" value="0">
                      <input type="hidden" id="maint" name="maint" value="0">
                      <br><br>
                      <div class="col-md-12">
                        <div class="col-md-9">
                          <label> &nbsp Set MODULES </label>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="col-md-9">
                          <input onclick="setCheck('tmem','mem')" type="checkbox" id="tmem" >
                          <label for="tmem"> &nbsp Membership </label>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="col-md-9">
                          <input onclick="setCheck('tsale','sale')" type="checkbox" id="tsale" name="prodcond" >
                          <label for="prodcond"> &nbsp Sales Transaction</label>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="col-md-9">
                          <input onclick="setCheck('trelo','relo')" type="checkbox" id="trelo" name="prodcond" >
                          <label for="prodcond"> &nbsp Reload</label>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="col-md-9">
                          <input onclick="setCheck('tegc','egc')" type="checkbox" id="tegc" name="prodcond" >
                          <label for="prodcond"> &nbsp EGC</label>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="col-md-9">
                          <input onclick="setCheck('trepo','repo')" type="checkbox" id="trepo" name="prodcond" >
                          <label for="prodcond"> &nbsp Reports</label>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="col-md-9">
                          <input onclick="setCheck('tquery','query')" type="checkbox" id="tquery">
                          <label for="prodcond"> &nbsp Queries</label>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="col-md-9">
                          <input onclick="setCheck('tutil','util')" type="checkbox" id="tutil" name="prodcond">
                          <label for="prodcond"> &nbsp Utilities</label>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="col-md-9">
                          <input onclick="setCheck('tmaint','maint')" type="checkbox" id="tmaint" name="prodcond">
                          <label for="prodcond"> &nbsp Maintenance</label>
                        </div>
                      </div>
                    </div>
                  </div><br>

                  <div class="modal-footer">
                    <button id="btnsubmit" type="submit" class="col-md-offset-10 col-md-2 btn btn-primary">OK</button>
                  </div>
                </form>
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
    function genCard(id){
      document.getElementById('code').value = id; 
      $("#memform").modal("show");
    }
  </script>
  <script>
    function setCheck(idfrom,idto){
      if(document.getElementById(idto).value == "0"){
        document.getElementById(idto).value = 1;
      }else{
        document.getElementById(idto).value = 0;
      }
    }
  </script>
@stop

