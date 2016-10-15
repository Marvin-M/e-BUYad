@extends('......layout')

@section('page-title')
    Maintenance - Add Branch Products
@stop

@section('other-scripts')
  {{HTML::script('bootflat-admin/js/datatables.min.js')}}
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$('#prod').DataTable();
		} );
	</script>
  <script>
    function hsBranded(x){
        if(document.getElementById('medtype').value == "1"){
            document.getElementById('branded').className = "collapse";
            document.getElementById('brand').removeAttribute("required");
            document.getElementById('brand').value = "";
        }else{
            document.getElementById('branded').className = "collapse in";
            document.getElementById('brand').setAttribute("required","");
        }
    }
  </script>
  <style>
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }

    input[type=number] {
      -moz-appearance:textfield;
    }
  </style>
@stop

@section('content')
  <div class="panel-body">
    <div class="content-row">
      <center><h5 class="content-row-title" style="font-size:25px">Branch Product Maintenance</h5></center>
      <center><h7 class="content-row-title" style="font-size:18px">
            @if(Cache::has('branch'))
              <?php
                  $branchname = DB::table('tblBranches')
                      ->select('strBranchName')
                      ->where('strBranchCode', Cache::get('branch'))
                      ->first();

                  echo $branchname->strBranchName;
              ?>
            @else
            No Branch Selected
            @endif
            <hr></h7></center>
      <div class="btn-group btn-group-justified">
        <a href="#" class="btn btn-info">Add Products</a>
        <a href="{{URL::to('/maintenance/branchprod/edit-product')}}" class="btn btn-primary">Edit Products</a>
      </div>
      <br>
      <div class="panel panel-default">
                        
        <div class="panel panel-default">
          <!-- datatables -->
          <div class = "row">
            <div class="col-md-12">
              <!--<hr style="border-width:1.1em">
              <center>
                <h5><span><i class="glyphicon glyphicon-list"></i></span> &nbspMEDICINE LIST</h5>
              </center>
              <hr>-->
              <div class="panel panel-info">
                <div class="panel-heading">
                  <h3 class="panel-title"> PRODUCT LIST </h3>
                </div>

                <div class="panel-body">
                  <div class="table-responsive"><!-- Product List Table -->
                      <table id="prod" class="table table-striped table-bordered table-hover dataTable no-footer" cellspacing="0" width="100%" role="grid" aria-describedby="prod_info" style="width: 100%;">
                        <thead>
                          
                          <tr role="row">
                            <th>Type</th>
                            <th>Products</th>
                            <th>ADD</th>
                          </tr>
                        </thead>
                                      
                        <tbody>
                          <?php
                            $counter = 0;

                            $results = DB::select("SELECT 
                                                    p.strProdCode,
                                                    p.strProdType,
                                                    b.strPMBranName,
                                                    (
                                                      SELECT group_concat(g.strPMGenName SEPARATOR ' ') 
                                                      FROM tblmedgennames mg LEFT JOIN tblprodmedgeneric g ON mg.strMedGenGenCode = g.strPMGenCode
                                                      WHERE mg.strMedGenMedCode = m.strProdMedCode GROUP BY mg.strMedGenMedCode
                                                    ) as 'GenNames',
                                                    t.strPMTheraClassName,
                                                    mn.strPMManuName,
                                                    f.strPMFormName,
                                                    pk.strPMPackName,
                                                    concat(m.decProdMedSize, ' ', u.strUOMName) as 'MedSize',
                                                    
                                                    nm.strProdNMedName,
                                                    c.strNMedCatName,
                                                    concat_ws(' ', g.strGenSizeName, s.decNMStanSize, un.strUOMName) as 'NMedSize',
                                                    
                                                    br.strBPBranCode
                                                      
                                                  FROM tblproducts p

                                                  LEFT JOIN tblprodmed m
                                                    ON p.strProdCode = m.strProdMedCode
                                                  LEFT JOIN tblprodnonmed nm
                                                    ON p.strProdCode = nm.strProdNMedCode
                                                  LEFT JOIN tblprodmedbranded b
                                                    ON m.strProdMedBranCode = b.strPMBranCode
                                                  LEFT JOIN tblpmtheraclass t
                                                    ON m.strProdMedTheraCode = t.strPMTheraClassCode
                                                  LEFT JOIN tblpmmanufacturer mn
                                                    ON m.strProdMedManuCode = mn.strPMManuCode
                                                  LEFT JOIN tblpmform f
                                                    ON m.strProdMedFormCode = f.strPMFormCode
                                                  LEFT JOIN tbluom u 
                                                    ON m.strProdMedUOMCode = u.strUOMCode
                                                  LEFT JOIN tblpmpackaging pk
                                                    ON m.strProdMedPackCode = pk.strPMPackCode
                                                    
                                                  LEFT JOIN tblnmedcategory c
                                                    ON nm.strProdNMedCatCode = c.strNMedCatCode
                                                  LEFT JOIN tblnmedgeneral gt
                                                    ON nm.strProdNMedCode = gt.strNMGenCode
                                                  LEFT JOIN tblgensize g
                                                    ON gt.strNMGenSizeCode = g.strGenSizeCode
                                                  LEFT JOIN tblnmedstandard s
                                                    ON nm.strProdNMedCode = s.strNMStanCode
                                                  LEFT JOIN tbluom un
                                                    ON s.strNMStanUOMCode = un.strUOMCode
                                                  LEFT JOIN tblBranProd br
                                                    ON p.strProdCode = br.strBPProdCode
                                                    
                                                  WHERE p.intStatus = 1
                                                  AND
                                                    ( br.strBPBranCode <> ? OR
                                                      br.strBPBranCode IS NULL);", [Cache::get('branch')]);
                            
                            foreach($results as $data){
                              if($counter%2 == 0){
                                $trClass="even";
                              }else{
                                $trClass="odd";
                              }

                              $counter++;

                              echo '<tr role="row" class="'.$trClass.'">';
                              if($data->strProdType == "0"){
                                echo '<td>MEDICINE</td>';
                              }else{
                                echo '<td>NON-MEDICINE</td>';
                              }

                              if($data->strProdType == 0){
                                $namesung = $data->strPMBranName.' ('.$data->GenNames.') '.$data->MedSize;
                                echo '<td>'.$namesung.
                                     '<br>'.$data->strPMManuName.
                                     '<br>'.$data->strPMFormName.' - '.$data->strPMPackName.
                                     '</td>';
                              }else{
                                $namesung = $data->strProdNMedName.' '.$data->NMedSize;
                                echo '<td>'.$namesung.'</td>';
                              }
                              echo '<td><button type="button" onclick="setAddingMessage(\''.$data->strProdCode.'\',\''.$namesung.'\')" class="btn btn-primary btn-block" data-target="#add_prod" data-toggle="modal" >+</button></td>';
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

          <div id="add_prod" class="modal fade" role="dialog"><!-- DELETING Products Modal -->
            <div class="modal-dialog">
              <div class="modal-dialog modal-md">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>

                  <br>
                  <form role="form" method="post" action="{{URL::to('/maintenance/branchprod/add-product/submit')}}">
                  <div class="modal-body">
                      <input type="hidden" id="us_code" name="us_code">
                      <div class="form-group">
                        <label class="col-md-3 control-label"> Product Name: </label>

                        <div class="col-md-9">
                          <input type="text" class="form-control" id="us_name" readonly>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-md-3 control-label"> Quantity: </label>

                        <div class="col-md-9">
                          <input type="number" class="form-control" id="us_qty" name="us_qty" maxlength="100">
                        </div>
                      </div>
                  </div>

                  <br>

                  <div class="modal-footer">
                    <button type="submit" class="col-md-offset-10 col-md-2 btn btn-primary">OK</button>
                  </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!--MESSAGE MODAL-->
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
  <script>//message
    @if(Session::get('message') != null)
      $('#prompt').modal('show');
    @endif
  </script>
  <script>
    function setAddingMessage(code, name){
      document.getElementById('us_name').value = name;
      document.getElementById('us_code').value = code;
      document.getElementById('us_qty').focus();
    }
  </script>
@stop