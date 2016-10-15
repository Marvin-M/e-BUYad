@extends('....layout')

@section('page-title')
    Deactivate Card
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
    <center><h5 class="content-row-title" style="font-size:25px"><i class="glyphicon glyphicon-credit-card"></i>&nbsp Deactivate E-Buyad Card
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
                          <th class="sorting_asc" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Member ID: activate to sort column descending" style="width: 249px;">Member ID</th>
                          <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Card ID: activate to sort column descending" style="width: 249px;">Card ID</th>
                          <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Last Name: activate to sort column descending" style="width: 249px;">Last Name</th>
                          <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">First Name</th>
                          <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Middle Name: activate to sort column ascending" style="width: 187px;">Middle Name</th>
                          <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Options</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $counter = 0;

                          $resultMembers = DB::select("SELECT 
                                                          m.strMemCode, mc.strMCardId, m.strMemFName, m.strMemMName, m.strMemLName, ma.strMemAcctPinCode
                                                        FROM tblMember m 
                                                        LEFT JOIN tblMemCard mc
                                                          ON m.strMemCode = mc.strMCardCode
                                                        LEFT JOIN tblMemAccount ma
                                                          ON m.strMemCode = ma.strMemAcctCode
                                                        WHERE m.intStatus = 1 and mc.strMcardCode IS NOT NULL");

                          foreach($resultMembers as $data){
                              if($counter%2 == 0){
                                  $trClass="even";
                              }else{
                                  $trClass="odd";
                              }
                              $counter++;


                              $fname = str_replace("'", "&", $data->strMemFName);
                              $lname = str_replace("'", "&", $data->strMemLName);
                              $fullname = $lname.', '.$fname;


                              echo '<tr role="row" class="'.$trClass.'">';
                              echo '<td class="sorting_1">'.$data->strMemCode.'</td>';
                              echo '<td>'.$data->strMCardId.'</td>';
                              echo '<td>'.$data->strMemLName.'</td>';
                              echo '<td>'.$data->strMemFName.'</td>';
                              echo '<td>'.$data->strMemMName.'</td>';
                              echo '
                                  <td align="center">
                                          <button type="button" class="btn btn-danger btn-block" href="#mem_form"
                                          data-toggle="collapse" onClick="deleteCard(\''.$data->strMemCode.'\',\''.$data->strMCardId.'\',\''.$data->strMemAcctPinCode.'\')"><span class="glyphicon glyphicon-pencil"></span></button>
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

        <div id="deletecard" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <div class="modal-dialog modal-md">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form  class="form-horizontal" role="form" method="post" action="{{URL::to('/transaction/deactivate-card/delete')}}">
                  <div class="modal-body">
                    <div class="col-md-offset-2 col-md-8">
                      <input type="hidden" id="code" name="code">
                      <input type="hidden" id="card" name="card">
                      <input type="hidden" id="pin" name="pin">
                      <br><br>
                      <div class="form-group">
                        <label class="col-md-6 control-label">INPUT PINCODE:</label>
                        
                        <div class="col-md-6">
                          <input type="password" class="form-control" id="pino" name="pino" onkeypress="return isNumber(event)" maxlength="4" required>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-md-6 control-label">INPUT PINCODE AGAIN:</label>
                        <div class="col-md-6">
                          <input type="password" class="form-control" id="pint" onkeypress="return isNumber(event)" required maxlength="4">
                        </div>
                      </div>
                    </div>
                  </div><br>

                  <div class="modal-footer">
                    <button id="btnsubmit" type="submit" class="col-md-offset-10 col-md-2 btn btn-danger" disabled="">DELETE</button>
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
    $("#pino").blur(function(){
      if(document.getElementById('pino').value == document.getElementById('pin').value && document.getElementById('pino').value.length > 0){
        if(document.getElementById('pint').value == document.getElementById('pin').value && document.getElementById('pint').value.length > 0){
          document.getElementById('btnsubmit').removeAttribute('disabled');
        }else{
          document.getElementById('btnsubmit').setAttribute('disabled','');
        }
      }else{
        document.getElementById('btnsubmit').setAttribute('disabled','');
      }
    });

    $("#pint").blur(function(){
      if(document.getElementById('pint').value == document.getElementById('pin').value && document.getElementById('pint').value.length > 0){
        if(document.getElementById('pino').value == document.getElementById('pin').value && document.getElementById('pino').value.length > 0){
          document.getElementById('btnsubmit').removeAttribute('disabled');
        }else{
          document.getElementById('btnsubmit').setAttribute('disabled','');
        }
      }else{
        document.getElementById('btnsubmit').setAttribute('disabled','');
      }
    });
  </script>
  <script>
    function deleteCard(memid, cardid, pin){
      document.getElementById('code').value = memid;
      document.getElementById('card').value = cardid;
      document.getElementById('pin').value = pin;
      $("#deletecard").modal("show");
    }
  </script>
  <script>
    function isNumber(evt) {
      evt = (evt) ? evt : window.event;
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
          return false;
      }
      return true;
    }
  </script>
@stop

