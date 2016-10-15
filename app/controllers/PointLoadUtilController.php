<?php

class PointLoadUtilController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function showPointLoad(){
		$result1 = DB::table('tblpointsetting')
						->select('PointMinimum','PointPercent')
						->where('id','=','1')
						->first();

		$result2 = DB::table('tblloadsetting')
						->select('LoadDefault','LoadMinimum')
						->where('id','=','1')
						->first();

		return View::make('utilities/pointloadsetting')
						->with('loaddef', strval($result2->LoadDefault))
						->with('loadmin', $result2->LoadMinimum)
						->with('ptperc', $result1->PointPercent)
						->with('ptmin', $result1->PointMinimum);
	}

	public function updatePointLoad(){
		DB::update(
			'UPDATE tblloadsetting 
				SET LoadDefault = ?,
					LoadMinimum = ?
			WHERE id = 1',
			[
				Input::get('defload'),
				Input::get('minload')
			]);

		DB::update(
			'UPDATE tblpointsetting 
				SET PointMinimum = ?,
					PointPercent = ?
			WHERE id = 1',
			[
				Input::get('mintotal'),
				Input::get('percpoint')
			]);

		return Redirect::to('/utils/pointload')->with('message','Mechanics Successfully Updated!');
	}
}
