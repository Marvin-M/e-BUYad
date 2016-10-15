<?php

class LoginController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		if(Session::has('user')){
			return Redirect::to('/transaction/sell');
		}else{
			return View::make('login');
		}
	}

	public function verifyLogin()
	{
		$emp = DB::table('tblEmpAcct')
                    ->select('strEAEmpCode')
                    ->where('strEAEmpCode', Input::get('username'))
                    ->where('password', Input::get('password'))
                    ->first();


        if($emp == null){
            return Redirect::to('/')
            	->with('loginmess','Wrong username and/or password!');
        }else{
        	$name = DB::table('tblEmployee')
        			->select('strEmpFName','strEmpLName')
        			->where('strEmpCode', $emp->strEAEmpCode)
        			->first();

        	$job = DB::table('tblEmpJobBranch')
        				->select('strJobCode','strBranCode')
	        			->where('strEmpCode', $emp->strEAEmpCode)
	        			->first();

	        if($job->strBranCode == Cache::get('branch') || $job->strJobCode == 'JOB00007' ){
	    		Session::put('user', $emp->strEAEmpCode);
	    		Session::put('username', $name->strEmpFName + $name->strEmpLName);
	    		Session::put('jobcode', $job->strJobCode);

				return Redirect::to('/transaction/sell');
	        }else{
	            return Redirect::to('/')
	            	->with('loginmess',$job->strJobCode);
	        }
        }
	}

	public function logout(){
		Session::flush();
		return Redirect::to('/');
	}
}
