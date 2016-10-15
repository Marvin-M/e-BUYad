<?php

class QueriesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function member()
	{
		return View::make('/queries/memberquery');
	}

	public function branch()
	{
		return View::make('/queries/branchquery');
	}

	public function product()
	{
		return View::make('/queries/productquery');
	}

	public function generateMember(){
		$datFrom = Input::get('start');
		$datTo = Input::get('end'); 
		$column = Input::get('col');
		$order = Input::get('ord');

		$res = DB::select(
			'SELECT 
				m.strMemCode as \'Code\',
			    m.strMemLName as \'LName\',
			    m.strMemFName as \'FName\',
			    TIMESTAMPDIFF(YEAR,m.datMemBirthday,CURDATE()) as \'Age\',
			    COUNT(DISTINCT t.strTransId) as \'Transaction\',
			    mc.decMCreditValue as \'Balance\',
			    SUM(mp.decPointValue) as \'Points\'
			FROM tblMember m
			LEFT JOIN tblTransaction t
				ON m.strMemCode = t.strTransCustCode
			LEFT JOIN tblMemCredit mc
				ON m.strMemCode = mc.strMCreditCode
			LEFT JOIN tblMemPoints mp
				ON m.strMemCode = mp.strPointMemCode
			WHERE (t.dtmTransDate BETWEEN ? AND ? OR t.dtmTransDate IS NULL)
			AND m.intStatus = 1
			GROUP BY m.strMemCode ORDER BY '.$column.' '.$order,
			[
				$datFrom,
				$datTo,
			]
		);
		
		return Response::json($res);
	}

	public function generateBranch(){
		$datFrom = Input::get('start');
		$datTo = Input::get('end'); 
		$datTo = date('Y-m-d',strtotime($datTo.' + 1 day'));
		$column = Input::get('col');
		$order = Input::get('ord');
		
		$res = DB::select(
				'SELECT 
					b.strBranchCode as \'Code\',
				    b.strBranchName as \'Name\',
				    COUNT(t.strTransId) as \'Transactions\',
				    SUM(
				    	ComputeProductTotal(
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
				    	) - t.decTransDiscAmount
				     ) as \'Sale\'
				    
				FROM tblBranches b
				LEFT JOIN tblTransaction t
					ON b.strBranchCode = t.strTransBranCode
				LEFT JOIN tblTransDetails td
					ON t.strTransId = td.strTDTransCode
				WHERE b.intStatus = 1 AND
				(t.dtmTransDate BETWEEN ? AND ?)
				GROUP BY b.strBranchCode
				ORDER BY 
				 '.$column.' '.$order,
			[
				$datFrom,
				$datTo
			]
		);
		
		return Response::json($res);
	}
}
