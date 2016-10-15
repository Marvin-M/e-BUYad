<?php

class MemberReactivationController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	

	public function index()
	{
		return View::make('/utilities/reactivate/memberreactivation');
	}

	public function thera()
	{
		return View::make('/utilities/reactivate/therareactivation');
	}

	public function generic()
	{
		return View::make('/utilities/reactivate/genreactivation');
	}

	public function brand()
	{
		return View::make('/utilities/reactivate/brandreactivation');
	}

	public function manufacturer()
	{
		return View::make('/utilities/reactivate/manureactivation');
	}

	public function product()
	{
		return View::make('/utilities/reactivate/prodreactivation');
	}

	public function prodcat()
	{
		return View::make('/utilities/reactivate/prodcatreactivation');
	}

	public function proddet()
	{
		return View::make('/utilities/reactivate/proddetreactivation');
	}

	public function form()
	{
		return View::make('/utilities/reactivate/formreactivation');
	}

	public function pack()
	{
		return View::make('/utilities/reactivate/packreactivation');
	}

	public function uom()
	{
		return View::make('/utilities/reactivate/uomreactivation');
	}

	public function branch()
	{
		return View::make('/utilities/reactivate/branchreactivation');
	}

	public function job()
	{
		return View::make('/utilities/reactivate/jobreactivation');
	}

	public function emp()
	{
		return View::make('/utilities/reactivate/empreactivation');
	}

	public function discount()
	{
		return View::make('/utilities/reactivate/discountreactivation');
	}

	public function promos()
	{
		return View::make('/utilities/reactivate/promoreactivation');
	}

	public function package()
	{
		return View::make('/utilities/reactivate/packagereactivation');
	}


}
