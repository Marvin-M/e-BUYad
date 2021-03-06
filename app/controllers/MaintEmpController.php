<?php

class MaintEmpController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('maintenance/employee/branchdet');
	}

    public function addBranch(){

        DB::insert(
            'INSERT INTO tblBranches (strBranchCode, strBranchName, strBranchAddress, strBranchContNum, strBranchFaxNum, dtmLastUpdate, intStatus) VALUES (?,?,?,?,?, now(), 1)',
            [
                (new CodeController())->getBranchCode(),
                Input::get('name'),
                Input::get('address'),
                Input::get('telnum'),
                Input::get('faxnum')
            ]
        );

        return Redirect::to('/maintenance/employee/branchdet');
    }

    public function updateBranch(){
        DB::update(
            'UPDATE tblBranches SET strBranchName = ?, strBranchAddress = ?, strBranchContNum = ?, strBranchFaxNum = ?, dtmLastUpdate = now() WHERE strBranchCode = ?',
            [
                Input::get('name'),
                Input::get('address'),
                Input::get('telnum'),
                Input::get('faxnum'),
                Input::get('code')
            ]
            );

        return Redirect::to('/maintenance/employee/branchdet');
    }

    public function deleteBranch(){
        DB::update(
            'UPDATE tblBranches SET intStatus = 0, dtmLastUpdate = now() WHERE strBranchCode = ?',
            [
                Input::get('del_id')
            ]
        );

        return Redirect::to('/maintenance/employee/branchdet');
    }

    //JOB DESCRIPTION

    public function showJobs(){
        return View::make('/maintenance/employee/jobdescdet');
    }

    public function addJob(){
        DB::insert(
            'INSERT INTO tblEmpJobDesc VALUES (?,?,?,now(),1)',
            [
                (new CodeController())->getJobCode(),
                Input::get('name'),
                Input::get('description')
            ]
            );

        return Redirect::to('/maintenance/employee/jobdet');
    }

    public function updateJob(){
        DB::update(
            'UPDATE tblEmpJobDesc SET strEJName = ?, strEJDescription = ?, dtmLastUpdate = now() WHERE strEJCode = ?',
            [
                Input::get('name'),
                Input::get('description'),
                Input::get('code')
            ]
        );

        return Redirect::to('/maintenance/employee/jobdet');
    }

    public function deleteJob(){
        DB::update(
            'UPDATE tblEmpJobDesc SET intStatus = 0, dtmLastUpdate = now()  WHERE strEJCode = ?',
            [
                Input::get('del_id')
            ]
        );

        return Redirect::to('/maintenance/employee/jobdet');
    }

    //EMPLOYEE DETAILS

    public function showEmployee(){
        return View::make('maintenance/employee/empdet');
    }

    public function addEmployee(){

        // $resj = DB::select(
        //             'SELECT strEJCode FROM tblEmpJobDesc WHERE strEJName = ?',
        //             [Input::get('jobcode')]
        //         );

        // foreach($resj as $data){
        //     $job = $data->strEJCode;
        // }

        // $resb = DB::select(
        //     'SELECT strBranchCode FROM tblBranches WHERE strBranchName = ?',
        //     [Input::get('branchcode')]
        // );

        // foreach($resb as $data1){
        //     $branch = $data1->strBranchCode;
        // }

        $empcode = (new CodeController())->getEmployeeCode();

        DB::insert(
            'INSERT INTO tblEmployee VALUES (?,?,?,?,?,?,now(),1)',
            [
                $empcode,
                Input::get('fname'),
                Input::get('mname'),
                Input::get('lname'),
                Input::get('address'),
                Input::get('contnum'),
            ]
        );
        return Redirect::to('maintenance/employee/empdet');
    }

    public function deleteEmployee(){
        DB::update(
            'UPDATE tblEmployee SET intStatus = 0, dtmLastUpdate = now() WHERE strEmpCode = ?',
            [
                Input::get('del_id')
            ]
        );

        return Redirect::to('maintenance/employee/empdet');
    }

    public function updateEmployee(){

        // $resj = DB::select(
        //     'SELECT strEJCode FROM tblEmpJobDesc WHERE strEJName = ?',
        //     [Input::get('jobcode')]
        // );

        // foreach($resj as $data){
        //     $job = $data->strEJCode;
        // }

        // $resb = DB::select(
        //     'SELECT strBranchCode FROM tblBranches WHERE strBranchName = ?',
        //     [Input::get('branchcode')]
        // );

        // foreach($resb as $data1){
        //     $branch = $data1->strBranchCode;
        // }

        DB::update(
            'UPDATE tblEmployee
                SET strEmpFName = ?,
                    strEmpMName = ?,
                    strEmpLName = ?,
                    strEmpAddress = ?,
                    strEmpContNum = ?,
                    dtmLastUpdate = now()
                WHERE strEmpCode = ?',
            [
                Input::get('fname'),
                Input::get('mname'),
                Input::get('lname'),
                Input::get('address'),
                Input::get('contnum'),
                Input::get('code'),
            ]
        );

        return Redirect::to('maintenance/employee/empdet');
    }
}
