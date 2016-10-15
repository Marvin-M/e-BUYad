<?php

class ReloadController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

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

    public function reloadCredit(){
    	try{
    		DB::update(
    			'UPDATE tblMemCredit SET decMCreditValue = ?, dtmLastUpdate = now() WHERE strMCreditCode = ?',
    			[
    				(intval(Input::get('membal')) + intval(Input::get('amt'))),
    				Input::get('memcode')
    			]);

            DB::insert('INSERT INTO tblMemCreditChange VALUES(null,?,?,now(),?,?,?)',
                    [
                        Input::get('memcode'),
                        intval(Input::get('amt')),
                        null,
                        Cache::get('branch'),
                        0
                    ]);
    		return Redirect::to('/transaction/reload');
    	}catch(PDOException $ex){}
    }

}
