<?php

class SetBranchController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function showSetBranch()
	{
		return View::make('/utilities/setbranch');
	}

	public function setBranch(){
		if(Cache::has('branch')){
			Cache::flush();
		}

		Cache::forever('branch',Input::get('branch'));
		Cache::forever('branchname',Input::get('branchname'));

		return Redirect::to('utils/set-branch')
			->with('message','Branch has been set/updated');
	}
}
