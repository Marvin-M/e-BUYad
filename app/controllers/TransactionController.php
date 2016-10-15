<?php

class TransactionController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function showRegistration()
	{
		return View::make('transaction/registration');
	}

    public function showSell()
    {
        $result1 = DB::table('tblloadsetting')
                        ->select('LoadMinimum')
                        ->where('id','=','1')
                        ->first();

        $result2 = DB::table('tblpointsetting')
                        ->select('PointMinimum','PointPercent')
                        ->where('id','=','1')
                        ->first();

        if(Cache::has('branch')){
            $branchname = DB::table('tblBranches')
                        ->select('strBranchName')
                        ->where('strBranchCode', Cache::get('branch'))
                        ->first();
            $branch = $branchname->strBranchName;
        }else{
            $branch = "NO_BRANCH";
        }

        return View::make('transaction/sell')
            ->with('code',(new CodeController())->getTransCode())
            ->with('ptmin', $result2->PointMinimum)
            ->with('ptperc', $result2->PointPercent)
            ->with('ldmin', $result1->LoadMinimum)
            ->with('branchname', $branch);
    }

    public function showReload()
    {
        $result = DB::table('tblloadsetting')
                        ->select('LoadMinimum')
                        ->where('id','=','1')
                        ->first();

        return View::make('transaction/reload')
                        ->with('loadmin', $result->LoadMinimum);
    }

    public function showChangePin()
    {
        return View::make('transaction/changepin');
    }

    public function showEGC()
    {
        return View::make('transaction/egc');
    }

    public function showEGCMem()
    {
        return View::make('transaction/egcmem');
    }

    public function showReturn()
    {
        return View::make('transaction/return')
            ->with('retid', (new CodeController())->getReturnsCode());
    }

    public function showGenCard()
    {
        return View::make('transaction/gencard');
    }

    public function showDeacCard(){
        return View::make('transaction/deaccard');
    }
    public function getEGCDetails(){
        $res = DB::table('tblEGC')
        ->where('strEGCPinCode', '=', Input::get('egcpincode'))
        ->get();

        return Response::json($res);
    }

    public function getMemDetails(){
        $res = DB::select(
            'SELECT m.strMemFName, m.strMemMName, m.strMemLName, b.decMCreditValue
             FROM tblMember m LEFT JOIN tblMemCredit b ON m.strMemCode = b.strMCreditCode
             WHERE m.strMemCode = ?',
             [
                Input::get('memcode')
             ]
            );

        return Response::json($res);
    }

    public function getPinCode(){
        $res = DB::select(
                    'SELECT ma.strMemAcctPinCode
                     FROM tblMemAccount ma
                     LEFT JOIN tblMemCard mc
                        ON ma.strMemAcctCode = mc.strMCardCode
                     WHERE 
                        mc.strMCardCode = ? AND
                        mc.strMCardId = ?',
                     [
                        Input::get('memcode'),
                        Input::get('cardid')
                     ]
                    );

        return Response::json($res);
    }

    public function showCashReceipt(){
        $pdf = App::make('dompdf'); 

        // -- arrays of to be inserted
        $prodcode = explode(';',Input::get('prodcode'));
        $prodname = explode(';',Input::get('prodname'));
        $prodprice = explode(';',Input::get('prodprice'));
        $prodpricemode = explode(';',Input::get('prodpricemode'));
        $prodqty = explode(';',Input::get('prodqty'));
        $prodamt = explode(';',Input::get('prodamt'));

        // -- additional cash
        $addcash = floatval(Input::get('addcash')) - floatval(Input::get('sumchan'));

        // -- computations
        $discform = floatval(Input::get('proddiscamt'));
        $discname = substr(Input::get('proddisc'),0,16).strval($discform * 100).'%)';
        $totalform = floatval(Input::get('prodrawtotal'));

        $vat = $this->getCurrentVat();

        if($discname == 'Senior Citizen (20%)'){
            $vatamt = 0;
            $vatex = 0;
            $vatex = ($totalform - ($totalform * $vat));
            $vatable = 0;
        }else{
            $vatex = 0; 
            $vatamt = ($totalform * $vat);
            $vatable = ($totalform - $vatamt);
        }

        $subtotal = ($vatex + $vatamt + $vatable);

        if($discform != 0){
            if($discform < 1){
                $discamt = $subtotal * $discform;
            }else{
                $discamt = $discform;
            }
        }else{
            $discamt = 0;
        }

        $total = ($subtotal - $discamt);


        // -- inserting into tblTransaction
        $res = DB::insert(
        'INSERT INTO tblTransaction VALUES (?,now(),?,?,(SELECT strDiscCode FROM tblDiscounts WHERE strDiscName = ?),?,?,?,?)',
         [
            Input::get('prodtransid'),
            'EMP00001',
            null,
            substr(Input::get('proddisc'),0,(strpos(Input::get('proddisc'),"("))-1),
            0,
            Cache::get('branch'),
            $discamt,
            0
         ]
        );

        // -- insert into tblTransDetails
        $strtbl = "";
        for($i = 0; $i < (sizeof($prodcode)-1); $i++){

            if($prodpricemode[$i] == "0"){
                $pricemode = "PC";
            }else{
                $pricemode = "PK";
            }

            if($i == 0){
                $strtbl = $strtbl.'<tr>
                        <td align="center">'.$prodqty[$i].' '.$pricemode.'</td>
                        <td>'.substr($prodname[$i],0,10).'...<br>Php '.$prodprice[$i].'</td>
                        <td align="right">Php '.strval(number_format($prodamt[$i],2,'.',',')).'</td>
                    </tr>';
            }else{
                $strtbl = $strtbl.'<tr>
                        <td align="center">'.$prodqty[$i].' '.$pricemode.'</td>
                        <td>'.substr($prodname[$i],0,10).'...<br>'.$prodprice[$i].'</td>
                        <td align="right">'.strval(number_format($prodamt[$i],2,'.',',')).'</td>
                    </tr>';
            }

            $res = DB::insert(
            'INSERT INTO tblTransDetails VALUES (null,?,?,?,?)',
             [
                Input::get('prodtransid'),
                $prodcode[$i],
                $prodqty[$i],
                $prodpricemode[$i],
             ]
            );

            DB::update('UPDATE tblBranProd SET intStock = intStock - ? WHERE strBPBranCode = ? AND strBPProdCode = ?',
                [
                    $prodqty[$i],
                    Cache::get('branch'),
                    $prodcode[$i]
                ]);

        }

        $branchname = Cache::get('branchname');
        $empname = Session::get('username');

        $pdf->loadHTML('
            <html>
            <head>
            </head>
            <style type="text/css">
                body{
                    font-family: "Monospace";
                    font-size: 12px;
                }
                #notheader{
                    margin-left: 200px;
                }
            </style>
            <body>
                <div id="header1">
                    <center><b>
                    <br>--------------------------------------------
                    <br>E-BUYAD
                    <br>Point of Sale
                    <br>and
                    <br>Cashless Payment System
                    <br>--------------------------------------------
                    </b></center>
                </div>
                <div id="notheader">
                    <div id="header2">
                        <br>TRANSACTION ID: '.Input::get('prodtransid').'
                        <br>DATETIME: '.date("Y-m-d").' '.date("h:ia",strtotime("+8 Hours")).'
                        <br>PHARMACIST: '.$empname.'
                        <br>BRANCH: '.$branchname.'<br><br>
                    </div>
                    <div id="details">
                        <table width="60%">
                            <col width="120px">
                            <col width="120px">
                            <col width="120px">
                            <tr>
                                <th> QTY </th>
                                <th align="left"> ITEM        </th>
                                <th align="right"> AMT </th>
                            </tr>
                            '.$strtbl.'
                        </table>
                    </div>
                    <div id="Footer">
                        <br>VATABLE AMT: '.strval($vatable).'
                        <br>VAT EXEMPT: '.strval(round($vatex,2)).'
                        <br>VAT (12%): '.strval(round($vatamt,2)).'
                        <br><b>SUBTOTAL</b>: '.strval(round($subtotal,2)).'
                        <br><b>DISCOUNT</b>: '.strval(round($discamt,2)).'
                        <br><b>TOTAL</b>: Php '.strval(number_format($total,2,'.',',')).'
                        <br>
                        <br>AMT TENDERED: Php '.strval(number_format(Input::get('sumamt'),2,'.',',')).'
                        <br>CHANGE: Php '.strval(number_format(Input::get('sumchan'),2,'.',',')).'
                    </div>
                </div>
            </body>
            </html>
            ');
        return $pdf->stream();
    }

    public function showCardReceipt(){
        $pdf = App::make('dompdf'); 

        // -- arrays of to be inserted
        $prodcode = explode(';',Input::get('prodcode'));
        $prodname = explode(';',Input::get('prodname'));
        $prodprice = explode(';',Input::get('prodprice'));
        $prodpricemode = explode(';',Input::get('prodpricemode'));
        $prodqty = explode(';',Input::get('prodqty'));
        $prodamt = explode(';',Input::get('prodamt'));

        // -- member code
        $memcode = Input::get('prodmemcode');

        // -- additional cash
        if(floatval(Input::get('addcash')) > 0){
            $addcash = floatval(Input::get('addcash')) - floatval(Input::get('sumchan'));
        }else{
            $addcash = 0;
        }

        // -- computations
        $discform = floatval(Input::get('proddiscamt'));
        $discname = substr(Input::get('proddisc'),0,16).strval($discform * 100).'%)';
        $totalform = floatval(Input::get('prodrawtotal'));

        $vat = $this->getCurrentVat();

        if($discname == 'Senior Citizen (20%)'){
            $vatamt = 0;
            $vatex = 0;
            $vatex = ($totalform - ($totalform * $vat));
            $vatable = 0;
        }else{
            $vatex = 0; 
            $vatamt = ($totalform * $vat);
            $vatable = ($totalform - $vatamt);
        }

        $subtotal = ($vatex + $vatamt + $vatable);

        if($discform != 0){
            if($discform < 1){
                $discamt = $subtotal * $discform;
            }else{
                $discamt = $discform;
            }
        }else{
            $discamt = 0;
        }

        $total = ($subtotal - $discamt);

        if(sizeof($memcode) <= 0){
            $memcode = null;
        }


        // -- inserting into tblTransaction
        $res = DB::insert(
        'INSERT INTO tblTransaction VALUES (?,now(),?,?,(SELECT strDiscCode FROM tblDiscounts WHERE strDiscName = ?),?,?,?,?)',
         [
            Input::get('prodtransid'),
            'EMP00001',
            $memcode,
            substr(Input::get('proddisc'),0,(strpos(Input::get('proddisc'),"("))-1),
            1,
            Cache::get('branch'),
            $discamt,
            $addcash
         ]
        );

        // -- insert into tblTransDetails
        $strtbl = "";
        for($i = 0; $i < (sizeof($prodcode)-1); $i++){

            if($prodpricemode[$i] == "0"){
                $pricemode = "PC";
            }else{
                $pricemode = "PK";
            }

            if($i == 0){
                $strtbl = $strtbl.'<tr>
                        <td align="center">'.$prodqty[$i].' '.$pricemode.'</td>
                        <td>'.substr($prodname[$i],0,10).'...<br>Php '.$prodprice[$i].'</td>
                        <td align="right">Php '.strval(number_format($prodamt[$i],2,'.',',')).'</td>
                    </tr>';
            }else{
                $strtbl = $strtbl.'<tr>
                        <td align="center">'.$prodqty[$i].' '.$pricemode.'</td>
                        <td>'.substr($prodname[$i],0,10).'...<br>'.$prodprice[$i].'</td>
                        <td align="right">'.strval(number_format($prodamt[$i],2,'.',',')).'</td>
                    </tr>';
            }

            $res = DB::insert(
            'INSERT INTO tblTransDetails VALUES (null,?,?,?,?)',
             [
                Input::get('prodtransid'),
                $prodcode[$i],
                $prodqty[$i],
                $prodpricemode[$i],
             ]
            );

            DB::update('UPDATE tblBranProd SET intStock = intStock - ? WHERE strBPBranCode = ? AND strBPProdCode = ?',
                [
                    $prodqty[$i],
                    Cache::get('branch'),
                    $prodcode[$i]
                ]);
        }

        // -- update credit
        $res = DB::update(
            'UPDATE tblMemCredit SET decMCreditValue = decMCreditValue - ?, dtmLastUpdate = now() WHERE strMCreditCode = ?',
            [
                floatval(Input::get('prodbal')),
                Input::get('prodmemcode')
            ]
            );

        $balance = 0;

        //update insert
        DB::insert('INSERT INTO tblMemPoints VALUES(?,?,?)',
                    [Input::get('prodtransid'),Input::get('prodmemcode'),Input::get('prodpts')]);

        DB::insert('INSERT INTO tblMemCreditChange VALUES(null,?,?,now(),?,?,?)',
            [
                Input::get('prodmemcode'),
                floatval(Input::get('prodbal')),
                Input::get('prodtransid'),
                Cache::get('branch'),
                1
            ]);

        DB::update(
            'UPDATE tblMemCredit SET decMCreditValue = decMCreditValue + ?, dtmLastUpdate = now() WHERE strMCreditCode = ?',
            [
                floatval(Input::get('prodpts')),
                Input::get('prodmemcode')
            ]
            );

        $res  = DB::table('tblMemCredit')
                ->select('decMCreditValue')
                ->where('strMCreditCode','=',Input::get('prodmemcode'))
                ->first();

        $balance = $res->decMCreditValue;

        DB::insert('INSERT INTO tblMemCreditChange VALUES(null,?,?,now(),?,?,?)',
            [
                Input::get('prodmemcode'),
                floatval(Input::get('prodpts')),
                Input::get('prodtransid'),
                Cache::get('branch'),
                2
            ]);

        $branchname = Cache::get('branchname');
        $empname = Session::get('username');

        $pdf->loadHTML('
            <html>
            <head>
            </head>
            <style type="text/css">
                body{
                    font-family: "Monospace";
                    font-size: 12px;
                }
                #notheader{
                    margin-left: 200px;
                }
            </style>
            <body>
                <div id="header1">
                    <center><b>
                    <br>-------------------------------  
                    <br>E-BUYAD
                    <br>Point of Sale
                    <br>and
                    <br>Cashless Payment System
                    <br>------------------------------- 
                    </b></center>
                </div>
                <div id="notheader">
                    <div id="header2">
                        <br>TRANSACTION ID: '.Input::get('prodtransid').'
                        <br>DATETIME: '.date("Y-m-d").' '.date("h:ia",strtotime("+8 Hours")).'
                        <br>PHARMACIST: '.$empname.'
                        <br>BRANCH: '.$branchname.'<br><br>
                    </div>
                    <div id="details">
                        <table width="60%">
                            <col width="120px">
                            <col width="120px">
                            <col width="120px">
                            <tr>
                                <th> QTY </th>
                                <th align="left"> ITEM        </th>
                                <th align="right"> AMT </th>
                            </tr>
                            '.$strtbl.'
                        </table>
                    </div>
                    <div id="Footer">
                        <br>VATABLE AMT: '.strval($vatable).'
                        <br>VAT EXEMPT: '.strval(round($vatex,2)).'
                        <br>VAT (12%): '.strval(round($vatamt,2)).'
                        <br><b>SUBTOTAL</b>: '.strval(round($subtotal,2)).'
                        <br><b>DISCOUNT</b>: '.strval(round($discamt,2)).'
                        <br><b>TOTAL</b>: Php '.strval(number_format($total,2,'.',',')).'
                        <br>
                        <br>CUSTOMER: '.Input::get('prodmemname').'
                        <br>CREDIT BALANCE: '.strval(round($balance,2)).'
                        <br>POINTS EARNED: '.Input::get('prodpts').'
                        <br>
                        <br>AMT TENDERED: Php '.strval(number_format(Input::get('sumamt'),2,'.',',')).'
                        <br>CHANGE: Php '.strval(number_format(Input::get('sumchan'),2,'.',',')).'
                    </div>
                </div>
            </body>
            </html>
            ');
        return $pdf->stream();
    }

    public function showReturnReceipt(){
        $pdf = App::make('dompdf'); 

        // -- arrays of to be inserted
        $prodcode = explode(';',Input::get('retprodcode'));
        $prodname = explode(';',Input::get('retname'));
        $prodprice = explode(';',Input::get('retprice'));
        $prodpricemode = explode(';',Input::get('retpricemode'));
        $prodqty = explode(';',Input::get('retqty'));
        $prodamt = explode(';',Input::get('retamtt'));

        // -- additional cash
        if(floatval(Input::get('retcash')) > 0){
            $addcash = floatval(Input::get('retamt')) - floatval(Input::get('rettotal'));
        }else{
            $addcash = 0;
        }

        // -- computations
        $totalform = floatval(Input::get('rettotal'));


        // -- inserting into tblTransaction
        $res = DB::insert(
        'INSERT INTO tblTransaction VALUES (?,now(),?,null,null,3,?,0,?)',
         [
            Input::get('rettransid'),
            'EMP00001',
            Cache::get('branch'),
            $addcash
         ]
        );

        // -- insert into tblTransDetails
        $strtbl = "";
        for($i = 0; $i < (sizeof($prodcode)-1); $i++){

            if($prodpricemode[$i] == "0"){
                $pricemode = "PC";
            }else{
                $pricemode = "PK";
            }

            if($i == 0){
                $strtbl = $strtbl.'<tr>
                        <td align="center">'.$prodqty[$i].' '.$pricemode.'</td>
                        <td>'.substr($prodname[$i],0,10).'...<br>Php '.$prodprice[$i].'</td>
                        <td align="right">Php '.strval(number_format($prodamt[$i],2,'.',',')).'</td>
                    </tr>';
            }else{
                $strtbl = $strtbl.'<tr>
                        <td align="center">'.$prodqty[$i].' '.$pricemode.'</td>
                        <td>'.substr($prodname[$i],0,10).'...<br>'.$prodprice[$i].'</td>
                        <td align="right">'.strval(number_format($prodamt[$i],2,'.',',')).'</td>
                    </tr>';
            }

            $res = DB::insert(
            'INSERT INTO tblTransDetails VALUES (null,?,?,?,?)',
             [
                Input::get('rettransid'),
                $prodcode[$i],
                $prodqty[$i],
                $prodpricemode[$i],
             ]
            );
        }

        DB::update('UPDATE tblReturns    SET isUsed = 1 WHERE strReturnCode = ?', [Input::get('retsumcode')]);

        $branchname = Cache::get('branchname');
        $empname = Session::get('username');
        if($addcash > 0){
            $change = floatval(Input::get('retcash')) - floatval($addcash);
        }else{
            $change = 0;
        }

        $pdf->loadHTML('
            <html>
            <head>
            </head>
            <style type="text/css">
                body{
                    font-family: "Monospace";
                    font-size: 12px;
                }
                #notheader{
                    margin-left: 200px;
                }
            </style>
            <body>
                <div id="header1">
                    <center><b>
                    <br>-------------------------------  
                    <br>E-BUYAD
                    <br>Point of Sale
                    <br>and
                    <br>Cashless Payment System
                    <br>------------------------------- 
                    </b></center>
                </div>
                <div id="notheader">
                    <div id="header2">
                        <br>TRANSACTION ID: '.Input::get('rettransid').'
                        <br>DATETIME: '.date("Y-m-d").' '.date("h:ia",strtotime("+8 Hours")).'
                        <br>PHARMACIST: '.$empname.'
                        <br>BRANCH: '.$branchname.'
                        <br>REF RETURN: '.Input::get('retsumcode').'
                        <br>RETURN AMOUNT: '.strval(number_format(Input::get('retamt'),2,'.',',')).'
                        <br><br>
                    </div>
                    <div id="details">
                        <table width="60%">
                            <col width="120px">
                            <col width="120px">
                            <col width="120px">
                            <tr>
                                <th> QTY </th>
                                <th align="left"> ITEM        </th>
                                <th align="right"> AMT </th>
                            </tr>
                            '.$strtbl.'
                        </table>
                    </div>
                    <div id="Footer">
                        <br><b>TOTAL</b>: Php '.strval(number_format(Input::get('rettotal'),2,'.',',')).'
                        <br>
                        <br>AMT TENDERED: Php '.strval(number_format(Input::get('retcash'),2,'.',',')).'
                        <br>CHANGE: Php '.strval(number_format($change,2,'.',',')).'
                    </div>
                </div>
            </body>
            </html>
            ');
        return $pdf->stream();
    }

    public function getCurrentVat(){
        $vat = 0.12;
        return $vat;
    }

    public function getTransInfo(){
        $res = DB::select(
            'SELECT t.dtmTransDate, concat(c.strMemFName, \' \', c.strMemMName, \' \', c.strMemLName) as \'customer\'
            FROM tbltransaction t LEFT JOIN tblmember c ON t.strTransCustCode = c.strMemCode WHERE t.strTransId = ?',
            [
                Input::get('transcode')
            ]
            );

        return Response::json($res);
    }

    public function fillBought(){
        $res = DB::select(
            'SELECT
                -- product name
                DISTINCT p.strProdCode as \'Code\',
                p.strProdType as \'Type\',
                b.strPMBranName as \'Brand\',
                (
                    SELECT group_concat(g.strPMGenName SEPARATOR \' \')
                    FROM tblmedgennames mg 
                    LEFT JOIN tblprodmedgeneric g
                    ON mg.strMedGenGenCode = g.strPMGenCode
                    WHERE mg.strMedGenMedCode = m.strProdMedCode
                    GROUP BY mg.strMedGenMedCode
                ) as \'Generic\',
                concat(m.decProdMedSize, \' \', u.strUOMName) as \'MedSize\',
                
                nm.strProdNMedName as \'NMedName\',
                concat_ws(\' \', g.strGenSizeName, s.decNMStanSize, un.strUOMName) as \'NMedSize\',

                -- quantity
                td.intQty as \'Quantity\',
                
                -- prices
                (
                    SELECT pr.decProdPricePerPiece
                    FROM tblProdPrice pr
                    WHERE pr.strProdPriceCode = td.strTDProdCode
                    AND pr.dtmUpdated < t.dtmTransDate
                    ORDER BY pr.dtmUpdated DESC LIMIT 1
                ) as \'PricePerPiece\',
                (
                    SELECT pr.decPricePerPackage
                    FROM tblProdPrice pr
                    WHERE pr.strProdPriceCode = td.strTDProdCode
                    AND pr.dtmUpdated < t.dtmTransDate
                    ORDER BY pr.dtmUpdated DESC LIMIT 1
                ) as \'PricePerPackage\',
                td.intPcOrPack
                
            FROM tblTransDetails td
            LEFT JOIN tblTransaction t
                ON td.strTDTransCode = t.strTransId
            LEFT JOIN tblProducts p
                ON td.strTDProdCode = p.strProdCode
            LEFT JOIN tblprodmed m
                ON p.strProdCode = m.strProdMedCode
            LEFT JOIN tblprodnonmed nm
                ON p.strProdCode = nm.strProdNMedCode
            LEFT JOIN tblProdPrice pr
                ON p.strProdCode = pr.strProdPriceCode

            LEFT JOIN tblprodmedbranded b
                ON m.strProdMedBranCode = b.strPMBranCode
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

            WHERE td.strTDTransCode = ?',
            [
                Input::get('transcode')
            ]
            );

        return Response::json($res);
    }

    public function reloadMember(){
        try{
            DB::update('UPDATE tblMemCredit SET decMCreditValue = decMCreditValue + ? WHERE strMCreditCode = ?',
                [Input::get('load'), Input::get('memcode')]);

            DB::insert('INSERT INTO tblMemCreditChange VALUES(null,?,?,now(),?,?,?)',
                    [
                        Input::get('memcode'),
                        intval(Input::get('load')),
                        null,
                        Cache::get('branch'),
                        0
                    ]);
        }catch(PDOException $ex){}
    }

    public function saveReturns(){
        $retcode = Input::get('retid');
        $transcode = Input::get('transid');
        $memcode = Input::get('cust');
        $total = Input::get('totalreturns');

        //action

        $prodcode = explode(';',Input::get('retcode'));
        $prodqty = explode(';',Input::get('retqty'));
        $prodprice = explode(';',Input::get('retpricetype'));
        $cond = explode(';',Input::get('retcondition'));
        $reason = explode(';',Input::get('retreason'));
        
        try{
            DB::insert('INSERT INTO tblReturns VALUES(?,?,?,?,now(),0)',
                [$retcode, $transcode, $memcode, floatval($total)]);

            for($i = 0; $i < (sizeof($prodcode)-1); $i++){
                if($cond[$i] == "GOOD"){
                    $tite = 0;
                    DB::update('UPDATE tblBranProd SET intStock = intStock + ? WHERE strBPBranCode = ? AND strBPProdCode = ?',
                    [
                        $prodqty[$i],
                        Cache::get('branch'),
                        $prodcode[$i]
                    ]);
                }else{
                    $tite = 1;
                }

                DB::insert(
                    'INSERT INTO tblRetDetails VALUES (null,?,?,?,?,?,?)',
                     [
                        $retcode,
                        $prodcode[$i],
                        intval($prodqty[$i]),
                        intval($prodprice[$i]),
                        $tite,
                        $reason[$i]
                     ]
                    );


            }

            $pdf = App::make('dompdf');

            $pdf->loadHTML('
                    <html>
                    <head>
                    </head>
                    <style type="text/css">
                        body{
                            font-family: "Monospace";
                        }
                        #notheader{
                            margin-left: 200px;
                        }
                    </style>
                    <body>
                        <div id="header1">
                            <center><b>
                            <br>-------------------------------  
                            <br>E-BUYAD
                            <br>Point of Sale
                            <br>and
                            <br>Cashless Payment System
                            <br>------------------------------- 
                            <br>
                            <br>RETURN SLIP
                            </b></center>
                        </div>
                        <div id="notheader">
                            <div id="header2">
                                <br>RETURNS ID: '.$retcode.' 
                                <br>DATETIME: '.date("Y-m-d").' '.date("h:ia",strtotime("+8 Hours")).'
                                <br>PHARMACIST: '.Session::get('username').'<br><br>
                            </div>
                            <div id="details">
                                <br>AMOUNT: '.$total.'
                            </div>
                            <div id="Footer">
                                <br>
                                <br>CUSTOMER: '.$memcode.'
                            </div>
                        </div>
                    </body>
                    </html>
            ');
        return $pdf->stream();   
                
        }catch(PDPException $e){}     
    }

    public function getReturnsDetails(){
        $result = DB::select('SELECT strCustName, decTotalAmount FROM tblReturns
                                WHERE strReturnCode = ? AND isUsed = 0',
                                [
                                    Input::get('returns')
                                ]);
        
        return Response::json($result);
    }

    public function getTransactions(){
        if(Input::get('transdate') != "0"){
            $date = strval(Input::get('transdate')).' 00:00:00';
            $date2 = strval(date('Y-m-d', strtotime($date." +1 day"))).' 00:00:00';
            $result = DB::select(
                'SELECT t.strTransId
                    FROM tblTransaction t
                    WHERE 
                        t.strTransBranCode = ? AND
                        (t.dtmTransDate BETWEEN ? AND ?)
                    ORDER BY t.dtmTransDate DESC',
                [
                    Cache::get('branch'),
                    $date,
                    $date2
                ]);
        }else{
            $result = DB::select(
                'SELECT t.strTransId
                    FROM tblTransaction t
                    WHERE 
                        t.strTransBranCode = ? 
                    ORDER BY t.dtmTransDate DESC',
                [
                    Cache::get('branch')
                ]);
        }
        return Response::json($result);
    }

    public function verifyEGCAccount(){
        $id = Input::get('egcid');
        $pin = Input::get('egcpin');

        // $res = DB::table('tblEGC')
        //      ->select('strEGCCode')
        //      ->where('strEGCCode',$id)
        //      ->where('strEGCPinCode',$pin)
        //      ->first();

        $res = DB::select(
            'SELECT strEGCCode, intEGCType FROM tblEGC
                WHERE strEGCCode = ? AND strEGCPinCode = ? LIMIT 1',
            [
                'EGC'.$id,
                $pin
            ] );

        return Response::json($res);
    }

    public function getEGCProducts(){
        $res = DB::select(
                           'SELECT 
                                e.strEGCBeneficiary as \'Beneficiary\',
                                ep.strEPProdCode as \'Code\',
                                ep.intQty as \'Quantity\',
                                bp.intStock as \'Stock\',
                                p.strProdType as \'Type\',
                                b.strPMBranName as \'Brand\',
                                (
                                    SELECT group_concat(g.strPMGenName SEPARATOR \' \')
                                    FROM tblmedgennames mg 
                                    LEFT JOIN tblprodmedgeneric g
                                    ON mg.strMedGenGenCode = g.strPMGenCode
                                    WHERE mg.strMedGenMedCode = m.strProdMedCode
                                    GROUP BY mg.strMedGenMedCode
                                ) as \'Generic\',
                                concat(m.decProdMedSize, \' \', u.strUOMName) as \'MedSize\',
                                
                                nm.strProdNMedName as \'NMedName\',
                                concat_ws(\' \', g.strGenSizeName, s.decNMStanSize, un.strUOMName) as \'NMedSize\',
                                
                                (
                                    SELECT pr.decProdPricePerPiece
                                    FROM tblProdPrice pr
                                    WHERE pr.strProdPriceCode = ep.strEPProdCode
                                    AND pr.dtmUpdated < now()
                                    ORDER BY pr.dtmUpdated DESC LIMIT 1
                                ) as \'PricePerPiece\'

                            FROM tblEGCProds ep
                            LEFT JOIN tblEGC e
                                ON ep.strEPEGCCode = e.strEGCCode
                            LEFT JOIN tblProducts p
                                ON ep.strEPProdCode = p.strProdCode
                            LEFT JOIN tblBranProd bp
                                ON ep.strEPProdCode = bp.strBPProdCode
                            LEFT JOIN tblprodmed m
                                ON p.strProdCode = m.strProdMedCode
                            LEFT JOIN tblprodnonmed nm
                                ON p.strProdCode = nm.strProdNMedCode
                            LEFT JOIN tblprodmedbranded b
                                ON m.strProdMedBranCode = b.strPMBranCode
                            LEFT JOIN tbluom u 
                                ON m.strProdMedUOMCode = u.strUOMCode
                            LEFT JOIN tblnmedgeneral gt
                                ON nm.strProdNMedCode = gt.strNMGenCode
                            LEFT JOIN tblgensize g
                                ON gt.strNMGenSizeCode = g.strGenSizeCode
                            LEFT JOIN tblnmedstandard s
                                ON nm.strProdNMedCode = s.strNMStanCode
                            LEFT JOIN tbluom un
                                ON s.strNMStanUOMCode = un.strUOMCode
                            WHERE ep.strEPEGCCode = ?',
                            [
                                Input::get('prodegcid')
                            ]);

        return Response::json($res);   
    }

    public function getEGCAmount(){
        $res = DB::select('SELECT 
                                e.strEGCBeneficiary,
                                eb.decEBBalance
                            FROM tblEGC e
                            LEFT JOIN tblEGCBalance eb
                                ON e.strEGCCode = eb.strEBEGCCode
                            WHERE e.strEGCCode = ?',
                            [
                                Input::get('amtegccode')
                            ]);

        return Response::json($res);
    }

    public function saveEGCProducts(){
        $pdf = App::make('dompdf'); 

        // -- arrays of to be inserted
        $prodcode = explode(';',Input::get('segcprodcode'));
        $prodname = explode(';',Input::get('segcprodname'));
        $prodqty = explode(';',Input::get('segcprodqty'));


        // -- insert into tblTransDetails
        $strtbl = "";
        for($i = 0; $i < (sizeof($prodcode)-1); $i++){
            $strtbl = $strtbl.'<tr>
                    <td align="center">'.$prodqty[$i].'</td>
                    <td>'.substr($prodname[$i],0,20).'</td>
                </tr>';

            DB::update('UPDATE tblEGCProds SET intQty = intQty - ? WHERE strEPEGCCode = ? AND strEPProdCode = ?',
                [
                    $prodqty[$i],
                    Input::get('segccode'),
                    $prodcode[$i]
                ]);
        }


        $branchname = Cache::get('branchname');
        $empname = Session::get('username');

        $pdf->loadHTML('
            <html>
            <head>
            </head>
            <style type="text/css">
                body{
                    font-family: "Monospace";
                    font-size: 12px;
                }
                #notheader{
                    margin-left: 200px;
                }
            </style>
            <body>
                <div id="header1">
                    <center><b>
                    <br>-------------------------------  
                    <br>E-BUYAD
                    <br>Point of Sale
                    <br>and
                    <br>Cashless Payment System
                    <br>------------------------------- 
                    </b></center>
                </div>
                <div id="notheader">
                    <div id="header2">
                        <br>DATETIME: '.date("Y-m-d").' '.date("h:ia",strtotime("+8 Hours")).'
                        <br>PHARMACIST: '.$empname.'
                        <br>BRANCH: '.$branchname.'
                        <br>REF EGC: '.Input::get('segccode').'
                        <br><br>
                    </div>
                    <div id="details">
                        <table width="60%">
                            <col width="120px">
                            <col width="240px">
                            <tr>
                                <th> QTY </th>
                                <th align="left"> ITEM        </th>
                            </tr>
                            '.$strtbl.'
                        </table>
                    </div>
                    <div id="Footer">
                        <br><b>BENEFICIARY</b>: '.Input::get('segcbene').'
                    </div>
                </div>
            </body>
            </html>
            ');
        return $pdf->stream();
    }

    public function saveEGCAmount(){
        $pdf = App::make('dompdf'); 

        // -- arrays of to be inserted
        $total = floatval(Input::get('egctotal'));
        $balance = floatval(Input::get('egcamt'));
        $addcash = floatval(Input::get('egccash'));
        $egccode = Input::get('egcsumcode');

        $branchname = Cache::get('branchname');
        $empname = "Luis Guballo";

        if($addcash > 0){
            $additionalcash = '<br>ADD CASH: Php '.strval(number_format($addcash,2,'.',','));
            $used = $balance;
            $balance = 0;

            DB::update('UPDATE tblEGCBalance SET decEBBalance = 0 WHERE strEBEGCCode = ?',
                [$egccode]);
        }else{
            $additionalcash = "";
            $used = $balance - ($balance - $total);
            $balance = $balance - $total;

            DB::update('UPDATE tblEGCBalance SET decEBBalance = ? WHERE strEBEGCCode = ?',
                [$balance, $egccode]);
        }

        $pdf->loadHTML('
            <html>
            <head>
            </head>
            <style type="text/css">
                body{
                    font-family: "Monospace";
                    font-size: 12px;
                }
                #notheader{
                    margin-left: 200px;
                }
            </style>
            <body>
                <div id="header1">
                    <center><b>
                    <br>-------------------------------  
                    <br>E-BUYAD
                    <br>Point of Sale
                    <br>and
                    <br>Cashless Payment System
                    <br>------------------------------- 
                    </b></center>
                </div>
                <div id="notheader">
                    <div id="header2">
                        <br>DATETIME: '.date("Y-m-d").' '.date("h:ia",strtotime("+8 Hours")).'
                        <br>PHARMACIST: '.$empname.'
                        <br>BRANCH: '.$branchname.'
                        <br>REF EGC: '.$egccode.'
                        <br><br>
                    </div>
                    <div id="details">
                    </div>
                    <div id="Footer">
                        <br><b>TOTAL</b>: Php '.strval(number_format($total,2,'.',',')).'
                        <br>
                        <br>AMT USED: Php '.strval(number_format($used,2,'.',',')).'
                        '.$additionalcash.'
                        <br>BALANCE: Php '.strval(number_format($balance,2,'.',',')).'
                    </div>
                </div>
            </body>
            </html>
            ');
        return $pdf->stream();
    }

    public function deleteMemCard(){
        $memid = Input::get('code');
        $memcardid = Input::get('card');

        try{
            DB::insert('INSERT INTO tblMemCardDeactivated VALUES(null,?,?)',
                [
                    $memid,
                    $memcardid
                ]);

            DB::table('tblMemCard')
                ->where('strMCardCode','=',$memid)
                ->delete();

            DB::table('tblMemAccount')
                ->where('strMemAcctCode','=',$memid)
                ->delete();
            return Redirect::to('/transaction/deactivate-card');
        }catch(PDOException $ex){
            return Redirect::to('/transaction/deactivate-card');
        }
    }

    public function updateMemPinCode(){
        try{
            DB::update('UPDATE tblMemAccount SET strMemAcctPinCode = ? WHERE strMemAcctCode = ?',
                [  
                    Input::get('pint'),
                    Input::get('code')
                ]);
            return Redirect::to('/transaction/change-pin');
        }catch(Exception $ex){
            return Redirect::to('/transaction/change-pin');
        }
    }
}
