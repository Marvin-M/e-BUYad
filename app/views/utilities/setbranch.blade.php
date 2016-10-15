@extends('....layout')

@section('page-title')
Utilities - Set Branch
@stop

@section('other-scripts')
{{HTML::script('bootflat-admin/js/datatables.min.js')}}
@stop

@section('content')
<div class="panel-body">
  <div class="content-row">  
    <center><h5 class="content-row-title" style="font-size:25px"><i class="glyphicon glyphicon-home"></i>&nbsp Branch
      <hr>
    </h5></center>

    <!-- forms -->
    <div class ="row">
      <div class="col-md-offset-1 col-md-9">
        <form role="form" class="form-horizontal" action="{{URL::to('/utils/set-branch/update-branch')}}">
          <div class="form-group">
            <label class = "col-md-offset-1 col-md-2 control-label">Set Branch:</label>
            <div class="col-md-5">
              <input type="hidden" name="branchname" id="branchname">
              <select onchange="setBranchName(this)" id = "branch" name="branch" class = "form-control" required>
              <option disabled selected value> -- SELECT BRANCH -- </option>
                <?php
                  $result = DB::select('SELECT strBranchName, strBranchCode FROM tblbranches WHERE intStatus = 1');

                  foreach($result as $data){
                    echo '<option value="'.$data->strBranchCode.'">'.$data->strBranchName.'</option>';
                  }
                ?>
              </select>
              @if(Cache::has('branch'))
              <script>
                $('#branch').val("{{Cache::get('branch')}}");
              </script>
              @endif
            </div>
            <div class="col-md-1">
              <button id="btnsubmit" type="submit" class="btn btn-info">SET BRANCH</button>
            </div>
          </div>
        </form>

        <div class="modal fade" id="prompt" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
</div>
@stop

@section('internal-scripts')
<script>
  @if(Session::get('message') != null)
    $('#prompt').modal('show');
  @endif
  function setBranchName(sel){
    document.getElementById('branchname').value = sel.options[sel.selectedIndex].text;
  }
</script>
@stop
