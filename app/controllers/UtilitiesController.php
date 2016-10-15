<?php

class UtilitiesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function showJobAccess(){
		return View::make('utilities/jobaccess');
	}

	public function saveAccess(){
		//try{

			DB::table('tblJobAccess')
				->where('strJobId','=',Input::get('code'))->delete();

			DB::update('INSERT INTO tblJobAccess
					VALUES (?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?)',
				[
					Input::get('code'),
					Input::get('mem'),
					Input::get('sale'),
					Input::get('relo'),
					Input::get('egc'),
					Input::get('repo'),
					Input::get('query'),
					Input::get('util'),
					Input::get('maint')
				]);

			return Redirect::to('/utils/jobaccess');
		// }catch(PDOException $ex){
		// 	return Redirect::to('/utils/jobaccess');
		// }
	}

}
