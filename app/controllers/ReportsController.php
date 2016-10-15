<?php

class ReportsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function sales()
    {
        return View::make('reports/salesreport');
	}

    public function product()
    {
        return View::make('reports/productreport');
    }

    public function credit()
    {
        return View::make('reports/creditreport');
    }
    
    public function dailyCredit(){
        $res = DB::select(
                        'SELECT 
                            DATE(dtmMCCChanged) as \'Dates\', 
                            SUM(decMCCValue) as \'Total\'
                        FROM tblMemCreditChange 
                        WHERE intMCCType = 0 
                        AND DATE(dtmMCCChanged) = ?
                        GROUP BY DATE(dtmMCCChanged) 
                        ORDER BY DATE(dtmMCCChanged) ASC',
                            [
                                Input::get('repdate')
                            ]);

        return Response::json($res);
    }

    public function weeklyCredit(){
        $lastdate = date('Y-m-d',strtotime(Input::get('repdate').' + 1 day'));
        $firstdate = date('Y-m-d',strtotime(Input::get('repdate').' - 8 days'));

        $res = DB::select(
                        'SELECT 
                            DATE(dtmMCCChanged) as \'Dates\', 
                            SUM(decMCCValue) as \'Total\'
                        FROM tblMemCreditChange 
                        WHERE intMCCType = 0 
                        AND dtmMCCChanged BETWEEN ? AND ?
                        GROUP BY DATE(dtmMCCChanged) 
                        ORDER BY DATE(dtmMCCChanged) ASC',
                            [
                                $firstdate,
                                $lastdate
                            ]);
        return Response::json($res);
    }

    public function monthlyCredit(){
        $month = Input::get('month');
        $year = Input::get('year');

        $datestart = $year.'-'.$month.'-01 00:00:00';
        $nextdate = date('Y-m-d', strtotime($datestart.' + 1 month')).' 00:00:00';

        $res = DB::select(
                        'SELECT 
                            DATE(dtmMCCChanged) as \'Dates\', 
                            SUM(decMCCValue) as \'Total\'
                        FROM tblMemCreditChange 
                        WHERE intMCCType = 0 
                        AND dtmMCCChanged BETWEEN ? AND ?
                        GROUP BY DATE(dtmMCCChanged) 
                        ORDER BY DATE(dtmMCCChanged) ASC',
                            [
                                $datestart,
                                $nextdate
                            ]);
        return Response::json($res);
    }

    public function quarterlyCredit(){
        $month = Input::get('month');
        $year = Input::get('year');

        $datestart = $year.'-'.$month.'-01 00:00:00';
        $nextdate = date('Y-m-d', strtotime($datestart.' + 3 months')).' 00:00:00';

        $res = DB::select(
                        'SELECT 
                            DATE(dtmMCCChanged) as \'Dates\', 
                            SUM(decMCCValue) as \'Total\'
                        FROM tblMemCreditChange 
                        WHERE intMCCType = 0 
                        AND dtmMCCChanged BETWEEN ? AND ?
                        GROUP BY DATE(dtmMCCChanged) 
                        ORDER BY DATE(dtmMCCChanged) ASC',
                            [
                                $datestart,
                                $nextdate
                            ]);

        return Response::json($res)
        ;
    }

    public function yearlyCredit(){
        $firstdate = Input::get('year');
        $nextdate = strval(intval(Input::get('year')) + 1);

        $firstdate = $firstdate.'-01-01 00:00:00';
        $nextdate = $nextdate.'-01-01 00:00:00';

        $res = DB::select(
                        'SELECT 
                            DATE(dtmMCCChanged) as \'Dates\', 
                            SUM(decMCCValue) as \'Total\'
                        FROM tblMemCreditChange 
                        WHERE intMCCType = 0 
                        AND dtmMCCChanged BETWEEN ? AND ?
                        GROUP BY DATE(dtmMCCChanged) 
                        ORDER BY DATE(dtmMCCChanged) ASC',
                            [
                                $firstdate,
                                $nextdate
                            ]);
        return Response::json($res);
    }

    public function customCredit(){
        $firstdate = Input::get('dtfir');
        $nextdate = date('Y-m-d',strtotime(Input::get('dtnex').' + 1 day')).' 00:00:00';

        $res = DB::select(
                        'SELECT 
                            DATE(dtmMCCChanged) as \'Dates\', 
                            SUM(decMCCValue) as \'Total\'
                        FROM tblMemCreditChange 
                        WHERE intMCCType = 0 
                        AND dtmMCCChanged BETWEEN ? AND ?
                        GROUP BY DATE(dtmMCCChanged) 
                        ORDER BY DATE(dtmMCCChanged) ASC',
                            [
                                $firstdate,
                                $nextdate
                            ]);
        return Response::json($res);
    }

    public function dailyReportPDF(){
        $pdf = App::make('dompdf'); 

        $res = DB::select(
                            'SELECT 
                                DATE(dtmMCCChanged) as \'Dates\', 
                                SUM(decMCCValue) as \'Total\'
                            FROM tblMemCreditChange 
                            WHERE intMCCType = 0 
                            AND DATE(dtmMCCChanged) = ?
                            GROUP BY DATE(dtmMCCChanged) 
                            ORDER BY DATE(dtmMCCChanged) ASC',
                            [
                               Input::get('dailydate')
                            ]);
        $total = 0;
        $strtbl = "";
        foreach($res as $data){
            $strtbl = $strtbl.'<tr>
                                    <td align="center"> '.$data->Dates.' </td>
                                    <td align="center"> '.strval(number_format($data->Total,2,'.',',')).' </td>
                                </tr>';
            $total = $total + $data->Total;
        }


        $pdf->loadHTML('<html>
                <head>
                </head>
                <style type="text/css">
                    body{
                        font-family: "Monospace";
                    }
                    #notheader{
                        margin-left: 10px;
                        margin-right: 10px;
                    }
                </style>
                <body>
                    <div id="header1">
                        <center><b>
                        <br>
                        <h3>E-BUYAD</h3>
                        Caloocan City<br>
                        09755128084<br>
                        ebuyad@Gmail.com<br>
                        <br>
                        E-Buyad Cash Deposit Report
                        <br>
                        </b></center>
                    </div>
                    <div id="notheader">
                        <div id="header2">
                            <br><b>CREATED BY:</b> Luis Guballo
                            <br><b>DATE: </b> '.date("F j, Y, g:i a").'
                            <br>
                            <br>
                            <br><b>TOTAL CASH DEPOSITS: Php '.strval(number_format($total,2,'.',',')).' </b>
                            <br>
                            <br><br>
                        </div>
                        <div id="details">
                            <table width="100%"">
                                <col width="150px">
                                <col width="150px">
                                <tr>
                                    <th align="center"> DATE </th>
                                    <th align="center"> AMOUNT </th>
                                </tr>
                                '.$strtbl.'
                            </table>
                        </div>
                    </div>
                </body>
                </html>');

        return $pdf->stream();
    }

    public function weeklyReportPDF(){
        $pdf = App::make('dompdf'); 

        $lastdate = date('Y-m-d',strtotime(Input::get('weeklydate').' + 1 day'));
        $firstdate = date('Y-m-d',strtotime(Input::get('weeklydate').' - 8 days'));

        $res = DB::select(
                            'SELECT 
                                DATE(dtmMCCChanged) as \'Dates\', 
                                SUM(decMCCValue) as \'Total\'
                            FROM tblMemCreditChange 
                            WHERE intMCCType = 0 
                            AND DATE(dtmMCCChanged) BETWEEN ? AND ?
                            GROUP BY DATE(dtmMCCChanged) 
                            ORDER BY DATE(dtmMCCChanged) ASC',
                            [
                               $firstdate,
                               $lastdate
                            ]);
        $total = 0;
        $strtbl = "";
        foreach($res as $data){
            $strtbl = $strtbl.'<tr>
                                    <td align="center"> '.$data->Dates.' </td>
                                    <td align="center"> '.strval(number_format($data->Total,2,'.',',')).' </td>
                                </tr>';
            $total = $total + $data->Total;
        }

        def("DOMPDF_ENABLE_REMOTE", false);
        $pdf->loadHTML('<html>
                <head>
                </head>
                <style type="text/css">
                    body{
                        font-family: "Monospace";
                    }
                    #notheader{
                        margin-left: 10px;
                        margin-right: 10px;
                    }
                </style>
                <body>
                    <div id="header1">
                        <center>
                        <img src="{{URL::to(\'/images/logo.png\')}}" style="height: 5%; width:5%">
                        <b>
                        <br>
                        <h3>E-BUYAD</h3>
                        Caloocan City<br>
                        09755128084<br>
                        ebuyad@Gmail.com<br>
                        <br>
                        E-Buyad Cash Deposit Report
                        <br>
                        </b></center>
                    </div>
                    <div id="notheader">
                        <div id="header2">
                            <br><b>CREATED BY:</b> Luis Guballo
                            <br><b>DATE: </b> '.date("F j, Y, g:i a").'
                            <br>
                            <br>
                            <br><b>TOTAL CASH DEPOSITS: Php '.strval(number_format($total,2,'.',',')).' </b>
                            <br>
                            <br><br>
                        </div>
                        <div id="details">
                            <table width="100%"">
                                <col width="150px">
                                <col width="150px">
                                <tr>
                                    <th align="center"> DATE </th>
                                    <th align="center"> AMOUNT </th>
                                </tr>
                                '.$strtbl.'
                            </table>
                        </div>
                    </div>
                </body>
                </html>');

        return $pdf->stream();
    }

    public function monthlyReportPDF(){
        $pdf = App::make('dompdf'); 

        $datestart = Input::get("monthlydate");
        $nextdate = date('Y-m-d', strtotime($datestart.' + 1 month')).' 00:00:00';

        $res = DB::select(
                            'SELECT 
                                DATE(dtmMCCChanged) as \'Dates\', 
                                SUM(decMCCValue) as \'Total\'
                            FROM tblMemCreditChange 
                            WHERE intMCCType = 0 
                            AND DATE(dtmMCCChanged) BETWEEN ? AND ?
                            GROUP BY DATE(dtmMCCChanged) 
                            ORDER BY DATE(dtmMCCChanged) ASC',
                            [
                               $datestart,
                               $nextdate
                            ]);
        $total = 0;
        $strtbl = "";
        foreach($res as $data){
            $strtbl = $strtbl.'<tr>
                                    <td align="center"> '.$data->Dates.' </td>
                                    <td align="center"> '.strval(number_format($data->Total,2,'.',',')).' </td>
                                </tr>';
            $total = $total + $data->Total;
        }

        def("DOMPDF_ENABLE_REMOTE", false);
        $pdf->loadHTML('<html>
                <head>
                </head>
                <style type="text/css">
                    body{
                        font-family: "Monospace";
                    }
                    #notheader{
                        margin-left: 10px;
                        margin-right: 10px;
                    }
                </style>
                <body>
                    <div id="header1">
                        <center>
                        <b>
                        <br>
                        <h3>E-BUYAD</h3>
                        Caloocan City<br>
                        09755128084<br>
                        ebuyad@Gmail.com<br>
                        <br>
                        E-Buyad Cash Deposit Report
                        <br>
                        </b></center>
                    </div>
                    <div id="notheader">
                        <div id="header2">
                            <br><b>CREATED BY:</b> Luis Guballo
                            <br><b>DATE: </b> '.date("F j, Y, g:i a").'
                            <br>
                            <br>
                            <br><b>TOTAL CASH DEPOSITS: Php '.strval(number_format($total,2,'.',',')).' </b>
                            <br>
                            <br><br>
                        </div>
                        <div id="details">
                            <table width="100%"">
                                <col width="150px">
                                <col width="150px">
                                <tr>
                                    <th align="center"> DATE </th>
                                    <th align="center"> AMOUNT </th>
                                </tr>
                                '.$strtbl.'
                            </table>
                        </div>
                    </div>
                </body>
                </html>');

        return $pdf->stream();
    }

    public function quarterlyReportPDF(){
        $pdf = App::make('dompdf'); 

        $datestart = Input::get('quarterlydate');
        $nextdate = date('Y-m-d', strtotime($datestart.' + 3 months')).' 00:00:00';

        $res = DB::select(
                            'SELECT 
                                DATE(dtmMCCChanged) as \'Dates\', 
                                SUM(decMCCValue) as \'Total\'
                            FROM tblMemCreditChange 
                            WHERE intMCCType = 0 
                            AND DATE(dtmMCCChanged) BETWEEN ? AND ?
                            GROUP BY DATE(dtmMCCChanged) 
                            ORDER BY DATE(dtmMCCChanged) ASC',
                            [
                               $datestart,
                               $nextdate
                            ]);
        $total = 0;
        $strtbl = "";
        foreach($res as $data){
            $strtbl = $strtbl.'<tr>
                                    <td align="center"> '.$data->Dates.' </td>
                                    <td align="center"> '.strval(number_format($data->Total,2,'.',',')).' </td>
                                </tr>';
            $total = $total + $data->Total;
        }

        def("DOMPDF_ENABLE_REMOTE", false);
        $pdf->loadHTML('<html>
                <head>
                </head>
                <style type="text/css">
                    body{
                        font-family: "Monospace";
                    }
                    #notheader{
                        margin-left: 10px;
                        margin-right: 10px;
                    }
                </style>
                <body>
                    <div id="header1">
                        <center>
                        <img src="{{URL::to(\'/images/logo.png\')}}" style="height: 5%; width:5%">
                        <b>
                        <br>
                        <h3>E-BUYAD</h3>
                        Caloocan City<br>
                        09755128084<br>
                        ebuyad@Gmail.com<br>
                        <br>
                        E-Buyad Cash Deposit Report
                        <br>
                        </b></center>
                    </div>
                    <div id="notheader">
                        <div id="header2">
                            <br><b>CREATED BY:</b> Luis Guballo
                            <br><b>DATE: </b> '.date("F j, Y, g:i a").'
                            <br>
                            <br>
                            <br><b>TOTAL CASH DEPOSITS: Php '.strval(number_format($total,2,'.',',')).' </b>
                            <br>
                            <br><br>
                        </div>
                        <div id="details">
                            <table width="100%"">
                                <col width="150px">
                                <col width="150px">
                                <tr>
                                    <th align="center"> DATE </th>
                                    <th align="center"> AMOUNT </th>
                                </tr>
                                '.$strtbl.'
                            </table>
                        </div>
                    </div>
                </body>
                </html>');

        return $pdf->stream();
    }

    public function yearlyReportPDF(){
        $pdf = App::make('dompdf'); 


        $firstdate = Input::get('yearlydate');
        $nextdate = date('Y-m-d',strtotime($firstdate.' + 1 year')).' 00:00:00';

        $res = DB::select(
                            'SELECT 
                                DATE(dtmMCCChanged) as \'Dates\', 
                                SUM(decMCCValue) as \'Total\'
                            FROM tblMemCreditChange 
                            WHERE intMCCType = 0 
                            AND DATE(dtmMCCChanged) BETWEEN ? AND ?
                            GROUP BY DATE(dtmMCCChanged) 
                            ORDER BY DATE(dtmMCCChanged) ASC',
                            [
                               $firstdate,
                               $nextdate
                            ]);
        $total = 0;
        $strtbl = "";
        foreach($res as $data){
            $strtbl = $strtbl.'<tr>
                                    <td align="center"> '.$data->Dates.' </td>
                                    <td align="center"> '.strval(number_format($data->Total,2,'.',',')).' </td>
                                </tr>';
            $total = $total + $data->Total;
        }

        def("DOMPDF_ENABLE_REMOTE", false);
        $pdf->loadHTML('<html>
                <head>
                </head>
                <style type="text/css">
                    body{
                        font-family: "Monospace";
                    }
                    #notheader{
                        margin-left: 10px;
                        margin-right: 10px;
                    }
                </style>
                <body>
                    <div id="header1">
                        <center>
                        <img src="{{URL::to(\'/images/logo.png\')}}" style="height: 5%; width:5%">
                        <b>
                        <br>
                        <h3>E-BUYAD</h3>
                        Caloocan City<br>
                        09755128084<br>
                        ebuyad@Gmail.com<br>
                        <br>
                        E-Buyad Cash Deposit Report
                        <br>
                        </b></center>
                    </div>
                    <div id="notheader">
                        <div id="header2">
                            <br><b>CREATED BY:</b> Luis Guballo
                            <br><b>DATE: </b> '.date("F j, Y, g:i a").'
                            <br>
                            <br>
                            <br><b>TOTAL CASH DEPOSITS: Php '.strval(number_format($total,2,'.',',')).' </b>
                            <br>
                            <br><br>
                        </div>
                        <div id="details">
                            <table width="100%"">
                                <col width="150px">
                                <col width="150px">
                                <tr>
                                    <th align="center"> DATE </th>
                                    <th align="center"> AMOUNT </th>
                                </tr>
                                '.$strtbl.'
                            </table>
                        </div>
                    </div>
                </body>
                </html>');

        return $pdf->stream();
    }

    public function customReportPDF(){
        $pdf = App::make('dompdf'); 


        $res = DB::select(
                            'SELECT 
                                DATE(dtmMCCChanged) as \'Dates\', 
                                SUM(decMCCValue) as \'Total\'
                            FROM tblMemCreditChange 
                            WHERE intMCCType = 0 
                            AND DATE(dtmMCCChanged) BETWEEN ? AND ?
                            GROUP BY DATE(dtmMCCChanged) 
                            ORDER BY DATE(dtmMCCChanged) ASC',
                            [
                               Input::get('customdate1'),
                               Input::get('customdate2')
                            ]);
        $total = 0;
        $strtbl = "";
        foreach($res as $data){
            $strtbl = $strtbl.'<tr>
                                    <td align="center"> '.$data->Dates.' </td>
                                    <td align="center"> '.strval(number_format($data->Total,2,'.',',')).' </td>
                                </tr>';
            $total = $total + $data->Total;
        }

        def("DOMPDF_ENABLE_REMOTE", false);

        $pdf->loadHTML('<html>
                <head>
                </head>
                <style type="text/css">
                    body{
                        font-family: "Monospace";
                    }
                    #notheader{
                        margin-left: 10px;
                        margin-right: 10px;
                    }
                </style>
                <body>
                    <div id="header1">
                        <center>
                        <img src="{{URL::to(\'/images/logo.png\')}}" style="height: 5%; width:5%">
                        <b>
                        <br>
                        <h3>E-BUYAD</h3>
                        Caloocan City<br>
                        09755128084<br>
                        ebuyad@Gmail.com<br>
                        <br>
                        E-Buyad Cash Deposit Report
                        <br>
                        </b></center>
                    </div>
                    <div id="notheader">
                        <div id="header2">
                            <br><b>CREATED BY:</b> Luis Guballo
                            <br><b>DATE: </b> '.date("F j, Y, g:i a").'
                            <br>
                            <br>
                            <br><b>TOTAL CASH DEPOSITS: Php '.strval(number_format($total,2,'.',',')).' </b>
                            <br>
                            <br><br>
                        </div>
                        <div id="details">
                            <table width="100%"">
                                <col width="150px">
                                <col width="150px">
                                <tr>
                                    <th align="center"> DATE </th>
                                    <th align="center"> AMOUNT </th>
                                </tr>
                                '.$strtbl.'
                            </table>
                        </div>
                    </div>
                </body>
                </html>');

        return $pdf->stream();
    }

    public function dailySales(){
        $res = DB::select(
                        'SELECT 
                            t.strTransId as \'Transaction\',
                            t.strTransDiscCode,
                            t.decTransDiscAmount as \'Discount\',
                            SUM(
                                computeProductTotal(
                                    td.intQty,
                                    td.intPcOrPack,
                                    (SELECT p.decProdPricePerPiece 
                                     FROM tblProdPrice p
                                     WHERE p.strProdPriceCode = td.strTDProdCode
                                     AND p.dtmUpdated < t.dtmTransDate 
                                     ORDER BY p.dtmUpdated DESC LIMIT 1),
                                    (SELECT p.decPricePerPackage
                                     FROM tblProdPrice p
                                     WHERE p.strProdPriceCode = td.strTDProdCode
                                     AND p.dtmUpdated < t.dtmTransDate 
                                     ORDER BY p.dtmUpdated DESC LIMIT 1)
                                )
                            ) as \'Total\',
                            (
                                SELECT tx.rate FROM tblTax tx
                                WHERE tx.datetime < t.dtmTransDate 
                                ORDER BY tx.datetime DESC LIMIT 1
                            ) as \'TAX\'
                            
                        FROM tblTransaction t
                        LEFT JOIN tblTransDetails td
                            ON t.strTransId = td.strTDTransCode
                       WHERE DATE(t.dtmTransDate) = ?
                        GROUP BY t.strTransId
                        ORDER BY t.strTransId ASC',
                            [
                                Input::get('repdate')
                            ]);

        return Response::json($res);
    }

    public function dailySReportPDF(){
        $pdf = App::make('dompdf'); 

        $res = DB::select(
                            'SELECT 
                                t.strTransId as \'Transaction\',
                                t.strTransDiscCode,
                                t.decTransDiscAmount as \'Discount\',
                                SUM(
                                    computeProductTotal(
                                        td.intQty,
                                        td.intPcOrPack,
                                        (SELECT p.decProdPricePerPiece 
                                         FROM tblProdPrice p
                                         WHERE p.strProdPriceCode = td.strTDProdCode
                                         AND p.dtmUpdated < t.dtmTransDate 
                                         ORDER BY p.dtmUpdated DESC LIMIT 1),
                                        (SELECT p.decPricePerPackage
                                         FROM tblProdPrice p
                                         WHERE p.strProdPriceCode = td.strTDProdCode
                                         AND p.dtmUpdated < t.dtmTransDate 
                                         ORDER BY p.dtmUpdated DESC LIMIT 1)
                                    )
                                ) as \'Total\',
                                (
                                    SELECT tx.rate FROM tblTax tx
                                    WHERE tx.datetime < t.dtmTransDate 
                                    ORDER BY tx.datetime DESC LIMIT 1
                                ) as \'TAX\'
                                
                            FROM tblTransaction t
                            LEFT JOIN tblTransDetails td
                                ON t.strTransId = td.strTDTransCode
                           WHERE DATE(t.dtmTransDate) = ?
                            GROUP BY t.strTransId
                            ORDER BY t.strTransId ASC',
                                [
                                   Input::get('dailydate')
                                ]);
        $total = 0;
        $strtbl = "";

            foreach($res as $data){
            
            if($data->strTransDiscCode == 'DSC00001'){
                $vatable = 0;
                $vatex = $data->Total - ($data->Total * $data->TAX);
                $totals = floatval($vatex) -  $data->Discount;
            }else{
                $vatable = $data->Total - ($data->Total * $data->TAX);
                $vatex = 0;
                $totals = $data->Total -  $data->Discount;
            }

            $strtbl = $strtbl.'<tr>
                                    <td align="center"> '.$data->Transaction.' </td>
                                    <td align="center"> '.$data->Total.' </td>
                                    <td align="center"> '.$data->TAX.' </td>
                                    <td align="center"> '.$vatable.' </td>
                                    <td align="center"> '.$vatex.' </td>
                                    <td align="center"> '.$data->Discount.' </td>
                                    <td align="center"> '.strval(number_format($totals,2,'.',',')).' </td>
                                </tr>';

            $total = $total + $totals;
        }


        $pdf->loadHTML('<html>
                <head>
                </head>
                <style type="text/css">
                    body{
                        font-family: "Monospace";
                    }
                    #notheader{
                        margin-left: 10px;
                        margin-right: 10px;
                    }
                </style>
                <body>
                    <div id="header1">
                        <center><b>
                        <br>
                        <h3>E-BUYAD</h3>
                        Caloocan City<br>
                        09755128084<br>
                        ebuyad@Gmail.com<br>
                        <br>
                        E-Buyad Cash Deposit Report
                        <br>
                        </b></center>
                    </div>
                    <div id="notheader">
                        <div id="header2">
                            <br><b>DATE: </b> '.date("F j, Y, g:i a").'
                            <br>
                            <br>
                            <br><b>TOTAL CASH DEPOSITS: Php '.strval(number_format($total,2,'.',',')).' </b>
                            <br>
                            <br><br>
                        </div>
                        <div id="details">
                            <table width="100%"">
                                <col width="150px">
                                <col width="150px">
                                <tr>
                                    <th align="center"> TRANSACTION </th>
                                    <th align="center"> GROSS AMOUNT </th>
                                    <th align="center"> VAT </th>
                                    <th align="center"> VATABLE </th>
                                    <th align="center"> VAT Exempt </th>
                                    <th align="center"> DISCOUNT </th>
                                    <th align="center"> TOTAL SALES </th>
                                </tr>
                                '.$strtbl.'
                            </table>
                        </div>
                    </div>
                </body>
                </html>');

        return $pdf->stream();
    }

    public function weeklySales(){
        $lastdate = date('Y-m-d',strtotime(Input::get('repdate').' + 1 day'));
        $firstdate = date('Y-m-d',strtotime(Input::get('repdate').' - 8 days'));

        $res = DB::select(
                        'SELECT 
                            t.strTransId as \'Transaction\',
                            t.strTransDiscCode,
                            t.decTransDiscAmount as \'Discount\',
                            SUM(
                                computeProductTotal(
                                    td.intQty,
                                    td.intPcOrPack,
                                    (SELECT p.decProdPricePerPiece 
                                     FROM tblProdPrice p
                                     WHERE p.strProdPriceCode = td.strTDProdCode
                                     AND p.dtmUpdated < t.dtmTransDate 
                                     ORDER BY p.dtmUpdated DESC LIMIT 1),
                                    (SELECT p.decPricePerPackage
                                     FROM tblProdPrice p
                                     WHERE p.strProdPriceCode = td.strTDProdCode
                                     AND p.dtmUpdated < t.dtmTransDate 
                                     ORDER BY p.dtmUpdated DESC LIMIT 1)
                                )
                            ) as \'Total\',
                            (
                                SELECT tx.rate FROM tblTax tx
                                WHERE tx.datetime < t.dtmTransDate 
                                ORDER BY tx.datetime DESC LIMIT 1
                            ) as \'TAX\'
                            
                        FROM tblTransaction t
                        LEFT JOIN tblTransDetails td
                            ON t.strTransId = td.strTDTransCode
                       WHERE DATE(t.dtmTransDate) BETWEEN ? AND ?
                        GROUP BY t.strTransId
                        ORDER BY t.strTransId ASC',
                            [
                                $firstdate,
                                $lastdate
                            ]);
        return Response::json($res);
    }

    public function monthlySales(){
        $month = Input::get('month');
        $year = Input::get('year');

        $datestart = $year.'-'.$month.'-01 00:00:00';
        $nextdate = date('Y-m-d', strtotime($datestart.' + 1 month')).' 00:00:00';

        $res = DB::select(
                       'SELECT 
                            t.strTransId as \'Transaction\',
                            t.strTransDiscCode,
                            t.decTransDiscAmount as \'Discount\',
                            SUM(
                                computeProductTotal(
                                    td.intQty,
                                    td.intPcOrPack,
                                    (SELECT p.decProdPricePerPiece 
                                     FROM tblProdPrice p
                                     WHERE p.strProdPriceCode = td.strTDProdCode
                                     AND p.dtmUpdated < t.dtmTransDate 
                                     ORDER BY p.dtmUpdated DESC LIMIT 1),
                                    (SELECT p.decPricePerPackage
                                     FROM tblProdPrice p
                                     WHERE p.strProdPriceCode = td.strTDProdCode
                                     AND p.dtmUpdated < t.dtmTransDate 
                                     ORDER BY p.dtmUpdated DESC LIMIT 1)
                                )
                            ) as \'Total\',
                            (
                                SELECT tx.rate FROM tblTax tx
                                WHERE tx.datetime < t.dtmTransDate 
                                ORDER BY tx.datetime DESC LIMIT 1
                            ) as \'TAX\'
                            
                        FROM tblTransaction t
                        LEFT JOIN tblTransDetails td
                            ON t.strTransId = td.strTDTransCode
                       WHERE t.dtmTransDate BETWEEN ? AND ?
                        GROUP BY t.strTransId
                        ORDER BY t.strTransId ASC',
                            [
                                $datestart,
                                $nextdate
                            ]);
        return Response::json($res);
    }

    public function quarterlySales(){
        $month = Input::get('month');
        $year = Input::get('year');

        $datestart = $year.'-'.$month.'-01 00:00:00';
        $nextdate = date('Y-m-d', strtotime($datestart.' + 3 months')).' 00:00:00';

        $res = DB::select(
                        'SELECT 
                            t.strTransId as \'Transaction\',
                            t.strTransDiscCode,
                            t.decTransDiscAmount as \'Discount\',
                            SUM(
                                computeProductTotal(
                                    td.intQty,
                                    td.intPcOrPack,
                                    (SELECT p.decProdPricePerPiece 
                                     FROM tblProdPrice p
                                     WHERE p.strProdPriceCode = td.strTDProdCode
                                     AND p.dtmUpdated < t.dtmTransDate 
                                     ORDER BY p.dtmUpdated DESC LIMIT 1),
                                    (SELECT p.decPricePerPackage
                                     FROM tblProdPrice p
                                     WHERE p.strProdPriceCode = td.strTDProdCode
                                     AND p.dtmUpdated < t.dtmTransDate 
                                     ORDER BY p.dtmUpdated DESC LIMIT 1)
                                )
                            ) as \'Total\',
                            (
                                SELECT tx.rate FROM tblTax tx
                                WHERE tx.datetime < t.dtmTransDate 
                                ORDER BY tx.datetime DESC LIMIT 1
                            ) as \'TAX\'
                            
                        FROM tblTransaction t
                        LEFT JOIN tblTransDetails td
                            ON t.strTransId = td.strTDTransCode
                       WHERE t.dtmTransDate BETWEEN ? AND ?
                        GROUP BY t.strTransId
                        ORDER BY t.strTransId ASC',
                            [
                                $datestart,
                                $nextdate
                            ]);

        return Response::json($res)
        ;
    }

    public function yearlySales(){
        $firstdate = Input::get('year');
        $nextdate = strval(intval(Input::get('year')) + 1);

        $firstdate = $firstdate.'-01-01 00:00:00';
        $nextdate = $nextdate.'-01-01 00:00:00';

        $res = DB::select(
                        'SELECT 
                            t.strTransId as \'Transaction\',
                            t.strTransDiscCode,
                            t.decTransDiscAmount as \'Discount\',
                            SUM(
                                computeProductTotal(
                                    td.intQty,
                                    td.intPcOrPack,
                                    (SELECT p.decProdPricePerPiece 
                                     FROM tblProdPrice p
                                     WHERE p.strProdPriceCode = td.strTDProdCode
                                     AND p.dtmUpdated < t.dtmTransDate 
                                     ORDER BY p.dtmUpdated DESC LIMIT 1),
                                    (SELECT p.decPricePerPackage
                                     FROM tblProdPrice p
                                     WHERE p.strProdPriceCode = td.strTDProdCode
                                     AND p.dtmUpdated < t.dtmTransDate 
                                     ORDER BY p.dtmUpdated DESC LIMIT 1)
                                )
                            ) as \'Total\',
                            (
                                SELECT tx.rate FROM tblTax tx
                                WHERE tx.datetime < t.dtmTransDate 
                                ORDER BY tx.datetime DESC LIMIT 1
                            ) as \'TAX\'
                            
                        FROM tblTransaction t
                        LEFT JOIN tblTransDetails td
                            ON t.strTransId = td.strTDTransCode
                       WHERE t.dtmTransDate BETWEEN ? AND ?
                        GROUP BY t.strTransId
                        ORDER BY t.strTransId ASC',
                            [
                                $firstdate,
                                $nextdate
                            ]);
        return Response::json($res);
    }

    public function customSales(){
        $firstdate = Input::get('dtfir');
        $nextdate = date('Y-m-d',strtotime(Input::get('dtnex').' + 1 day')).' 00:00:00';

        $res = DB::select(
                        'SELECT 
                            t.strTransId as \'Transaction\',
                            t.strTransDiscCode,
                            t.decTransDiscAmount as \'Discount\',
                            SUM(
                                computeProductTotal(
                                    td.intQty,
                                    td.intPcOrPack,
                                    (SELECT p.decProdPricePerPiece 
                                     FROM tblProdPrice p
                                     WHERE p.strProdPriceCode = td.strTDProdCode
                                     AND p.dtmUpdated < t.dtmTransDate 
                                     ORDER BY p.dtmUpdated DESC LIMIT 1),
                                    (SELECT p.decPricePerPackage
                                     FROM tblProdPrice p
                                     WHERE p.strProdPriceCode = td.strTDProdCode
                                     AND p.dtmUpdated < t.dtmTransDate 
                                     ORDER BY p.dtmUpdated DESC LIMIT 1)
                                )
                            ) as \'Total\',
                            (
                                SELECT tx.rate FROM tblTax tx
                                WHERE tx.datetime < t.dtmTransDate 
                                ORDER BY tx.datetime DESC LIMIT 1
                            ) as \'TAX\'
                            
                        FROM tblTransaction t
                        LEFT JOIN tblTransDetails td
                            ON t.strTransId = td.strTDTransCode
                       WHERE t.dtmTransDate BETWEEN ? AND ?
                        GROUP BY t.strTransId
                        ORDER BY t.strTransId ASC',
                            [
                                $firstdate,
                                $nextdate
                            ]);
        return Response::json($res);
    }

    public function weeklySReportPDF(){
        $pdf = App::make('dompdf'); 

        $lastdate = date('Y-m-d',strtotime(Input::get('weeklydate').' + 1 day'));
        $firstdate = date('Y-m-d',strtotime(Input::get('weeklydate').' - 8 days'));

        $res = DB::select(
                            'SELECT 
                                t.strTransId as \'Transaction\',
                                t.strTransDiscCode,
                                t.decTransDiscAmount as \'Discount\',
                                SUM(
                                    computeProductTotal(
                                        td.intQty,
                                        td.intPcOrPack,
                                        (SELECT p.decProdPricePerPiece 
                                         FROM tblProdPrice p
                                         WHERE p.strProdPriceCode = td.strTDProdCode
                                         AND p.dtmUpdated < t.dtmTransDate 
                                         ORDER BY p.dtmUpdated DESC LIMIT 1),
                                        (SELECT p.decPricePerPackage
                                         FROM tblProdPrice p
                                         WHERE p.strProdPriceCode = td.strTDProdCode
                                         AND p.dtmUpdated < t.dtmTransDate 
                                         ORDER BY p.dtmUpdated DESC LIMIT 1)
                                    )
                                ) as \'Total\',
                                (
                                    SELECT tx.rate FROM tblTax tx
                                    WHERE tx.datetime < t.dtmTransDate 
                                    ORDER BY tx.datetime DESC LIMIT 1
                                ) as \'TAX\'
                                
                            FROM tblTransaction t
                            LEFT JOIN tblTransDetails td
                                ON t.strTransId = td.strTDTransCode
                           WHERE DATE(t.dtmTransDate) BETWEEN ? AND ?
                            GROUP BY t.strTransId
                            ORDER BY t.strTransId ASC',
                            [
                               $firstdate,
                               $lastdate
                            ]);
        $total = 0;
        $strtbl = "";

            foreach($res as $data){
            
            if($data->strTransDiscCode == 'DSC00001'){
                $vatable = 0;
                $vatex = $data->Total - ($data->Total * $data->TAX);
                $totals = floatval($vatex) -  $data->Discount;
            }else{
                $vatable = $data->Total - ($data->Total * $data->TAX);
                $vatex = 0;
                $totals = $data->Total -  $data->Discount;
            }

            $strtbl = $strtbl.'<tr>
                                    <td align="center"> '.$data->Transaction.' </td>
                                    <td align="center"> '.$data->Total.' </td>
                                    <td align="center"> '.$data->TAX.' </td>
                                    <td align="center"> '.$vatable.' </td>
                                    <td align="center"> '.$vatex.' </td>
                                    <td align="center"> '.$data->Discount.' </td>
                                    <td align="center"> '.strval(number_format($totals,2,'.',',')).' </td>
                                </tr>';

            $total = $total + $totals;
        }

        def("DOMPDF_ENABLE_REMOTE", false);
        $pdf->loadHTML('<html>
                <head>
                </head>
                <style type="text/css">
                    body{
                        font-family: "Monospace";
                    }
                    #notheader{
                        margin-left: 10px;
                        margin-right: 10px;
                    }
                </style>
                <body>
                    <div id="header1">
                        <center>
                        <img src="{{URL::to(\'/images/logo.png\')}}" style="height: 5%; width:5%">
                        <b>
                        <br>
                        <h3>E-BUYAD</h3>
                        Caloocan City<br>
                        09755128084<br>
                        ebuyad@Gmail.com<br>
                        <br>
                        E-Buyad Cash Deposit Report
                        <br>
                        </b></center>
                    </div>
                    <div id="notheader">
                        <div id="header2">
                            <br><b>CREATED BY:</b> Luis Guballo
                            <br><b>DATE: </b> '.date("F j, Y, g:i a").'
                            <br>
                            <br>
                            <br><b>TOTAL CASH DEPOSITS: Php '.strval(number_format($total,2,'.',',')).' </b>
                            <br>
                            <br><br>
                        </div>
                        <div id="details">
                            <table width="100%"">
                                <col width="150px">
                                <col width="150px">
                                <tr>
                                    <th align="center"> TRANSACTION </th>
                                    <th align="center"> GROSS AMOUNT </th>
                                    <th align="center"> VAT </th>
                                    <th align="center"> VATABLE </th>
                                    <th align="center"> VAT Exempt </th>
                                    <th align="center"> DISCOUNT </th>
                                    <th align="center"> TOTAL SALES </th>
                                </tr>
                                '.$strtbl.'
                            </table>
                        </div>
                    </div>
                </body>
                </html>');

        return $pdf->stream();
    }

    public function monthlySReportPDF(){
        $pdf = App::make('dompdf'); 

        $datestart = Input::get("monthlydate");
        $nextdate = date('Y-m-d', strtotime($datestart.' + 1 month')).' 00:00:00';

        $res = DB::select(
                            'SELECT 
                                t.strTransId as \'Transaction\',
                                t.strTransDiscCode,
                                t.decTransDiscAmount as \'Discount\',
                                SUM(
                                    computeProductTotal(
                                        td.intQty,
                                        td.intPcOrPack,
                                        (SELECT p.decProdPricePerPiece 
                                         FROM tblProdPrice p
                                         WHERE p.strProdPriceCode = td.strTDProdCode
                                         AND p.dtmUpdated < t.dtmTransDate 
                                         ORDER BY p.dtmUpdated DESC LIMIT 1),
                                        (SELECT p.decPricePerPackage
                                         FROM tblProdPrice p
                                         WHERE p.strProdPriceCode = td.strTDProdCode
                                         AND p.dtmUpdated < t.dtmTransDate 
                                         ORDER BY p.dtmUpdated DESC LIMIT 1)
                                    )
                                ) as \'Total\',
                                (
                                    SELECT tx.rate FROM tblTax tx
                                    WHERE tx.datetime < t.dtmTransDate 
                                    ORDER BY tx.datetime DESC LIMIT 1
                                ) as \'TAX\'
                                
                            FROM tblTransaction t
                            LEFT JOIN tblTransDetails td
                                ON t.strTransId = td.strTDTransCode
                           WHERE t.dtmTransDate BETWEEN ? AND ?
                            GROUP BY t.strTransId
                            ORDER BY t.strTransId ASC',
                            [
                               $datestart,
                               $nextdate
                            ]);
        $total = 0;
        $strtbl = "";

            foreach($res as $data){
            
            if($data->strTransDiscCode == 'DSC00001'){
                $vatable = 0;
                $vatex = $data->Total - ($data->Total * $data->TAX);
                $totals = floatval($vatex) -  $data->Discount;
            }else{
                $vatable = $data->Total - ($data->Total * $data->TAX);
                $vatex = 0;
                $totals = $data->Total -  $data->Discount;
            }

            $strtbl = $strtbl.'<tr>
                                    <td align="center"> '.$data->Transaction.' </td>
                                    <td align="center"> '.$data->Total.' </td>
                                    <td align="center"> '.$data->TAX.' </td>
                                    <td align="center"> '.$vatable.' </td>
                                    <td align="center"> '.$vatex.' </td>
                                    <td align="center"> '.$data->Discount.' </td>
                                    <td align="center"> '.strval(number_format($totals,2,'.',',')).' </td>
                                </tr>';

            $total = $total + $totals;
        }

        def("DOMPDF_ENABLE_REMOTE", false);
        $pdf->loadHTML('<html>
                <head>
                </head>
                <style type="text/css">
                    body{
                        font-family: "Monospace";
                    }
                    #notheader{
                        margin-left: 10px;
                        margin-right: 10px;
                    }
                </style>
                <body>
                    <div id="header1">
                        <center>
                        <b>
                        <br>
                        <h3>E-BUYAD</h3>
                        Caloocan City<br>
                        09755128084<br>
                        ebuyad@Gmail.com<br>
                        <br>
                        E-Buyad Cash Deposit Report
                        <br>
                        </b></center>
                    </div>
                    <div id="notheader">
                        <div id="header2">
                            <br><b>DATE: </b> '.date("F j, Y, g:i a").'
                            <br>
                            <br>
                            <br><b>TOTAL CASH DEPOSITS: Php '.strval(number_format($total,2,'.',',')).' </b>
                            <br>
                            <br><br>
                        </div>
                        <div id="details">
                            <table width="100%"">
                                <col width="150px">
                                <col width="150px">
                                <tr>
                                    <th align="center"> TRANSACTION </th>
                                    <th align="center"> GROSS AMOUNT </th>
                                    <th align="center"> VAT </th>
                                    <th align="center"> VATABLE </th>
                                    <th align="center"> VAT Exempt </th>
                                    <th align="center"> DISCOUNT </th>
                                    <th align="center"> TOTAL SALES </th>
                                </tr>
                                '.$strtbl.'
                            </table>
                        </div>
                    </div>
                </body>
                </html>');

        return $pdf->stream();
    }

    public function quarterlySReportPDF(){
        $pdf = App::make('dompdf'); 

        $datestart = Input::get('quarterlydate');
        $nextdate = date('Y-m-d', strtotime($datestart.' + 3 months')).' 00:00:00';

        $res = DB::select(
                            'SELECT 
                                t.strTransId as \'Transaction\',
                                t.strTransDiscCode,
                                t.decTransDiscAmount as \'Discount\',
                                SUM(
                                    computeProductTotal(
                                        td.intQty,
                                        td.intPcOrPack,
                                        (SELECT p.decProdPricePerPiece 
                                         FROM tblProdPrice p
                                         WHERE p.strProdPriceCode = td.strTDProdCode
                                         AND p.dtmUpdated < t.dtmTransDate 
                                         ORDER BY p.dtmUpdated DESC LIMIT 1),
                                        (SELECT p.decPricePerPackage
                                         FROM tblProdPrice p
                                         WHERE p.strProdPriceCode = td.strTDProdCode
                                         AND p.dtmUpdated < t.dtmTransDate 
                                         ORDER BY p.dtmUpdated DESC LIMIT 1)
                                    )
                                ) as \'Total\',
                                (
                                    SELECT tx.rate FROM tblTax tx
                                    WHERE tx.datetime < t.dtmTransDate 
                                    ORDER BY tx.datetime DESC LIMIT 1
                                ) as \'TAX\'
                                
                            FROM tblTransaction t
                            LEFT JOIN tblTransDetails td
                                ON t.strTransId = td.strTDTransCode
                           WHERE t.dtmTransDate BETWEEN ? AND ?
                            GROUP BY t.strTransId
                            ORDER BY t.strTransId ASC',
                            [
                               $datestart,
                               $nextdate
                            ]);
        $total = 0;
        $strtbl = "";

            foreach($res as $data){
            
            if($data->strTransDiscCode == 'DSC00001'){
                $vatable = 0;
                $vatex = $data->Total - ($data->Total * $data->TAX);
                $totals = floatval($vatex) -  $data->Discount;
            }else{
                $vatable = $data->Total - ($data->Total * $data->TAX);
                $vatex = 0;
                $totals = $data->Total -  $data->Discount;
            }

            $strtbl = $strtbl.'<tr>
                                    <td align="center"> '.$data->Transaction.' </td>
                                    <td align="center"> '.$data->Total.' </td>
                                    <td align="center"> '.$data->TAX.' </td>
                                    <td align="center"> '.$vatable.' </td>
                                    <td align="center"> '.$vatex.' </td>
                                    <td align="center"> '.$data->Discount.' </td>
                                    <td align="center"> '.strval(number_format($totals,2,'.',',')).' </td>
                                </tr>';

            $total = $total + $totals;
        }

        def("DOMPDF_ENABLE_REMOTE", false);
        $pdf->loadHTML('<html>
                <head>
                </head>
                <style type="text/css">
                    body{
                        font-family: "Monospace";
                    }
                    #notheader{
                        margin-left: 10px;
                        margin-right: 10px;
                    }
                </style>
                <body>
                    <div id="header1">
                        <center>
                        <img src="{{URL::to(\'/images/logo.png\')}}" style="height: 5%; width:5%">
                        <b>
                        <br>
                        <h3>E-BUYAD</h3>
                        Caloocan City<br>
                        09755128084<br>
                        ebuyad@Gmail.com<br>
                        <br>
                        E-Buyad Cash Deposit Report
                        <br>
                        </b></center>
                    </div>
                    <div id="notheader">
                        <div id="header2">
                            <br><b>CREATED BY:</b> Luis Guballo
                            <br><b>DATE: </b> '.date("F j, Y, g:i a").'
                            <br>
                            <br>
                            <br><b>TOTAL CASH DEPOSITS: Php '.strval(number_format($total,2,'.',',')).' </b>
                            <br>
                            <br><br>
                        </div>
                        <div id="details">
                            <table width="100%"">
                                <col width="150px">
                                <col width="150px">
                                <tr>
                                    <th align="center"> TRANSACTION </th>
                                    <th align="center"> GROSS AMOUNT </th>
                                    <th align="center"> VAT </th>
                                    <th align="center"> VATABLE </th>
                                    <th align="center"> VAT Exempt </th>
                                    <th align="center"> DISCOUNT </th>
                                    <th align="center"> TOTAL SALES </th>
                                </tr>
                                '.$strtbl.'
                            </table>
                        </div>
                    </div>
                </body>
                </html>');

        return $pdf->stream();
    }

    public function yearlySReportPDF(){
        $pdf = App::make('dompdf'); 


        $firstdate = Input::get('yearlydate');
        $nextdate = date('Y-m-d',strtotime($firstdate.' + 1 year')).' 00:00:00';

        $res = DB::select(
                            'SELECT 
                                t.strTransId as \'Transaction\',
                                t.strTransDiscCode,
                                t.decTransDiscAmount as \'Discount\',
                                SUM(
                                    computeProductTotal(
                                        td.intQty,
                                        td.intPcOrPack,
                                        (SELECT p.decProdPricePerPiece 
                                         FROM tblProdPrice p
                                         WHERE p.strProdPriceCode = td.strTDProdCode
                                         AND p.dtmUpdated < t.dtmTransDate 
                                         ORDER BY p.dtmUpdated DESC LIMIT 1),
                                        (SELECT p.decPricePerPackage
                                         FROM tblProdPrice p
                                         WHERE p.strProdPriceCode = td.strTDProdCode
                                         AND p.dtmUpdated < t.dtmTransDate 
                                         ORDER BY p.dtmUpdated DESC LIMIT 1)
                                    )
                                ) as \'Total\',
                                (
                                    SELECT tx.rate FROM tblTax tx
                                    WHERE tx.datetime < t.dtmTransDate 
                                    ORDER BY tx.datetime DESC LIMIT 1
                                ) as \'TAX\'
                                
                            FROM tblTransaction t
                            LEFT JOIN tblTransDetails td
                                ON t.strTransId = td.strTDTransCode
                           WHERE t.dtmTransDate BETWEEN ? AND ?
                            GROUP BY t.strTransId
                            ORDER BY t.strTransId ASC',
                            [
                               $firstdate,
                               $nextdate
                            ]);
        $total = 0;
        $strtbl = "";

            foreach($res as $data){
            
            if($data->strTransDiscCode == 'DSC00001'){
                $vatable = 0;
                $vatex = $data->Total - ($data->Total * $data->TAX);
                $totals = floatval($vatex) -  $data->Discount;
            }else{
                $vatable = $data->Total - ($data->Total * $data->TAX);
                $vatex = 0;
                $totals = $data->Total -  $data->Discount;
            }

            $strtbl = $strtbl.'<tr>
                                    <td align="center"> '.$data->Transaction.' </td>
                                    <td align="center"> '.$data->Total.' </td>
                                    <td align="center"> '.$data->TAX.' </td>
                                    <td align="center"> '.$vatable.' </td>
                                    <td align="center"> '.$vatex.' </td>
                                    <td align="center"> '.$data->Discount.' </td>
                                    <td align="center"> '.strval(number_format($totals,2,'.',',')).' </td>
                                </tr>';

            $total = $total + $totals;
        }

        def("DOMPDF_ENABLE_REMOTE", false);
        $pdf->loadHTML('<html>
                <head>
                </head>
                <style type="text/css">
                    body{
                        font-family: "Monospace";
                    }
                    #notheader{
                        margin-left: 10px;
                        margin-right: 10px;
                    }
                </style>
                <body>
                    <div id="header1">
                        <center>
                        <img src="{{URL::to(\'/images/logo.png\')}}" style="height: 5%; width:5%">
                        <b>
                        <br>
                        <h3>E-BUYAD</h3>
                        Caloocan City<br>
                        09755128084<br>
                        ebuyad@Gmail.com<br>
                        <br>
                        E-Buyad Cash Deposit Report
                        <br>
                        </b></center>
                    </div>
                    <div id="notheader">
                        <div id="header2">
                            <br><b>CREATED BY:</b> Luis Guballo
                            <br><b>DATE: </b> '.date("F j, Y, g:i a").'
                            <br>
                            <br>
                            <br><b>TOTAL CASH DEPOSITS: Php '.strval(number_format($total,2,'.',',')).' </b>
                            <br>
                            <br><br>
                        </div>
                        <div id="details">
                            <table width="100%"">
                                <col width="150px">
                                <col width="150px">
                                <tr>
                                    <th align="center"> TRANSACTION </th>
                                    <th align="center"> GROSS AMOUNT </th>
                                    <th align="center"> VAT </th>
                                    <th align="center"> VATABLE </th>
                                    <th align="center"> VAT Exempt </th>
                                    <th align="center"> DISCOUNT </th>
                                    <th align="center"> TOTAL SALES </th>
                                </tr>
                                '.$strtbl.'
                            </table>
                        </div>
                    </div>
                </body>
                </html>');

        return $pdf->stream();
    }

    public function customSReportPDF(){
        $pdf = App::make('dompdf'); 


        $res = DB::select(
                            'SELECT 
                                t.strTransId as \'Transaction\',
                                t.strTransDiscCode,
                                t.decTransDiscAmount as \'Discount\',
                                SUM(
                                    computeProductTotal(
                                        td.intQty,
                                        td.intPcOrPack,
                                        (SELECT p.decProdPricePerPiece 
                                         FROM tblProdPrice p
                                         WHERE p.strProdPriceCode = td.strTDProdCode
                                         AND p.dtmUpdated < t.dtmTransDate 
                                         ORDER BY p.dtmUpdated DESC LIMIT 1),
                                        (SELECT p.decPricePerPackage
                                         FROM tblProdPrice p
                                         WHERE p.strProdPriceCode = td.strTDProdCode
                                         AND p.dtmUpdated < t.dtmTransDate 
                                         ORDER BY p.dtmUpdated DESC LIMIT 1)
                                    )
                                ) as \'Total\',
                                (
                                    SELECT tx.rate FROM tblTax tx
                                    WHERE tx.datetime < t.dtmTransDate 
                                    ORDER BY tx.datetime DESC LIMIT 1
                                ) as \'TAX\'
                                
                            FROM tblTransaction t
                            LEFT JOIN tblTransDetails td
                                ON t.strTransId = td.strTDTransCode
                           WHERE DATE(t.dtmTransDate) BETWEEN ? AND ?
                            GROUP BY t.strTransId
                            ORDER BY t.strTransId ASC',
                            [
                               Input::get('customdate1'),
                               Input::get('customdate2')
                            ]);
        $total = 0;
        $strtbl = "";

            foreach($res as $data){
            
            if($data->strTransDiscCode == 'DSC00001'){
                $vatable = 0;
                $vatex = $data->Total - ($data->Total * $data->TAX);
                $totals = floatval($vatex) -  $data->Discount;
            }else{
                $vatable = $data->Total - ($data->Total * $data->TAX);
                $vatex = 0;
                $totals = $data->Total -  $data->Discount;
            }

            $strtbl = $strtbl.'<tr>
                                    <td align="center"> '.$data->Transaction.' </td>
                                    <td align="center"> '.$data->Total.' </td>
                                    <td align="center"> '.$data->TAX.' </td>
                                    <td align="center"> '.$vatable.' </td>
                                    <td align="center"> '.$vatex.' </td>
                                    <td align="center"> '.$data->Discount.' </td>
                                    <td align="center"> '.strval(number_format($totals,2,'.',',')).' </td>
                                </tr>';

            $total = $total + $totals;
        }

        def("DOMPDF_ENABLE_REMOTE", false);

        $pdf->loadHTML('<html>
                <head>
                </head>
                <style type="text/css">
                    body{
                        font-family: "Monospace";
                    }
                    #notheader{
                        margin-left: 10px;
                        margin-right: 10px;
                    }
                </style>
                <body>
                    <div id="header1">
                        <center>
                        <img src="{{URL::to(\'/images/logo.png\')}}" style="height: 5%; width:5%">
                        <b>
                        <br>
                        <h3>E-BUYAD</h3>
                        Caloocan City<br>
                        09755128084<br>
                        ebuyad@Gmail.com<br>
                        <br>
                        E-Buyad Cash Deposit Report
                        <br>
                        </b></center>
                    </div>
                    <div id="notheader">
                        <div id="header2">
                            <br><b>CREATED BY:</b> Luis Guballo
                            <br><b>DATE: </b> '.date("F j, Y, g:i a").'
                            <br>
                            <br>
                            <br><b>TOTAL CASH DEPOSITS: Php '.strval(number_format($total,2,'.',',')).' </b>
                            <br>
                            <br><br>
                        </div>
                        <div id="details">
                            <table width="100%"">
                                <col width="150px">
                                <col width="150px">
                                <tr>
                                    <th align="center"> TRANSACTION </th>
                                    <th align="center"> GROSS AMOUNT </th>
                                    <th align="center"> VAT </th>
                                    <th align="center"> VATABLE </th>
                                    <th align="center"> VAT Exempt </th>
                                    <th align="center"> DISCOUNT </th>
                                    <th align="center"> TOTAL SALES </th>
                                </tr>
                                '.$strtbl.'
                            </table>
                        </div>
                    </div>
                </body>
                </html>');

        return $pdf->stream();
    }
    public function egc()
    {
        return View::make('reports/egcreport');
    }
}

 