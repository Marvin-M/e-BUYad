@extends('......layout')

@section('page-title')
    Maintenance - Edit Branch Products
@stop

@section('other-scripts')
  {{HTML::script('bootflat-admin/js/datatables.min.js')}}
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function() {
			$('#prod').DataTable();
		} );
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
        <a href="{{URL::to('/maintenance/branchprod/add-product')}}" class="btn btn-primary">Add Products</a>
        <a href="#" class="btn btn-info">Edit Products</a>
      </div>
      <br>
      <div class="panel panel-default">
          <br>
          <!-- forms -->
          <div class ="row">
            <div class="col-md-offset-2 col-md-8">
              <div id="bran_form" class="collapse">
                <form id="branform" method="post" role="form" action="{{URL::to('/maintenance/branchprod/edit-product/update')}}" class="form-horizontal">
                  <input type="hidden" id = "code" name = "code">

                  <div id="namediv" class="form-group has-feedback">
                    <label class = "col-md-2 control-label">Product Name</label>

                    <div class="col-md-10">
                      <input type="text" id="name" class="form-control" name="name" readonly>
                      <span id="namespan" class="glyphicon form-control-feedback" aria-hidden="true"></span>
                      <p id="namep" class="help-block with-errors"></p>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="control-label col-sm-2">Quantity:</label>

                    <div class="col-sm-10">
                      <input type="number" id="qty" class="form-control" name="qty">
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="col-md-offset-2 col-md-10">
                      <button id="btnsubmit" type="submit" class="btn btn-info" data-toggle="modal" data-target="#Submit">Submit</button>

                      <button class="btn btn-info" type="cancel" onclick="clearForm()" href="#bran_form" data-toggle="collapse">Cancel</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
                        
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
                            <th>Quantity</th>
                            <th>ACTION</th>
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
                                                    br.intStock,
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
                                                  AND br.strBPBranCode = ?;", [Cache::get('branch')]);
                            
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

                              echo '<td>'.$data->intStock.'</td>';
                              echo '<td align="center">
                                    <table>
                                      <tr>
                                          <button type="button" class="btn btn-success btn-block" href="#bran_form" data-toggle="collapse" onClick="setFormData(\''.$data->strProdCode.'\',
                                                                                                                                                               \''.$namesung.'\',
                                                                                                                                                               \''.$data->intStock.'\')"><span class="glyphicon glyphicon-pencil"></span></button>
                                      </tr>
                                      
                                      <tr>
                                          <button type="button" class="btn btn-danger btn-block" data-target="#add_prod" data-toggle="modal" onClick="setDeleteData(\''.$data->strProdCode.'\',
                                                                                                                                                                 \''.$namesung.'\',
                                                                                                                                                                 \''.$data->intStock.'\')"><span class="glyphicon glyphicon-remove"></span></button>
                                      </tr>
                                    </table>
                                  </td>';
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

          <div id="add_prod" class="modal fade" role="dialog"><!-- Adding Products Modal -->
            <div class="modal-dialog">
              <div class="modal-dialog modal-md">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>

                  <br>
                  <form role="form" method="post" action="{{URL::to('/maintenance/branchprod/edit-product/delete')}}">
                  <div class="modal-body">
                      <input type="hidden" id="del_code" name="del_code">
                      <div class="form-group">
                        <label class="col-md-3 control-label"> Product Name: </label>

                        <div class="col-md-9">
                          <input type="text" class="form-control" id="del_name" readonly>
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-md-3 control-label"> Quantity: </label>

                        <div class="col-md-9">
                          <input type="number" class="form-control" id="del_qty" name="del_qty" readonly>
                        </div>
                      </div>
                  </div>

                  <br>

                  <div class="modal-footer">
                    <button type="submit" class="col-md-offset-10 col-md-2 btn btn-danger">DELETE</button>
                  </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

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

    function setFormData(code, name, qty){
      document.getElementById('name').value = name;
      document.getElementById('code').value = code;
      document.getElementById('qty').value = qty;
    }

    function setDeleteData(code, name, qty){
      document.getElementById('del_name').value = name;
      document.getElementById('del_code').value = code;
      document.getElementById('del_qty').value = qty;
    }
  </script>
@stop