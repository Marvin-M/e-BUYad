<?php

class BranchProductController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function showAddBranchProduct()
	{
		return View::make('/maintenance/branchprod/addbranprod');
	}

	public function addProductToBranch(){
		try{
			DB::insert(
				'INSERT INTO tblBranProd VALUES(?,?,?,now())',
				[
					Cache::get('branch'),
					Input::get('us_code'),
					Input::get('us_qty')
				]);

			return Redirect::to('/maintenance/branchprod/add-product')
				->with('message','Product successfully added to branch!');
		}catch(PDOException $ex){

			return Redirect::to('/maintenance/branchprod/add-product')
				->with('message','Error: Product might already be existing in the branch!');
		}
	}

	public function showEditBranchProduct()
	{
		return View::make('/maintenance/branchprod/editbranprod');
	}

	public function updateBranchProduct(){
		try{
			DB::update('UPDATE tblBranProd SET intStock = ? WHERE strBPBranCode = ? AND strBPProdCode = ?',
				[
					Input::get('qty'),
					Cache::get('branch'),
					Input::get('code')
				]);

			return Redirect::to('/maintenance/branchprod/edit-product')
				->with('message','Product successfully updated product!');
		}catch(PDOException $ex){
		 	return Redirect::to('/maintenance/branchprod/edit-product')
		 		->with('message','updating failed');
		 }
	}

	public function deleteBranchProduct(){
		try{
			DB::table('tblBranProd')
                ->where('strBPBranCode','=',Cache::get('branch'))
                ->where('strBPProdCode','=',Input::get('del_code'))
                ->delete();

			return Redirect::to('/maintenance/branchprod/edit-product')
				->with('message','Product successfully deleted product!');
		 }catch(PDOException $ex){
		 	return Redirect::to('/maintenance/branchprod/edit-product')
		 		->with('message','deleting failed');
		 }
	}
}
