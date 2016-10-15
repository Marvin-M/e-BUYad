@extends('....layout')

@section('page-title')
    Accept A Return
@stop

@section('other-scripts')
{{HTML::script('bootflat-admin/js/datatables.min.js')}}
{{HTML::script('js/select2/js/select2.min.js')}}
{{HTML::style('js/select2/css/select2.min.css')}}
{{HTML::script('js/icheck/icheck.min.js')}}
{{HTML::style('js/icheck/flat/blue.css')}}

<script type="text/javascript" charset="utf-8">
  $(document).ready(function() {
    $("#transdate").select2({
      placeholder: "Select a date"
    });
    $("#translistid").select2();
    $("input").iCheck({
      checkboxClass: 'icheckbox_flat'
    });
  } );
</script>
@stop

@section('content')
<div class="panel-body">
  <div class="content-row">
    <center><h5 class="content-row-title" style="font-size:25px"><i class="glyphicon glyphicon-shopping-cart"></i>&nbsp Accept Return
      <hr>
      </h5></center>

    <!-- OR# and Customer --> 
    <div class="panel panel-default col-md-12">
      <form role="form" class="form-horizontal">
      </form>
        <div class="col-md-6">
          <label class="control-label">Date:</label>
          <select id="transdate" onchange="setTransaction()" name="transdate" class="form-control" >
              <option selected value="0"> ALL TRANSACTIONS </option>
              <?php
                $results = DB::select('SELECT DISTINCT DATE_FORMAT(t.dtmTransDate, \'%Y-%m-%d\')
                                        as \'Dates\'
                                        FROM tblTransaction t
                                        WHERE t.strTransBranCode = ? ORDER BY t.dtmTransDate DESC',
                                        [
                                          Cache::get('branch')
                                        ]);

                foreach($results as $data){
                  echo '<option value="'.$data->Dates.'">'.$data->Dates.'</option>';
                }
              ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="control-label">Transaction ID:</label>
          <select id="translistid" onchange="setInfo()" name="translistid" class="form-control">
              <option selected disabled> -- SELECT TRANSACTION -- </option>
              <?php
                $results = DB::select('SELECT t.strTransId
                                      FROM tblTransaction t
                                      WHERE 
                                        t.strTransBranCode = ? 
                                      ORDER BY t.dtmTransDate DESC',
                                        [
                                          Cache::get('branch')
                                        ]);

                foreach($results as $data){
                  echo '<option value="'.$data->strTransId.'">'.$data->strTransId.'</option>';
                }
              ?>
          </select>
        </div>
    </div>
    <!-- END -->

    <!-- Product List and Chosen Products List -->
    <div class="panel panel-default col-md-12">
      <div class="panel-body col-md-5">
        <div class="panel panel-header">
          <center>
            <h5>Bought Products</h5>
          </center>
        </div>
        <div class="panel-body" style="height:500px;">
          <input type="hidden" id="code">
          <input type="hidden" id="name">
          <input type="hidden" id="qty">
          <input type="hidden" id="pricetype">
          <input type="hidden" id="price">
          <input type="hidden" id="row">
            <table id="bought" class="table table-condensed table-hover">
                <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Qty</th>
                    <th></th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Return</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
      </div>

      <div class="modal fade" id="modalqty" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h6 class="modal-title" id="myModalLabel">Quantity</h6>
              </div>
              <div class="modal-body">
                <form role="form" class="form-horizontal">
                  <div class="col-md-12">
                    <label class="col-md-3">Pieces to be returned:</label> 
                    <div class="col-md-9">
                    <input class="form-control" type="number" id="pcqty" name="pcqty" min="0.00" step="0.01" pattern="/^[0-9]+(\.[0-9]{1,2})?$/">
                    </div>
                  </div>
                  <div class="col-md-12">
                    <label class="col-md-3">Condition:</label> 
                    <div class="col-md-9">
                      <input type="checkbox" id="prodcond" name="prodcond" checked>
                      <label for="prodcond"> &nbsp In GOOD condition</label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <label class="col-md-3">Reason:</label> 
                    <div class="col-md-9">
                    <input class="form-control" type="text" id="reason" name="reason" maxlength="200">
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-danger" onclick="checkQuantity()">Confirm</button>
              </div>
            </div>
          </div>
        </div>

      <!-- second half-->
      <div class="panel-body col-md-7">
        <div class="panel panel-header">
          <center>
            <h5>Returned Products</h5>
          </center>
        </div>
        <div class="panel-body" style="height:500px;">
            <table id="returned" class="table table-condensed table-hover">
              <thead>
                <tr>
                  <th>Remove</th>
                  <th>Code</th>
                  <th>Name</th>
                  <th>Qty</th>
                  <th></th>
                  <th>Price</th>
                  <th>Total</th>
                  <th>Condition</th>
                  <th>Reason</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
        </div>
      </div>
    </div>
    <!-- END -->
      <form role="form" method="post" action="{{URL::to('/transaction/return/save')}}">
        <div class="col-md-6">
         <div class="form-group">
          <input type="hidden" id="retcode" name="retcode">
          <input type="hidden" id="retqty" name="retqty">
          <input type="hidden" id="retpricetype" name="retpricetype">
          <input type="hidden" id="retcondition" name="retcondition">
          <input type="hidden" id="retreason" name="retreason">

          <div class="input-group" style="width: 800px;">
              <input type="hidden" name="retid" value="{{$retid}}">
              <div class="form-group">
              <label class="control-label col-md-2" style="padding-right:0px">
                Transaction ID:
              </label>
              <div class="col-md-5">
                <input id="transid" type="text"  name="transid" class="form-control" readonly>
              </div>
              </div>
            </div>
            <div class="input-group" style="width: 800px;">
              <label class="control-label col-md-2">
                Date:
              </label>
              <div class="col-md-5">
                <input id="date" name="date" type="text"  class="form-control" readonly>
              </div>
            </div>
            <div class="input-group" style="width: 800px;">
              <label class="control-label col-md-2">
                Pharmacist:
              </label>
              <div class="col-md-5">
                <input id="pharmacist" type="text" name="pharmacist" class="form-control" readonly>
              </div>
            </div>
            <div class="input-group" style="width: 800px;">
              <label class="control-label col-md-2">
                Customer:
              </label>
              <div class="col-md-5">
                <input id="cust" type="text" name="cust" class="form-control" required readonly>
              </div>
            </div>
         </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <div class="col-md-12">
              <center>
               <label class="control-label col-md-4">
                <h5>TOTAL:</h5>
                </label>
                <div class="col-md-8">
                  <input id="totalreturns" type="text"  class="form-control" name="totalreturns" required readonly>
                </div>
              </center>
            </div>
            <div class="col-md-12">
              <center>
              <button type="submit" id="btncash" onclick="collectItems()" disabled class="btn btn-danger col-md-12" >GENERATE RETURN SLIP</button>
              </center>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

@stop

@section('internal-scripts')
<script>
  function addItem(){

    var code = document.getElementById('code').value;
    var name = document.getElementById('name').value;
    var quantity = parseInt(document.getElementById('pcqty').value);
    var price = parseFloat(document.getElementById('price').value);
    var pricetype = document.getElementById('pricetype').value;
    var total = parseFloat(quantity * price).toFixed(2);

    if(parseFloat(quantity)%1 === 0 && quantity != 0){
      var isexisting = isItemAdded(code, pricetype);


      if(document.getElementById('prodcond').checked){
        var condition = "GOOD";
      }else{
        var condition = "DEFECTIVE";
      }

      var reason = document.getElementById('reason').value;
        
      if(isexisting == 0){
        var $table = $("#returned");

        var newTR = $("<tr>" + 
                    "<td><button class=\"delete\" onclick=\"deductAmt("+(total)+")\">x</button></td>" + 
                    "<td>"
                      + code + 
                    "</td>" +  
                    "<td>"
                      + name + 
                    "</td>" +  
                    "<td>" 
                      + quantity +
                    "</td>" + 
                    "<td>" 
                      + pricetype +
                    "</td>" + 
                    "<td>" 
                      + price +
                    "</td>" + 
                    "<td>" 
                      + total +
                    "</td>" + 
                    "<td>" 
                      + condition +
                    "</td>" + 
                    "<td>" 
                      + reason +
                    "</td>"
                    + "</tr>");

        $table.append(newTR);
      }else{
        tempqty = quantity;
        quantity = parseInt(document.getElementById('returned').getElementsByTagName('TR')[isexisting].getElementsByTagName('TD')[3].innerHTML) + parseInt(quantity);
        document.getElementById('returned').getElementsByTagName('TR')[isexisting].getElementsByTagName('TD')[0].innerHTML = "<button class=\"delete\" onclick=\"deductAmt("+(quantity * price)+")\">x</button>";
        document.getElementById('returned').getElementsByTagName('TR')[isexisting].getElementsByTagName('TD')[4].innerHTML = quantity.toString();
        document.getElementById('returned').getElementsByTagName('TR')[isexisting].getElementsByTagName('TD')[5].innerHTML = (quantity * parseFloat(price)).toString();
        quantity = tempqty;
      }

      setTotal();
      document.getElementById('btncash').removeAttribute('disabled');
    }else{
      alert('please input a valid integer');
    }

    document.getElementById('qty').value = "";
  }
</script>
<script>
</script>
<script type="text/javascript">
  $("#returned").on('click', '.delete', function () {   
      var index = $(this).closest('tr').index(); 
      $(this).closest('tr').remove();
      document.getElementById('returned2').deleteRow(index);
  });
</script>
<script>
  function deductAmt(amt){
    document.getElementById('totalreturns').value = parseFloat(parseFloat(document.getElementById('totalreturns').value) - amt).toFixed(2);
    if(parseFloat(document.getElementById('totalreturns').value) == 0){ 
      document.getElementById('btncash').setAttribute('disabled','');
    }
  }

  function isItemAdded(term, ptype){
    //returns 0 if not return row if existing
    var itemrow = 0
    var columnName = 1;      
    var columnPrice = 4;                      // which column to search
    var pattern = new RegExp(term, 'g');       // make search more flexible 
    var table = document.getElementById('returned');
    var tr = table.getElementsByTagName('TR');
    var isNameMatch, isPriceMatch;
    for(var i = 0; i < tr.length; i++){
      var td = tr[i].getElementsByTagName('TD');
      isNameMatch = 0;
      isPriceMatch = 0;
      for(var j = 0; j < td.length; j++){
        if(j == columnName && td[j].innerHTML == term){
          isNameMatch = 1;
          // itemrow = i;
          // break;
        }

        if(j == columnPrice && td[j].innerHTML == ptype){
          isPriceMatch = 1;
          // itemrow = i;
          // break;
        }

        if(isPriceMatch == 1 && isNameMatch == 1){
          itemrow = i;
          break;
        }
      }    
    }

    return itemrow;
  }
  function checkQuantity(){
    if(parseInt(document.getElementById('pcqty').value) <= parseInt(document.getElementById('qty').value)){
      addItem();
    }else{
      alert('INVALID QUANTITY');
    }
  }
</script>
<script>
  function setInfo(){
    $('#bought tr:gt(0)').remove();
    $('#returned tr:gt(0)').remove();
    document.getElementById('totalreturns').value = "";

    $.ajax({
            url: '/transaction/return/trans-info',
            type: 'GET',
            data: {
                transcode: document.getElementById('translistid').value
            },
            success: function(data){
              document.getElementById('transid').value = document.getElementById('translistid').value;
              document.getElementById('date').value = data[0]['dtmTransDate'];
              document.getElementById('pharmacist').value = 'Luis Guballo';
              document.getElementById('cust').value = data[0]['customer'];
              if(data[0]['customer'] == null){
                document.getElementById('cust').removeAttribute('readonly');
              }else{
                document.getElementById('cust').setAttribute('readonly','');
              }
            }, 
              error: function(xhr){
              }
        });

    $.ajax({
            url: '/transaction/return/trans-det',
            type: 'GET',
            data: {
                transcode: document.getElementById('translistid').value
            },

            success: function(data2){
              for(var i = 0; i < data2.length; i++){
                var $table = $("#bought");

                if(data2[i]['Type'] == 0){
                  if(data2[i]['Brand'] == null) data2[i]['Brand'] = '';

                  var name = data2[i]['Brand'] + ' ' + data2[i]['Generic'] + ' ' + data2[i]['MedSize'];
                }else{
                  var name = data2[i]['NMedName'] + ' ' + data2[i]['NMedSize'];
                }

                if(data2[i]['intPcOrPack'] == 0){
                  var price = data2[i]['PricePerPiece'];
                  var pricetype = 'PC';
                }else{
                  var price = data2[i]['PricePerPackage'];
                  var pricetype = 'PK';
                }

                var total = parseFloat(price * data2[i]['Quantity']);

                var newTR = $("<tr>" + 
                    "<td>"
                      + data2[i]['Code'] + 
                    "</td>" + 
                    "<td>" 
                      + name +
                    "</td>" + 
                    "<td>" 
                      + data2[i]['Quantity'] +
                    "</td>" + 
                    "<td>" 
                      + pricetype +
                    "</td>" + 
                    "<td>" 
                      + (parseFloat(price).toFixed(2)).toString() +
                    "</td>" + 
                    "<td>" 
                      + (parseFloat(total).toFixed(2)).toString() +
                    "</td>" + 
                    "<td>"+
                    "<button onclick=\"setData('" + 
                      data2[i]['Code'] + "','" + 
                      name + "','" + 
                      data2[i]['Quantity'] + "','" + 
                      pricetype + "','" + 
                      price + "','" + 
                      i + "')\">></button></td>" + 
                    + "</tr>");

                $table.append(newTR);
              }
            }, 
              error: function(xhr){
              }
        });
  }

  function setData(code, name, quantity, pricetype, price, row){
    document.getElementById('code').value = code;
    document.getElementById('name').value = name;
    document.getElementById('qty').value = quantity;
    document.getElementById('pricetype').value = pricetype;
    document.getElementById('price').value = price;
    document.getElementById('row').value = row;
    $('#modalqty').modal('show');
  }

  function setTotal(){
    var counter = 0;
    for(var i = 1; i < document.getElementById('returned').rows.length; i++){
      counter = counter + parseFloat(document.getElementById('returned').getElementsByTagName('TR')[i].getElementsByTagName('TD')[6].innerHTML);
    }
    document.getElementById('totalreturns').value = "" + (counter.toFixed(2)).toString();
  }

  function setTransaction(){
    $.ajax({
            url: '/transaction/return/get-trans-id',
            type: 'GET',
            data: {
                transdate: document.getElementById('transdate').value
            },

            success: function(data){
              document.getElementById('translistid').innerHTML = "";
              var opt = "<option selected disabled> -- SELECT TRANSACTION -- </option>";
              for(var i = 0; i < data.length; i++){
                opt = opt + "<option value='" + data[i]['strTransId'] + "'>" + data[i]['strTransId']  +"</option>";
              }
              document.getElementById('translistid').innerHTML = opt;
            }, 
            error: function(xhr){
              alert("ERROR FETCHING TRANSACTIONS");
            }
        });
  }
</script>
<script>
  function collectItems(){
    for(var i = 1; i < document.getElementById('returned').rows.length; i++){
      document.getElementById('retcode').value = document.getElementById('retcode').value + document.getElementById('returned').getElementsByTagName('TR')[i].getElementsByTagName('TD')[1].innerHTML + ";";
      document.getElementById('retqty').value = document.getElementById('retqty').value + document.getElementById('returned').getElementsByTagName('TR')[i].getElementsByTagName('TD')[3].innerHTML + ";";
      document.getElementById('retcondition').value = document.getElementById('retcondition').value + document.getElementById('returned').getElementsByTagName('TR')[i].getElementsByTagName('TD')[7].innerHTML + ";";
      document.getElementById('retreason').value = document.getElementById('retreason').value + document.getElementById('returned').getElementsByTagName('TR')[i].getElementsByTagName('TD')[8].innerHTML + ";";

      if(document.getElementById('returned').getElementsByTagName('TR')[i].getElementsByTagName('TD')[4].innerHTML == "PC"){
        document.getElementById('retpricetype').value = document.getElementById('retpricetype').value + "0" + ";";
      }else{
        document.getElementById('retpricetype').value = document.getElementById('retpricetype').value + "1" + ";";
      }
    }
  }
</script>
@stop 