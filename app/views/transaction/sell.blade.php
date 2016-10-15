@extends('....layout')

@section('page-title')
    Make A Sale
@stop

@section('other-scripts')
{{HTML::script('bootflat-admin/js/datatables.min.js')}}
{{HTML::script('qr/qcode-decoder.min.js')}}
{{HTML::script('js/select2/js/select2.min.js')}}
{{HTML::style('js/select2/css/select2.min.css')}}

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#example').DataTable();
    $('#example2').DataTable();
    $('#example3').DataTable();
    $("#retuid").select2();
	} );
</script>
@stop

@section('content')
<div class="panel-body">
  <div class="content-row">
    <center><h5 class="content-row-title" style="font-size:25px"><i class="glyphicon glyphicon-shopping-cart"></i>&nbsp Make A Sale
      <hr>
      </h5></center>

    <!-- OR# and Customer --> 
    <div class="panel panel-default col-md-12">
        <form role="form" class="form-horizontal">
          <div class="col-md-3">
            <label class="control-label">Transaction ID:</label><br>
            <input id="transid" name="transid" type="text" readonly value="{{ $code }}">
          </div>
          <div class="col-md-3">
            <label class="control-label">Date and Time:</label><br>
            <?php
              echo '<input id="transid" name="transid" type="text" readonly value="'.date("Y-m-d").' '.date("h:ia",strtotime("+8 Hours")).'">';
            ?>
          </div>
          <div class="col-md-3">
            <label class="control-label">Pharmacist:</label><br>
            <input id="transid" name="transid" type="text" readonly value="Luis Guballo">
          </div>
          <div class="col-md-3">
            <label class="control-label">Branch:</label><br>
            <input id="transid" name="transid" type="text" readonly value="{{ $branchname }}">
          </div>
        </form>
    </div>

    <!-- END -->

    <!-- Product List and Chosen Products List -->
    <div class="panel panel-default col-md-12">
      <div class="panel-body col-md-6">

        <div class="btn-group btn-group-justified">
          <a id="medmenu" class="btn btn-info" onclick="showMed();">Medicine</a>
          <a id="nmedmenu" class="btn btn-primary" onclick="showNonMed();">Non-medicine</a>
          <a id="egcprods" class="btn btn-info hidden">Electronic Gift Products</a>
        </div>

        <br>
        <div class="panel panel-header">
          <div id="medtbl" class="collapse in">
            <div class="col-md-12">
              <form role="form" class="form-horizontal">
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="form-group">
                      <label class="control-label col-md-4">Filter by:</label>
                      <div class="col-md-8">
                        <select id = "mfilterby" name="mfilterby" class = "form-control">
                          <option disabled selected value>SEARCH</option>
                          <option value="0">Therapeutic Class</option>
                          <option value="1">Manufacturer</option>
                          <option value="2">Form</option>
                          <option value="3">Packaging</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label col-md-4">Look for:</label>
                    <div class="col-md-8">
                      <select id = "mlookfor" name="mlookfor" class = "form-control">
                        <option disabled selected value>SEARCH</option>
                        <option value="">ALL</option>
                      </select>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="table-responsive">
              <table id="example" class="table table-bordered table-hover">
                <thead>
                  <tr role="row">
                    <th class="sorting_asc" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Member ID: activate to sort column descending" style="width: 249px;">Name</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Size</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Price</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Add</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Thera</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Manu</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Form</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Pack</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Stock</th>
                  </tr>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $counter = 0;
                    $results = DB::select("SELECT 
                                              m.strProdMedCode,
                                              b.strPMBranName, 
                                              (
                                                SELECT group_concat(g.strPMGenName SEPARATOR ' ') 
                                                    FROM tblmedgennames mg LEFT JOIN tblprodmedgeneric g ON mg.strMedGenGenCode = g.strPMGenCode
                                                    WHERE mg.strMedGenMedCode = m.strProdMedCode GROUP BY mg.strMedGenMedCode
                                              ) as 'GenNames',
                                              t.strPMTheraClassName,
                                              mn.strPMManuName,
                                              f.strPMFormName,
                                              p.strPMPackName, 
                                              m.decProdMedSize, 
                                              u.strUOMName, 
                                              (
                                                  SELECT pr.decProdPricePerPiece
                                                  FROM tblProdPrice pr
                                                  WHERE pr.strProdPriceCode = m.strProdMedCode
                                                  AND pr.dtmUpdated < now()
                                                  ORDER BY pr.dtmUpdated DESC LIMIT 1
                                              ) as 'PricePerPiece',
                                              (
                                                  SELECT pr.decPricePerPackage
                                                  FROM tblProdPrice pr
                                                  WHERE pr.strProdPriceCode = m.strProdMedCode
                                                  AND pr.dtmUpdated < now()
                                                  ORDER BY pr.dtmUpdated DESC LIMIT 1
                                              ) as 'PricePerPackage',
                                              (
                                                  SELECT pr.intQtyPerPackage
                                                  FROM tblProdPrice pr
                                                  WHERE pr.strProdPriceCode = m.strProdMedCode
                                                  AND pr.dtmUpdated < now()
                                                  ORDER BY pr.dtmUpdated DESC LIMIT 1
                                              ) as 'PiecePerPackage',
                                          br.intStock

                                          FROM tblProdMed m
                                          LEFT JOIN tblProdMedBranded b
                                            ON m.strProdMedBranCode = b.strPMBranCode
                                          LEFT JOIN tblpmtheraclass t
                                            ON m.strProdMedTheraCode = t.strPMTheraClassCode
                                          LEFT JOIN tblpmmanufacturer mn
                                            ON m.strProdMedManuCode = mn.strPMManuCode
                                          LEFT JOIN tblpmform f
                                            ON m.strProdMedFormCode = f.strPMFormCode
                                          LEFT JOIN tblPMPackaging p
                                            ON m.strProdMedPackCode = p.strPMPackCode
                                          LEFT JOIN tblUOM u
                                            ON m.strProdMedUOMCode = u.strUOMCode
                                          LEFT JOIN tblProducts pd
                                            ON m.strProdMedCode = pd.strProdCode
                                          LEFT JOIN tblBranProd br
                                            ON m.strProdMedCode = br.strBPProdCode
                                         

                                          WHERE pd.intStatus = 1
                                          AND br.intStock > 0
                                          AND br.strBPBranCode = ?;", [Cache::get('branch')]);
                      foreach($results as $data){
                            if($counter%2 == 0){
                                $trClass="even";
                            }else{
                                $trClass="odd";
                            }
                            $counter++;

                            echo '<tr role="row" class="'.$trClass.'">';
                            echo '<td><b>'.$data->strPMBranName.'</b> ('.$data->GenNames  .')</td>';
                            echo '<td>'.$data->strPMPackName.' - '.$data->decProdMedSize.' '.$data->strUOMName.'</td>';

                            echo '<td>'.$data->PricePerPiece.' / Piece <br/>
                                          '.$data->PricePerPackage.' / Package</td>';

                            echo '<td><button class="btn btn-success btn-block" data-toggle="modal" data-target="#quantity" '.
                                  'onClick="setModalForm(\''.
                                    $data->strProdMedCode.'\',\''.
                                    $data->strPMBranName.' '.$data->GenNames.'\','.
                                    $data->PricePerPiece.','.
                                    $data->PricePerPackage
                                  .',0,'.$data->intStock.')">+</button></td>';
                            echo '<td>'.$data->strPMTheraClassName.'</td>';
                            echo '<td>'.$data->strPMManuName.'</td>';
                            echo '<td>'.$data->strPMFormName.'</td>';
                            echo '<td>'.$data->strPMPackName.'</td>';
                            echo '<td>'.$data->intStock.'</td>';
                            echo '</tr>';
                        }
                  ?>
                </tbody>
              </table>
            </div>
          </div>

          <div id="nmedtbl" class="collapse">
            <div class="col-md-offset-2 col-md-8">
              <form role="form" class="form-horizontal">
                <div class="form-group">
                  <label class="control-label col-md-4">Category:</label>
                  <div class="col-md-8">
                    <select id = "nlookfor" name="nlookfor" class = "form-control">
                      <option disabled selected value>SEARCH</option>
                      <option value="">ALL</option>
                    </select>
                  </div>
                </div>
              </form>
            </div>

            <div class="table-responsive">
              <table id="example2" class="table table-bordered table-hover">
                <thead>
                  <tr role="row">
                    <th class="sorting_asc" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Member ID: activate to sort column descending" style="width: 249px;">Code</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Name</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Size</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Price</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Option: activate to sort column ascending" style="width: 147px;">Add</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Cate</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Stock</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $counter = 0;
                    $results = DB::select("SELECT nm.strProdNMedCode, 
                                                  nm.strProdNMedName, 
                                                  pr.decProdPricePerPiece,
                                                  c.strNMedCatName, 
                                                  s.strGenSizeName, 
                                                  st.decNMStanSize, 
                                                  u.strUOMName,
                                                  br.intStock

                                          FROM tblProdNonMed nm
                                          LEFT JOIN tblProducts p
                                            ON nm.strProdNMedCode = p.strProdCode
                                          LEFT JOIN tblnmedcategory c
                                            ON nm.strProdNMedCatCode = c.strNMedCatCode
                                          LEFT JOIN tblProdPrice pr
                                            ON nm.strProdNMedCode = pr.strProdPriceCode
                                          LEFT JOIN tblNMedGeneral g
                                            ON nm.strProdNMedCode = g.strNMGenCode
                                          LEFT JOIN tblGenSize s
                                            ON g.strNMGenSizeCode = s.strGenSizeCode
                                          LEFT JOIN tblNMedStandard st
                                            ON nm.strProdNMedCode = st.strNMStanCode
                                          LEFT JOIN tblUOM u
                                            ON st.strNMStanUOMCode = u.strUOMCode
                                          LEFT JOIN tblBranProd br
                                            ON nm.strProdNMedCode = br.strBPProdCode

                                          WHERE p.intStatus = 1
                                          AND br.intStock > 0
                                          AND br.strBPBranCode = ?;", [Cache::get('branch')]);
                      foreach($results as $data){
                            if($counter%2 == 0){
                                $trClass="even";
                            }else{
                                $trClass="odd";
                            }
                            $counter++;

                            echo '<tr role="row" class="'.$trClass.'">';
                            echo '<td>'.$data->strProdNMedCode.'</td>';
                            echo '<td>'.$data->strProdNMedName.'</td>';
                            echo '<td>'.$data->strGenSizeName.' '.$data->decNMStanSize.' '.$data->strUOMName.'</td>';
                            echo '<td>'.$data->decProdPricePerPiece.'</td>';
                            echo '<td><button class="btn btn-success btn-block" data-toggle="modal" data-target="#quantity" onclick="setModalForm(\''.
                                    $data->strProdNMedCode.'\',\''.
                                    $data->strProdNMedName.'\','.
                                    $data->decProdPricePerPiece
                                    .',0,1,'.
                                    $data->intStock.')">+</button></td>';
                            echo '<td>'.$data->strNMedCatName.'</td>';
                            echo '<td>'.$data->intStock.'</td>';
                            echo '</tr>';
                        }
                  ?>
                </tbody>
              </table>
            </div>
          </div>

          <div class="modal fade" id="quantity" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h6 class="modal-title" id="myModalLabel">Set Quantity</h6>
                </div>
                <div class="modal-body">

                  <form role="form" class="form-horizontal col-md-6-offset-3">
                    <div class="form-group">
                      <label class="control-label col-sm-4">Quantity:</label>
                      <div class="col-sm-6">
                        <input type="number" class="form-control stepper-input" min="1" max="10000" onkeypress="return (event.charCode == 8 || event.charCode == 0) ? null : event.charCode >= 48 && event.charCode <= 57" id="pqty">
                      </div>
                    </div>
                    <div id="showpcpk" class="hidden">
                      <div class="form-group">
                        <div class="radio">
                          <label class="control-label col-md-5">
                            <input onclick="setFinalPrice(0)" type="radio" name="qtydisc" id="qtypc" value="piece" checked>Piece
                          </label>
                          <label class="control-label col-md-3">
                            <input onclick="setFinalPrice(1)" type="radio" name="qtydisc" id="qtypk" value="package">Package
                          </label>
                        </div>
                      </div>
                    </div>
                  </form>
                  <input type="hidden" id="pcode">
                  <input type="hidden" id="pname">
                  <input type="hidden" id="pcprice">
                  <input type="hidden" id="pkprice">
                  <input type="hidden" id="finalprice">
                  <input type="hidden" id="modeprice" value="0">
                  <input type="hidden" id="pstock">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-danger" onclick="addItem()"  data-dismiss="modal" >Add</button>
                </div>
              </div>
            </div>
          </div>

          <div id="prodegctbl" class="collapse in hidden">
            <div class="table-responsive">
              <table id="example3" class="table table-bordered table-hover">
                <thead>
                  <tr role="row">
                    <th class="sorting_asc" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Member ID: activate to sort column descending" style="width: 249px;">Name</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Quantity</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Add</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Code</th>
                    <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="First Name: activate to sort column ascending" style="width: 400px;">Stock</th>
                  </tr>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- second half-->
      <div class="panel-body col-md-6">
        <div class="panel panel-header">
          <center>
            <h5>Selected Products</h5>
          </center>
        </div>
        <div class="panel-body" style="height:500px;">
            <table id="purchased" class="table table-condensed table-hover">
              <tr>
                <thead>
                  <tr>
                    <th>Remove</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th id='purchaseprice'>Price</th>
                    <th></th>
                    <th>Qty</th>
                    <th id='purchasetotal'>Total</th>
                  </tr>
                </thead>
              </tr>
              <tr>
                <tbody>
                </tbody>
              </tr>
            </table>
        </div>
      </div>
    </div>
    <!-- END -->

    <div class="col-md-6">
    </div>

    <div class="col-md-6">
      <div id="fgdisc" class="form-group">
        <div class="checkbox">
            <div class="col-md-6-offset-3 input-group">
              <input type="checkbox" id="discchk" onclick="setDiscount();">  <label for="discchk"> APPLY COMPANY DISCOUNT: </label>
              <select disabled onchange="addDiscount();" id="disc" class="form-control col-md-6">
                <option selected disabled="" value="0"> --CHOOSE DISCOUNT-- </option>
                <?php
                  $results = DB::select('SELECT dblDiscPerc, decDiscAmt, strDiscName FROM tblDiscounts WHERE intStatus = 1');

                  $discTitle = "";
                  foreach($results as $data){
                    if($data->decDiscAmt == 0){
                      $name = $data->dblDiscPerc * 100;
                      $name = strval($name).'%';
                      echo '<option value="'.$data->dblDiscPerc.'">'.$data->strDiscName.' ('.$name.')</option>';
                    }else{
                      $name = strval(round($data->decDiscAmt,2));
                      echo '<option value="'.$data->decDiscAmt.'">'.$data->strDiscName.' (Php'.$name.')</option>';
                    }
                  }
                ?>
              </select>
              <select class="hidden" id="hiddendiscid">
                <?php
                  $results = DB::select('SELECT strDiscCode FROM tblDiscounts WHERE intStatus = 1');

                  $discTitle = "";
                  foreach($results as $data){
                    echo '<option value="'.$data->strDiscCode.'"></option>';
                  }
                ?>
              </select>
            </div>
        </div>
      </div>
      <div id="fgsumm" class="form-group">
        <input type="hidden" id="rawtotal" value="0">
        <div id="showreturnamt" class="input-group hidden"  style="width: 800px;">
          <input type="hidden" id="returncode" name="returncode">
          <label class="control-label col-md-2">Returns Amount:</label> 
          <div class="col-md-5">
            <input class="form-control" type="text" id="sumreturn" name="sumreturn" readonly>
          </div>
        </div>
        <div id="showegcamount" class="input-group hidden" style="width: 800px;">
          <label class="control-label col-md-2">
            EGC AMOUNT:
          </label>
          <div class="col-md-5">
            <div class="input-group">
              <input type="text" class="form-control" id="sumegc" readonly>
            </div>
          </div>
        </div>
        <div class="input-group" style="width: 800px;">
          <label class="control-label col-md-2">
            Discount:
          </label>
          <div class="col-md-5">
            <input id="discamt" type="text"  class="form-control" value="0" readonly>
          </div>
        </div>
        <div class="input-group" style="width: 800px;">
          <label class="control-label col-md-2">
            VAT (Inclusive):
          </label>
          <div class="col-md-5">
            <div class="input-group">
            <input type="text"  class="form-control" value="12" readonly>
            <span class="input-group-addon">%</span>
            </div>
          </div>
        </div>
        <div class="input-group" style="width: 800px;">
          <div class="form-group">
          <label class="control-label col-md-2">
            Subtotal:
          </label>
          <div class="col-md-5">
            <input id="subt" type="text"  class="form-control" value="0" readonly>
          </div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-12">
          <center>
          <h5>
            PAYMENT
          </h5>
          </center>
        </div>

        <div class="col-md-12">
          <center>
          <button type="button" id="btncash" onclick="setFormSubmit('cash')" data-toggle="modal" data-target="#cash" class="btn btn-success col-md-offset-1 col-md-5" >Cash</button>
          <button type="button" id="btncard" data-toggle="modal" class="btn btn-info col-md-offset-1 col-md-5" onclick="checkGrandTotal()">E-BUYad Card</button><br><br><br>
          <button type="button" id="btnegc" data-toggle="modal" data-target="#egclogin" class="btn btn-warning col-md-offset-1 col-md-5" >EGC</button>
          <button type="button" id="btnreturn" onclick="showReturnsModal()" class="btn btn-danger col-md-offset-1 col-md-5">Returns</button>
          </center>
        </div>

        <div class="modal fade" id="cash" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h6 class="modal-title" id="myModalLabel">Cash Payment</h6>
              </div>
              <div class="modal-body">
                <center>
                <label>Amount Tendered:</label> 
                <div class="input-group">
                <span class="input-group-addon">Php</span>
                <input  class="form-control" type="number" id="amtpaid" name="amtpaid" min="0.00" step="0.01" pattern="/^[0-9]+(\.[0-9]{1,2})?$/">
                </div>
                </center>
              </div>
              <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-danger" onclick="checkCashAmount()">Confirm</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="transum" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h6 class="modal-title" id="myModalLabel">Transaction Summary</h6>
              </div>
              <form id="transdet" role="form" method="post" action="">
                <div class="modal-body">
                  <!-- the transaction id -->
                  <input type="hidden" name="prodtransid" id="prodtransid" value="{{ $code }}">
                  <!-- discount name -->
                  <input type="hidden" name="proddisc" id="proddisc">
                  <!-- discount rate -->
                  <input type="hidden" name="proddiscamt" id="proddiscamt" value="0">
                  <!-- raw total -->
                  <input type="hidden" name="prodrawtotal" id="prodrawtotal" value="0">
                  <!-- product codes -->
                  <input type="hidden" name="prodcode" id="prodcode">
                  <!-- piece or package -->
                  <input type="hidden" name="prodpricemode" id="prodpricemode">
                  <!-- product names -->
                  <input type="hidden" name="prodname" id="prodname">
                  <!-- product prices -->
                  <input type="hidden" name="prodprice" id="prodprice">
                  <!-- quantities -->
                  <input type="hidden" name="prodqty" id="prodqty">
                  <input type="hidden" name="prodamt" id="prodamt">
                  <input type="hidden" name="prodmemcode" id="prodmemcode">
                  <input type="hidden" name="prodmemname" id="prodmemname">
                  <input type="hidden" name="prodbal" id="prodbal">
                  <input type="hidden" name="prodpts" id="prodpts">

                  <!-- discount amount -->
                  <input type="hidden" name="discamt" id="discamt" value="0">
                  <!-- additional cash -->
                  <input type="hidden" name="addcash" id="addcash" value="0">

                  <div id="heading">
                    <center>
                      <div class="input-group">
                        <center>
                        <label>Subtotal:</label> 
                        <div class="input-group">
                        <input  class="form-control" type="text" id="sumsubt" name="sumsubt" readonly>
                        </div>
                        </center>
                      </div>
                      <div class="input-group">
                        <center>
                        <label>Discount:</label> 
                        <div class="input-group">
                        <input  class="form-control" type="text" id="sumdisc" name="sumdisc" readonly>
                        </div>
                        </center>
                      </div>
                      <div class="input-group">
                        <center>
                        <label>VAT Rate: (INCLUSIVE)</label> 
                        <div class="input-group">
                        <input  class="form-control" type="text" id="sumtax" name="sumtax" readonly>
                        </div>
                        </center>
                      </div>
                      <!--
                      <div class="input-group">
                        <center>
                        <label>Grand Total:</label> 
                        <div class="input-group">
                        <input  class="form-control" type="text" id="sumgrant" name="sumgrant" readonly>
                        </div>
                        </center>
                      </div>
                      -->
                      <div class="input-group">
                        <center>
                        <label>Amount Tendered:</label> 
                        <div class="input-group">
                        <input  class="form-control" type="text" id="sumamt" name="sumamt"  readonly>
                        </div>
                        </center>
                      </div>
                      <div class="input-group">
                        <center>
                        <label>Change:</label> 
                        <div class="input-group">
                        <input  class="form-control" type="text" id="sumchan" name="sumchan" readonly>
                        </div>
                        </center>
                      </div>
                    </center>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-danger" onclick="checkSeniorDiscount()">Save & Print Receipt</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="modal fade" id="cardid" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h6 class="modal-title" id="myModalLabel">SCAN QR CODE</h6>
              </div>
              <div class="modal-body">
                <div id="heading">
                  <center>
                    <div class="input-group">
                    <video autoplay height = "500" width = "450"></video>
                    <h5 id="camstatus">PLEASE HOVER YOUR QR CODE TO THE CAMERA</h5>
                    <script type="text/javascript">

                      (function () {
                        'use strict';

                        var qr = new QCodeDecoder();
                        if (!(qr.isCanvasSupported() && qr.hasGetUserMedia())) {
                          alert('Your browser doesn\'t match the required specs.');
                          throw new Error('Canvas and getUserMedia are required');
                        }

                        var video = document.querySelector('video');
                        var reset = document.querySelector('#reset');
                        var stop = document.querySelector('#stop');


                        function resultHandler (err, result) {
                          if (err){
                            return console.log(err.message);
                            document.getElementById('camstatus').value = "SCANNING FAILURE"
                          }
                          else{
                            document.getElementById('camstatus').innerHTML = "SCANNING SUCCESSFUL";
                            $.ajax({
                                url: '/transaction/sell/verify-qr',
                                type: 'GET',
                                data: {
                                    memcode: result.substr(5,8),
                                    cardid: result.substr(0,5)
                                },
                                success: function(data){
                                  if(data.length > 0){
                                    document.getElementById('edocnip').value = data[0]['strMemAcctPinCode'];
                                    document.getElementById('edocmem').value = result.substr(5,8);
                                  }else{
                                    alert('INVALID QR CODE');
                                  }
                                }, 
                                  error: function(xhr, status, error) {
                                    alert('ERROR IN READING QR CODE');
                                  }
                            });
                          }
                        }

                        // prepare a canvas element that will receive
                        // the image to decode, sets the callback for
                        // the result and then prepares the
                        // videoElement to send its source to the
                        // decoder.

                        qr.decodeFromCamera(video, resultHandler);


                        // attach some event handlers to reset and
                        // stop whenever we want.

                        reset.onclick = function () {
                          qr.decodeFromCamera(video, resultHandler);
                        };

                        stop.onclick = function () {
                          qr.stop();
                        };

                      })();
                      </script>
                    </div>
                  </center>
                </div>
                <div id="details">
                </div>
                <div id="products">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger"  data-toggle="modal" data-target="#cardpin" data-dismiss="modal" onclick="document.getElementById('camstatus').innerHTML = 'PLEASE HOVER YOUR QR CODE TO THE CAMERA'">Confirm</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="cardpin" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h6 class="modal-title" id="myModalLabel">PLEASE ENTER PIN CODE</h6>
              </div>
              <div class="modal-body">
                <div id="heading">
                  <center>
                    <div class="input-group">
                      <center>
                      <label>PIN CODE:</label> 
                      <div class="input-group">
                      <input  class="form-control" type="password" id="nipcode" maxlength="4">
                      <input type="hidden" id="edocnip">
                      <input type="hidden" id="edocmem">
                      </div>
                      </center>
                    </div>
                  </center>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger" data-dismiss="modal" onclick="checkPinCode()"  >Confirm</button> 
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="carddet" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h6 class="modal-title" id="myModalLabel">Member Account Details</h6>
              </div>
              <div class="modal-body">
                <div id="heading">
                  <center>
                    <div class="input-group">
                      <center>
                      <label>Member Name:</label> 
                      <div class="input-group">
                      <input  class="form-control" type="text" id="memname" readonly>
                      </div>
                      </center>
                    </div>
                    <div class="input-group">
                      <center>
                      <label>Balance:</label> 
                      <div class="input-group">
                      <input min="0" class="form-control" type="number" id="memcurbal" readonly>
                      </div>
                      </center>
                    </div>
                    <div class="input-group">
                      <center>
                      <label>Balance:</label> 
                      <div class="input-group">
                      <input onblur="showLoadAddCash()" min="0" class="form-control" type="number" id="membal">
                      </div>
                      </center>
                    </div>
                    <div id="loadaddcash" class="input-group hidden">
                      <center>
                      <label>Additional Cash:</label> 
                      <div class="input-group">
                      <input class="form-control" type="number" id="memaddcash" min="0" value="0">
                      </div>
                      </center>
                    </div>
                  </center>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" data-toggle="modal" href="#reload">Reload</button>
                <button type="submit" class="btn btn-danger" onclick="checkBalance();" data-dismiss="modal">Load Payment</button>
                <!-- <button type="submit" class="btn btn-danger" data-dismiss="modal">Cash</button> 
                <button type="submit" class="btn btn-danger" data-dismiss="modal">Load + Cash</button>  -->
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="reload" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h6 class="modal-title" id="myModalLabel">RELOAD</h6>
              </div>
              <div class="modal-body">
                <center>
                <label>Amount Tendered:</label> 
                <div class="input-group">
                <span class="input-group-addon">Php</span>
                <input  class="form-control" type="number" id="rload" name="rload" min="0.00" step="0.01" pattern="/^[0-9]+(\.[0-9]{1,2})?$/">
                </div>
                </center>
              </div>
              <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-danger" onclick="reloadMember()">Confirm</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="returnsinfo" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h6 class="modal-title" id="myModalLabel">RETURNS</h6>
              </div>
              <div class="modal-body">
                <form role="form" class="form-horizontal">
                  <div class="col-md-12">
                    <label class="col-md-4">Select Return Code:</label>
                    <div class="col-md-8">
                      <select id="retuid" name="retuid" class="form-control" onchange="abx()" style="width:300px">
                        <option disabled selected value="0">--- SELECT RETURNS ID ---</option>
                          <?php
                            $result = DB::select('SELECT decTotalAmount, strReturnCode FROM tblReturns WHERE isUsed = 0');

                            foreach($result as $data){
                              echo '<option value="'.$data->decTotalAmount.'">'.$data->strReturnCode.'</option>';
                            }
                          ?>
                      </select>
                    </div>
                  </div>
                </form>
                <br><br>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="returnssum" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h6 class="modal-title" id="myModalLabel">RETURNS SUMMARY</h6>
              </div>
              <div class="modal-body">
                <form role="form" class="form-horizontal" method="post" action="{{URL::to('/transaction/sell/return')}}">

                  <!-- the transaction id -->
                  <input type="hidden" name="rettransid" id="rettransid" value="{{ $code }}">
                  <!-- product codes -->
                  <input type="hidden" name="retprodcode" id="retprodcode">
                  <!-- piece or package -->
                  <input type="hidden" name="retpricemode" id="retpricemode">
                  <!-- product names -->
                  <input type="hidden" name="retname" id="retname">
                  <!-- product prices -->
                  <input type="hidden" name="retprice" id="retprice">
                  <!-- quantities -->
                  <input type="hidden" name="retqty" id="retqty">
                  <input type="hidden" name="retamtt" id="retamtt">

                  <div class="col-md-12">
                    <label class="col-md-4">Return Code:</label>
                    <div class="col-md-8">
                      <input type="text" class="form-control" id="retsumcode" name="retsumcode" readonly>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <label class="col-md-4">Return Amount:</label>
                    <div class="col-md-8">
                      <input type="text" class="form-control" id="retamt" name="retamt" readonly>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <label class="col-md-4">Total Amount:</label>
                    <div class="col-md-8">
                      <input type="text" class="form-control" id="rettotal" name="rettotal" readonly>
                    </div>
                  </div>
                  <div id="retaddcash" class="col-md-12 hidden">
                    <label class="col-md-4">Additional Cash:</label>
                    <div class="col-md-8">
                      <input type="number" class="form-control" id="retcash" name="retcash">
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="col-md-7"></div>
                    <div class="col-md-5">
                      <button type="submit" class="btn btn-block btn-danger" id="btnSubmitReturn" onclick="collectReturnItems()">Confirm Returns</button> 
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="egclogin" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h6 class="modal-title" id="myModalLabel">ENTER EGC CREDENTIALS</h6>
              </div>
              <div class="modal-body">
                <form role="form" class="form-horizontal">
                  <div class="col-md-12">
                    <label class="col-md-4">EGC ID:</label>
                    <label class="col-md-3"> EGC </label>
                    <div class="col-md-5">
                      <input type="text" class="form-control col-md-8" id="legcid" name="legcid" maxlength="5">
                    </div>
                  </div>
                  <div class="col-md-12">
                    <label class="col-md-4">PINCODE:</label>
                    <div class="col-md-8">
                      <input type="password" class="form-control" id="legcpass" name="legcpass" maxlength="4">
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="col-md-7"></div>
                    <div class="col-md-5">
                      <button type="button" class="btn btn-block btn-primary" id="btnLoginEGC" onclick="verifyEGCAccount()">Submit</button> 
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="egcprodconfirm" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h6 class="modal-title" id="myModalLabel">SUBMIT PRODUCTS</h6>
              </div>
              <div class="modal-body">
                <form role="form" class="form-horizontal" method="post" action="{{URL::to('/transaction/sell/save-egc-prods')}}">
                  <input type="hidden" name="segcbene" id="segcbene">
                  <input type="hidden" name="segccode" id="segccode">
                  <input type="hidden" name="segcprodcode" id="segcprodcode">
                  <input type="hidden" name="segcprodname" id="segcprodname">
                  <input type="hidden" name="segcprodqty" id="segcprodqty">
                  <center>
                    <h3> Purchase the products? </h3>
                  </center>
                  <div class="col-md-12">
                    <div class="col-md-7"></div>
                    <div class="col-md-5">
                      <button type="submit" class="btn btn-block btn-primary" id="btnCofirmProdEGC">Submit</button> 
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="egcamtconfirm" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h6 class="modal-title" id="myModalLabel">EGC AMOUNT SUMMARY</h6>
              </div>
              <div class="modal-body">
                <form role="form" class="form-horizontal" method="post" action="{{URL::to('/transaction/sell/save-egc-amt')}}">
                  
                  <!-- product codes -->
                  <input type="hidden" name="egcprodcode" id="egcprodcode">
                  <!-- piece or package -->
                  <input type="hidden" name="egcpricemode" id="egcpricemode">
                  <!-- product names -->
                  <input type="hidden" name="egcname" id="egcname">
                  <!-- product prices -->
                  <input type="hidden" name="egcprice" id="egcprice">
                  <!-- quantities -->
                  <input type="hidden" name="egcqty" id="egcqty">
                  <input type="hidden" name="egcamtt" id="egcamtt">
                  <input type="hidden" name="egcdiscamt" id="egcdiscamt">

                  <div class="col-md-12">
                    <label class="col-md-4">EGC Code:</label>
                    <div class="col-md-8">
                      <input type="text" class="form-control" id="egcsumcode" name="egcsumcode" readonly>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <label class="col-md-4">Beneficiary:</label>
                    <div class="col-md-8">
                      <input type="text" class="form-control" id="egcbene" name="egcbene" readonly>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <label class="col-md-4">EGC Amount:</label>
                    <div class="col-md-8">
                      <input type="text" class="form-control" id="egcamt" name="egcamt" readonly>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <label class="col-md-4">Purchase Amount:</label>
                    <div class="col-md-8">
                      <input type="text" class="form-control" id="egctotal" name="egctotal" readonly>
                    </div>
                  </div>
                  <div id="egcaddcash" class="col-md-12 hidden">
                    <label class="col-md-4">Additional Cash:</label>
                    <div class="col-md-8">
                      <input type="number" class="form-control" id="egccash" name="egccash" value="0">
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="col-md-7"></div>
                    <div class="col-md-5">
                      <button type="submit" class="btn btn-block btn-danger" id="btnSubmitEGCAmt" onclick="collectEGCItems()">Submit EGC</button> 
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
              </div>
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
  //automatically shows the modal when start up

  //modal closing event
    //$('#myModal').on('hidden.bs.modal', function () {
      // do something…
    //})
</script>
<script>
  function abx(){
    document.getElementById('showreturnamt').className = "input-group";
    document.getElementById('sumreturn').value = document.getElementById('retuid').value;
    disableOtherButtons();
    document.getElementById('btnreturn').innerHTML = "USE RETURNS";
    document.getElementById('btnreturn').setAttribute('onclick','checkReturnAmount()');
    $("#returnsinfo").modal('hide');
  }

  function disableOtherButtons(){
    document.getElementById('btncash').setAttribute('disabled','');
    document.getElementById('btncard').setAttribute('disabled','');
    document.getElementById('btnegc').setAttribute('disabled','');
  }

  function enableOtherButtons(){
    document.getElementById('btncash').removeAttribute('disabled');
    document.getElementById('btncard').removeAttribute('disabled');
    document.getElementById('btnegc').removeAttribute('disabled');
    document.getElementById('btnretuuse').setAttribute('disabled','');
    document.getElementById('btnretupay').setAttribute('disabled','');
    document.getElementById('retuid').value = "";
    document.getElementById('retuamt').innerHTML = "";
  }
</script>
<script>
  $(document).ready(function(){
    document.getElementById('example_length').parentElement.outerHTML = "";
    document.getElementById('example2_length').parentElement.outerHTML = "";
    document.getElementById('example3_length').parentElement.outerHTML = "";


  
    //fill category combo box
    $.ajax({
            url: '/maintenance/ppd/packages/get-search-names',
            type: 'GET',
            data: {
                table: 'tblnmedcategory',
                column: 'strNMedCatName'
            },
            success: function(data){
              var opt = "";

              for(var i = 0; i < data.length; i++){
                  opt = opt +
                    '<option>' +
                      data[i]['strNMedCatName'] +
                    '</option>'
                }

              document.getElementById('nlookfor').innerHTML = '<option disabled selected value>SEARCH</option>' + opt + '<option value="">ALL</option>';
            }, 
            error: function(){
            }
        });
  });

  $('#example').dataTable( {
    "pageLength": 10,
    "columnDefs": [
            {
                "targets": [ 4 ],
                "visible": false
            },
            {
                "targets": [ 5 ],
                "visible": false
            },
            {
                "targets": [ 6 ],
                "visible": false
            },
            {
                "targets": [ 7 ],
                "visible": false
            },
            {
                "targets": [ 8 ],
                "visible": false
            }
        ]
  } );

  $('#example2').dataTable( {
    "pageLength": 10,
    "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false
            },
            {
                "targets": [ 5 ],
                "visible": false
            },
            {
                "targets": [ 6 ],
                "visible": false
            }
        ]
  } );

  $('#example3').dataTable( {
    "pageLength": 10,
    "columnDefs": [
            {
                "targets": [ 3 ],
                "visible": false
            },
            {
                "targets": [ 4 ],
                "visible": false
            }
        ]
  } );

  $("#mfilterby").on('change', function(){
    var sttbl;
    var stcol;

    if(document.getElementById('mfilterby').value == 0){
      sttbl = 'tblpmtheraclass';
      stcol = 'strPMTheraClassName';
    }else if(document.getElementById('mfilterby').value == 1){
      sttbl = 'tblpmmanufacturer';
      stcol = 'strPMManuName';
    }else if(document.getElementById('mfilterby').value == 2){
      sttbl = 'tblpmform';
      stcol = 'strPMFormName';
    }else{
      sttbl = 'tblpmpackaging';
      stcol = 'strPMPackName';
    }

    $.ajax({
            url: '/maintenance/ppd/packages/get-search-names',
            type: 'GET',
            data: {
                table: sttbl,
                column: stcol
            },
            success: function(data){
              var opt = "";

              for(var i = 0; i < data.length; i++){
                  opt = opt +
                    '<option>' +
                      data[i][stcol] +
                    '</option>'
                }

              document.getElementById('mlookfor').innerHTML = '<option disabled selected value>SEARCH</option>' + opt + '<option value="">ALL</option>';
            }, 
            error: function(){
            }
        });
  });

  $("#mlookfor").on('change', function(){
    var col;
    var cont = document.getElementById('mfilterby').value;

    if(cont == "0"){
      col = 4;
    }else if(cont == "1"){
      col = 5;
    }else if(cont == "2"){
      col = 6;
    }else if(cont == "3"){
      col = 7;
    }

    filterColumn(col, document.getElementById('mlookfor').value, "#example");
  })

  $("#nlookfor").on('change', function(){
    filterColumn(5, document.getElementById('nlookfor').value, "#example2");
  })

  document.getElementById('example').style = "width:100%";
  
  function filterColumn (i,value, table) {
    $(table).DataTable().column( i ).search(
        value,
        true,
        true
      ).draw();
  }
</script>
<script>
  function reloadMember(){

    if((parseFloat(document.getElementById('rload').value) >= parseFloat('{{$ldmin}}'))){
      var reload = parseFloat(document.getElementById('rload').value);
      var total = parseFloat(document.getElementById('membal').value) + parseFloat(document.getElementById('rload').value);
      total = parseFloat(total).toFixed(2);

      $.ajax({
          url: "{{URL::to('/transaction/sell/reload-mem')}}",
          type: 'POST',
          data: {
              load: reload,
              memcode: $('#edocmem').val()
          },
          success: function(data){
            document.getElementById('membal').value = total;
            document.getElementById('rload').value = "";

            if(parseFloat(total) >= parseFloat(document.getElementById('subt').value)){
              document.getElementById('loadaddcash').className = 'input-group hidden';
              document.getElementById('memaddcash').value = '0';
              document.getElementById('membal').setAttribute('readonly','');
            }else{
              document.getElementById('loadaddcash').className = 'input-group';
              document.getElementById('memaddcash').value = '0';
              document.getElementById('membal').removeAttribute('readonly');
            }
          }, 
          error: function(xhr){
            alert("error");
          }
      });
    }else{
      alert('INPUT NOT WITHIN ALLOWED INPUT AMOUNT');
      document.getElementById('rload').value = "";
    }
  }
</script>
<script>
  function showMed(){
    document.getElementById('medtbl').className="collapse in";
    document.getElementById('nmedtbl').className="collapse";
    document.getElementById('medmenu').className="btn btn-info";
    document.getElementById('nmedmenu').className="btn btn-primary";
  }
  function showNonMed(){
    document.getElementById('medtbl').className="collapse";
    document.getElementById('nmedtbl').className="collapse in";
    document.getElementById('nmedmenu').className="btn btn-info";
    document.getElementById('medmenu').className="btn btn-primary";
  }
</script>
<script>
  function setModalForm(pcode, pname, pcprice, pkprice, mode, stock){
    document.getElementById('pcode').value= pcode;
    document.getElementById('pname').value= pname;
    document.getElementById('pcprice').value= pcprice;
    document.getElementById('pkprice').value= pkprice;
    document.getElementById('finalprice').value= pcprice;
    document.getElementById('pstock').value= stock;

    if(mode == 0){
      document.getElementById('showpcpk').className = "";
    }else{
      document.getElementById('showpcpk').className = "hidden";
    }
  }

  function addItem(){

    var code = document.getElementById('pcode').value;
    var name = document.getElementById('pname').value;
    var quantity = parseInt(document.getElementById('pqty').value);
    var price = document.getElementById('finalprice').value;
    var stock = parseInt(document.getElementById('pstock').value);

    if(document.getElementById('modeprice').value == "0"){
      var modeprice = "PC";
    }else{
      var modeprice = "PK";
    }

    if(quantity > stock){
      alert('Quantity set is greater than on stock')
    }else{
      if(parseFloat(quantity)%1 === 0 && quantity != 0){
        var isexisting = isItemAdded(code, modeprice);

        if(isexisting == 0){
          var $table = $("#purchased");

          var newTR = $("<tr>" + 
                        "<td><button class=\"delete\" onclick=\"deductAmt("+(quantity * price)+")\">x</button></td>" + 
                        "<td>"
                          + code + 
                        "</td>" + 
                        "<td>" 
                          + name +
                        "</td>" + 
                        "<td>" 
                          + price +
                        "</td>" + 
                        "<td>(" 
                          + modeprice +
                        ")</td>" + 
                        "<td>" 
                          + quantity +
                        "</td>" + 
                        "<td>" 
                          + (quantity * price) +
                        "</td>"
                        + "</tr>");

            $table.append(newTR);
          }else{
            tempqty = quantity;
            quantity = parseInt(document.getElementById('purchased').getElementsByTagName('TR')[isexisting].getElementsByTagName('TD')[5].innerHTML) + parseInt(quantity);
            document.getElementById('purchased').getElementsByTagName('TR')[isexisting].getElementsByTagName('TD')[0].innerHTML = "<button class=\"delete\" onclick=\"deductAmt("+(quantity * price)+")\">x</button>";
            document.getElementById('purchased').getElementsByTagName('TR')[isexisting].getElementsByTagName('TD')[5].innerHTML = quantity.toString();
            document.getElementById('purchased').getElementsByTagName('TR')[isexisting].getElementsByTagName('TD')[6].innerHTML = (quantity * parseFloat(price)).toString();
            quantity = tempqty;
          }

        var subt = parseFloat(document.getElementById('subt').value) + (quantity * price);
        var disc = parseFloat(document.getElementById('discamt').value);

        document.getElementById('rawtotal').value = parseFloat(document.getElementById('rawtotal').value) + (quantity * price);

        //var temp = subt + (subt * 0.12);

        //document.getElementById('grandt').value = (temp - (temp * (disc/100))).toFixed(2);

        document.getElementById('subt').value = parseFloat(document.getElementById('subt').value) + (quantity * price);
        previous_total = document.getElementById('subt').value;
      }else{
        alert('please input a valid integer');
      }
    }
    document.getElementById('pqty').value = "";
    document.getElementById('qtypc').checked = true;
    document.getElementById('qtypk').checked = false;
    document.getElementById('modeprice').value="0";
  }

  function collectItems(){
    try{
      document.getElementById('prodcode').value = "";
      document.getElementById('prodname').value = "";
      document.getElementById('prodprice').value = "";
      document.getElementById('prodqty').value = "";
      document.getElementById('prodamt').value = "";
      document.getElementById('prodpricemode').value = "";
      for(var i = 1; i < document.getElementById('purchased').rows.length; i++){
        document.getElementById('prodcode').value = document.getElementById('prodcode').value + document.getElementById('purchased').getElementsByTagName('TR')[i].getElementsByTagName('TD')[1].innerHTML + ";";
        document.getElementById('prodname').value = document.getElementById('prodname').value + document.getElementById('purchased').getElementsByTagName('TR')[i].getElementsByTagName('TD')[2].innerHTML + ";";
        document.getElementById('prodprice').value = document.getElementById('prodprice').value + document.getElementById('purchased').getElementsByTagName('TR')[i].getElementsByTagName('TD')[3].innerHTML + ";";
        document.getElementById('prodqty').value = document.getElementById('prodqty').value + document.getElementById('purchased').getElementsByTagName('TR')[i].getElementsByTagName('TD')[5].innerHTML + ";";
        document.getElementById('prodamt').value = document.getElementById('prodamt').value + document.getElementById('purchased').getElementsByTagName('TR')[i].getElementsByTagName('TD')[6].innerHTML + ";";

        if(document.getElementById('purchased').getElementsByTagName('TR')[i].getElementsByTagName('TD')[4].innerHTML == "(PC)"){
          document.getElementById('prodpricemode').value = document.getElementById('prodpricemode').value + "0" + ";";
        }else{
          document.getElementById('prodpricemode').value = document.getElementById('prodpricemode').value + "1" + ";";
        }
      }
    }catch(ee){}
  }

  function setFinalPrice(mode){
    if(mode == "0"){
      document.getElementById('finalprice').value = document.getElementById('pcprice').value;
      document.getElementById('modeprice').value = "0";
    }else{
      document.getElementById('finalprice').value = document.getElementById('pkprice').value;
      document.getElementById('modeprice').value = "1";
    }
  }
</script>
<script>
  function setDiscount(){
    if(document.getElementById('discchk').checked == true){
      previous_total = document.getElementById('subt').value;
      document.getElementById('disc').removeAttribute("disabled");
    }else{
      document.getElementById('disc').value = "0";
      document.getElementById('disc').setAttribute("disabled","");
      document.getElementById('discamt').value="0";
      //addDiscount();
      document.getElementById('subt').value = document.getElementById('rawtotal').value; 
      document.getElementById('proddiscamt').value="0";
      document.getElementById('proddisc').value="";
    }
  }

  function addDiscount(){
    var previous_total = document.getElementById('rawtotal').value;

    if(parseFloat(previous_total) > 0){
      if(parseFloat(document.getElementById('disc').value) < 1){
        var percent = ((parseFloat(document.getElementById('disc').value) * 100)).toString();
        var strdisc =  "Senior Citizen (" + percent + "%)"; 
        if(document.getElementById('disc').options[document.getElementById('disc').selectedIndex].innerHTML != strdisc){
          document.getElementById('discamt').value = (parseFloat(document.getElementById('disc').value) * 100).toString() + "%";

          if(document.getElementById('disc').selectedIndex > 0){
            document.getElementById('proddisc').value = document.getElementById('disc').options[document.getElementById('disc').selectedIndex].innerHTML;
          }else{
           document.getElementById('proddisc').value = ""; 
          }

          document.getElementById('subt').value = (previous_total -(previous_total * parseFloat(document.getElementById('disc').value))).toFixed(2) ;
        }else{
          document.getElementById('discamt').value = (parseFloat(document.getElementById('disc').value) * 100).toString() + "%";
          
          if(document.getElementById('disc').selectedIndex > 0){
            document.getElementById('proddisc').value = document.getElementById('disc').options[document.getElementById('disc').selectedIndex].innerHTML;
          }else{
           document.getElementById('proddisc').value = ""; 
          }
            var previous_total = (document.getElementById('rawtotal').value);
            var subt = parseFloat(previous_total);
            var disc = parseFloat(document.getElementById('disc').value);

            var temp = subt - (subt * 0.12);
            //temp is now vatless
            temp = temp - (temp * disc);
            //temp is now discounted

           document.getElementById('subt').value = (parseFloat(temp).toFixed(2)).toString() ;
        }
        document.getElementById('proddiscamt').value = document.getElementById('disc').value;
      }else{
        document.getElementById('discamt').value = "Php " + document.getElementById('disc').value ;

        if(document.getElementById('disc').selectedIndex > 0){
          document.getElementById('proddisc').value = document.getElementById('disc').options[document.getElementById('disc').selectedIndex].innerHTML;
        }else{
          document.getElementById('proddisc').value = ""; 
        }

        var subt = parseFloat(previous_total);
        var disc = parseFloat(document.getElementById('disc').value);

        if(subt - disc <= 0){
          document.getElementById('subt').value = previous_total;
          document.getElementById('disc').value = "0";
          document.getElementById('discchk').checked = false;
          document.getElementById('disc').setAttribute("disabled","");
          document.getElementById('discamt').value="0";
          document.getElementById('proddiscamt').value = "0";
        }else{
          document.getElementById('subt').value = (subt-disc).toFixed(2);
          document.getElementById('proddiscamt').value = document.getElementById('disc').value;
        }
      }
    }else{  
      document.getElementById('disc').value = "0";
      document.getElementById('discchk').checked = false;
      document.getElementById('disc').setAttribute("disabled","");
      document.getElementById('discamt').value="0";
      document.getElementById('proddiscamt').value = "0";
    }
  }
</script>
<script>
  $("#purchased").on('click', '.delete', function () {
      
      $(this).closest('tr').remove();

  });
</script>
<script>
  function deductAmt(amt){
    document.getElementById('subt').value = parseFloat(document.getElementById('rawtotal').value) - amt;
    document.getElementById('rawtotal').value = parseFloat(document.getElementById('rawtotal').value) - amt;

    document.getElementById('discchk').checked = false;
    document.getElementById('disc').value = 0;
    document.getElementById('discamt').value = "0";
  }
</script>
<script>
  function checkGrandTotal(){
    if(parseFloat(document.getElementById('subt').value) > 0){
      $('#cardid').modal('show');
    }else{
      alert('Please choose products first');
    }
  }
   function checkPinCode(){
    if(document.getElementById('nipcode').value == document.getElementById('edocnip').value){
      getMemDetails();
      $('#carddet').modal('show');
    }else{
      alert('WRONG PASSWORD');
    }
   }
  function checkMemAmount(){
    if(parseFloat(document.getElementById('subt').value) 
        <= parseFloat(document.getElementById('membal').value)){

      document.getElementById('btnreceipt').removeAttribute("disabled");
    }else{
      document.getElementById('btnreceipt').setAttribute("disabled",""  );
    }
  }

  function checkCashAmount(){
    if( (parseFloat(document.getElementById('subt').value) 
        <= parseFloat(document.getElementById('amtpaid').value))
        && parseFloat(document.getElementById('subt').value) > 0){
        $('#transum').modal('show');

        //---

        document.getElementById('sumsubt').value  = document.getElementById('subt').value;
        document.getElementById('prodrawtotal').value = document.getElementById('rawtotal').value;
        document.getElementById('sumdisc').value  = document.getElementById('discamt').value;
        document.getElementById('sumtax').value   = "12%";
        //document.getElementById('sumgrant').value = (parseFloat(document.getElementById('grandt').value).toFixed(2)).toString();
        document.getElementById('sumamt').value   = document.getElementById('amtpaid').value;
        document.getElementById('sumchan').value  = ((parseFloat(document.getElementById('amtpaid').value) - parseFloat(document.getElementById('subt').value)).toFixed(2)).toString();
        collectItems();
    }else{
      alert('Payment cannot be processed');
    }

    document.getElementById('amtpaid').value = "";
  }

  function checkBalance(){
    var membal = parseFloat(document.getElementById('membal').value);
    var memaddcash = parseFloat(document.getElementById('memaddcash').value);

    if( (parseFloat(document.getElementById('subt').value) 
        <= (membal + memaddcash))
        && parseFloat(document.getElementById('subt').value) > 0){
        
        $('#transum').modal('show');

        //---

        document.getElementById('sumsubt').value  = (parseFloat(document.getElementById('subt').value).toFixed(2)).toString();
        document.getElementById('sumdisc').value  = (parseInt(parseFloat(document.getElementById('disc').value) * 100)).toString();
        document.getElementById('sumtax').value   = "12%";
        //document.getElementById('sumgrant').value = (parseFloat(document.getElementById('grandt').value).toFixed(2)).toString();
        
        document.getElementById('prodrawtotal').value = document.getElementById('rawtotal').value;
        document.getElementById('prodmemname').value = document.getElementById('memname').value;      
        document.getElementById('prodmemcode').value = document.getElementById('edocmem').value;


        document.getElementById('prodbal').value = (parseFloat(parseFloat(document.getElementById('membal').value) - parseFloat(document.getElementById('subt').value)).toFixed(2)).toString();

        var subt = parseFloat(document.getElementById('subt').value);
        var usedbal = parseFloat(document.getElementById('membal').value);
        var addcash = parseFloat(document.getElementById('memaddcash').value);

        if(usedbal >= subt){
          document.getElementById('prodbal').value = (parseFloat(document.getElementById('subt').value).toFixed(2)).toString();
          var sumamt = subt;
          var change = 0;
        }else{
          document.getElementById('prodbal').value = (parseFloat(document.getElementById('membal').value).toFixed(2)).toString();
          var sumamt = parseFloat(usedbal + addcash);
          var change = addcash - (subt - usedbal);
        }

        document.getElementById('addcash').value = parseFloat(addcash);
        document.getElementById('sumamt').value   = (parseFloat(sumamt).toFixed(2)).toString();
        document.getElementById('sumchan').value  = (parseFloat(change).toFixed(2)).toString();

        if(
            ( parseFloat(document.getElementById('sumsubt').value) >= parseFloat('{{$ptmin}}') )
          ){
            var perc = parseFloat(parseFloat('{{$ptperc}}') / 100).toFixed(2);
            document.getElementById('prodpts').value = (parseFloat(parseFloat(document.getElementById('sumsubt').value)*perc).toFixed(2)).toString();
        }else{
          document.getElementById('prodpts').value = "0"; 
        }
        setFormSubmit('card');
        collectItems();
    }else{
      alert('Payment cannot be processed');
    }
  }
  function isItemAdded(term, mode){
    //returns 0 if not return row if existing
    var itemrow = 0                         // make search more flexible 
    var table = document.getElementById('purchased');
    var tr = table.getElementsByTagName('TR');
    mode = "(" + mode + ")";
    var breaker;

    for(var i = 0; i < tr.length; i++){
      var td = tr[i].getElementsByTagName('TD');
      breaker = 0;

      for(var j = 0; j < td.length; j++){
        if(j == 1 && td[j].innerHTML == term){
            breaker++;
        }
        if(j == 4 && td[j].innerHTML == mode){
            breaker++;
        }
      }

      if(breaker == 2){
        itemrow = i;
        break;
      }    
    }
    return itemrow;
  }
</script>
<script>
  function getMemDetails(){
    var membal = 0;
    $.ajax({
        url: '/transaction/sell/get-mem-details',
        type: 'GET',
        data: {
            memcode: $('#edocmem').val()
        },
        success: function(data){
            $('#memname').val(data[0]['strMemFName'] + " " + data[0]['strMemMName'] + " " + data[0]['strMemLName']);
            $('#membal').val(data[0]['decMCreditValue']);
            $('#memcurbal').val(data[0]['decMCreditValue']);
            document.getElementById('membal').setAttribute('max',(data[0]['decMCreditValue']).toString());
            membal = data[0]['decMCreditValue'];
        }, 

        error: function(xhr){
          $('#memname').val("");
          $('#membal').val("");
          $('#memcurbal').val("");
          membal = 0;
        }
    });

    showLoadAddCash();
  }
</script>
<script>
  function showLoadAddCash(){
    if(parseFloat(document.getElementById('membal').value) < parseFloat(document.getElementById('subt').value)){
      document.getElementById('loadaddcash').className = 'input-group';
    }else{
      document.getElementById('loadaddcash').className = 'input-group hidden';
      document.getElementById('memaddcash').value = '0';
    }
  }
</script>
<script>
  function checkReturnAmount(){
    if(parseFloat(document.getElementById('sumreturn').value) > parseFloat(document.getElementById('subt').value)){
      document.getElementById('retaddcash').className = "col-md-12 hidden";
      alert('TOTAL AMOUNT SHOULD BE GREATER THAN OR EQUAL TO RETURNS AMOUNT');
    }else{
      if(parseFloat(document.getElementById('sumreturn').value) < parseFloat(document.getElementById('subt').value)){
        var additional = parseFloat(document.getElementById('rettotal').value) - parseFloat(document.getElementById('retamt').value);
        document.getElementById('retcash').value = (parseFloat(additional).toFixed(2)).toString();
        document.getElementById('retaddcash').className = "col-md-12";
      }

      var retuid = document.getElementById('retuid');
      document.getElementById('retsumcode').value = retuid.options[retuid.selectedIndex].text;
      document.getElementById('retamt').value  = ((parseFloat(document.getElementById('sumreturn').value)).toFixed(2)).toString();
      document.getElementById('rettotal').value = ((parseFloat(document.getElementById('subt').value)).toFixed(2)).toString()

      $("#returnssum").modal("show");
    }
  }

  function showReturnsModal(){
    $("#returnsinfo").modal('show');
  }

  function collectReturnItems(){
    collectItems();
    document.getElementById('retprodcode').value = document.getElementById('prodcode').value;
    document.getElementById('retpricemode').value = document.getElementById('prodpricemode').value;
    document.getElementById('retname').value = document.getElementById('prodname').value;
    document.getElementById('retprice').value = document.getElementById('prodprice').value;
    document.getElementById('retqty').value = document.getElementById('prodqty').value;
    document.getElementById('retamtt').value = document.getElementById('prodamt').value;
  }
</script>
<script>
  function setFormSubmit(module){
    if(module == "cash"){
      document.getElementById('transdet').action = "{{URL::to('/transaction/sell/get-cash-receipt')}}";
    }else if(module == "card"){
      document.getElementById('transdet').action = "{{URL::to('/transaction/sell/get-card-receipt')}}";
    }else{
      alert('yehey');
    }
  }
</script>
<script>
  function verifyEGCAccount(){
    $.ajax({
        url: '/transaction/sell/verify-egc',
        type: 'GET',
        data: {
            egcid: $('#legcid').val(),
            egcpin: $('#legcpass').val()
        },
        success: function(data){
            if(data.length > 0){ 
              $('#egclogin').modal('hide');
              var ecode = data[0]['strEGCCode'];

              if(data[0]['intEGCType'] == 1){
                document.getElementById('medmenu').className = 'btn btn-info hidden';
                document.getElementById('nmedmenu').className = 'btn btn-primary hidden';
                document.getElementById('egcprods').className = 'btn btn-info';
                document.getElementById('medtbl').className = 'collapse in hidden';
                document.getElementById('nmedtbl').className = 'collapse hidden';
                document.getElementById('prodegctbl').className = 'collapse in'; 
                document.getElementById('fgdisc').className = 'form-group hidden';  
                document.getElementById('fgsumm').className = 'form-group hidden'; 

                //populate mo bakla
                populateEGCProdTable(ecode);
                document.getElementById('btnegc').setAttribute('onclick','collectEGCProds()');
              }else{
                setEGCAmount(ecode);
                document.getElementById('btnegc').setAttribute('onclick','showEGCCashSumm()');
              }


              document.getElementById('btncash').setAttribute('disabled','');
              document.getElementById('btncard').setAttribute('disabled','');
              document.getElementById('btnreturn').setAttribute('disabled','');
              document.getElementById('btnegc').innerHTML = "USE EGC";
              document.getElementById('btnegc').removeAttribute('data-target');
            }else{
              alert('ERROR FETCHING EGC ACCOUNT!');
            }
        }, 

        error: function(xhr){
          alert('BACKEND ERROR FETCHING EGC ACCOUNT!');
        }
    });
  }

  function populateEGCProdTable(code){
    $.ajax({
            url: '/transaction/sell/get-egc-prods',
            type: 'GET',
            data: {
                prodegcid: code
            },
            success: function(data){
              var i; 
              var status;
              var name;
              var bran;

              var t = $('#example3').DataTable();

              for(i = 0; i < data.length; i++){
                status="OK";

                if(
                    (data[i]['Stock'] <= 0) ||
                    (data[i]['Stock'] == null) ||
                    (data[i]['Quantity'] == 0) ||
                    (data[i]['Stock'] < data[i]['Quantity'])
                  ){ status = "NOTOK" }


                if(parseInt(data[i]['Type']) == 0){
                  bran = data[i]['Brand'];

                  if(bran == null){
                    bran = "";
                  }

                  name = "<b>" + bran + "</b> " + data[i]['Generic'] + " " + data[i]['MedSize'];
                }else{
                  name = data[i]['NMedName'] + " " + data[i]['NMedSize'];
                }

                var button = "";
                if(status == "OK"){
                  button = "<button class='btn btn-success btn-block' data-toggle='modal' data-target='#quantity'" +
                                  "onClick=\"setModalForm('" +
                                    data[i]['Code'] + "','" +
                                    name + "','" +
                                    data[i]['PricePerPiece'] + "','" +
                                    "0','" +
                                    "1','" +
                                   data[i]['Stock'] + "')\">+</button>";
                }else{
                  button = "<button class='btn btn-danger btn-block' disabled>UNAVAILABLE</button>";
                }

                t.row.add(
                    [
                      name,
                      data[i]['Quantity'],
                      button,
                      data[i]['Code'],
                      data[i]['Stock']
                    ]
                  ).draw(false);
              }

              document.getElementById('segcbene').value = data[0]['Beneficiary'];
              document.getElementById('segccode').value = code;
            },
            error: function(xhr){
              alert('ERROR RETRIEVING PRODUCTS!');
            }
          });
  }

  function collectEGCProds(){
    if(parseInt(document.getElementById('myTableID').rows.length) > 3){
      collectItems();
      document.getElementById('segcprodcode').value = document.getElementById('prodcode').value;
      document.getElementById('segcprodqty').value = document.getElementById('prodqty').value;
      document.getElementById('segcprodname').value = document.getElementById('prodname').value;
      $("#egcprodconfirm").modal("show");
    }else{
      alert('PLEASE SELECT PRODUCTS!');
    }
  }

  function setEGCAmount(code){
    $.ajax({
        url: '/transaction/sell/get-egc-amt',
        type: 'GET',
        data: {
            amtegccode: code
        },
        success: function(data){
            document.getElementById('sumegc').value = data[0]['decEBBalance'];
            document.getElementById('egcamt').value = data[0]['decEBBalance'];
            document.getElementById('egcsumcode').value = code;
            document.getElementById('egcbene').value = data[0]['strEGCBeneficiary'];
            document.getElementById('showegcamount').className = "input-group";
            document.getElementById('btnegc').setAttribute('onclick','setEGCSumm()');
        }, 

        error: function(xhr){
          alert('ERROR FETCHING EGC AMOUNT!');
        }
    });
  }

  function setEGCSumm(){
    if(parseFloat(document.getElementById('subt').value) > 0){
    collectItems();
    document.getElementById('egcprodcode').value = document.getElementById('prodcode').value;
    document.getElementById('egcpricemode').value = document.getElementById('prodpricemode').value;
    document.getElementById('egcname').value = document.getElementById('prodname').value;
    document.getElementById('egcprice').value = document.getElementById('prodprice').value;
    document.getElementById('egcqty').value = document.getElementById('prodqty').value;
    document.getElementById('egcamtt').value = document.getElementById('prodamt').value;
    document.getElementById('egctotal').value = document.getElementById('subt').value;

    if(parseFloat( document.getElementById('egctotal').value) > parseFloat(document.getElementById('egcamt').value)){
      document.getElementById('egcaddcash').className = "col-md-12";
      document.getElementById('egccash').value = parseFloat(document.getElementById('egctotal').value) - parseFloat(document.getElementById('egcamt').value);
    }

    $("#egcamtconfirm").modal("show");
  }else{alert('PLEASE SELECT PRODUCTS');}
  }
</script>
@stop 