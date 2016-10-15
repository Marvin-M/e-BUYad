<?php

class MaintPPDController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

    //------------------------- DISCOUNT ---------------------------------

    public function showDiscount()
    {
        return View::make('maintenance/ppd/discount');
    }

    public function addDiscount(){
        if(!$this->isDiscountInactive(Input::get('name'))){
            try{
                DB::insert(
                    'INSERT INTO tblDiscounts VALUES (?,?,(?/100),?,now(),1,?)',
                    [
                        (new CodeController())->getDiscountCode(),
                        Input::get('name'),
                        Input::get('percent'),
                        Input::get('amount'),
                        Input::get('desc')
                    ]
                );

                return Redirect::to('/maintenance/ppd/discount')
                    ->with('message', 'Successfully added: '.Input::get('name'));
            }catch(PDOException $ex){

                return Redirect::to('/maintenance/ppd/discount')
                    ->with('message', 'Failed adding: '.Input::get('name').'. Name might already be existing');
            }
        }else{
            try{
                DB::update(
                    'UPDATE tblDiscounts SET dblDiscPerc = ?, decDiscAmt = ?, strDiscDesc = ?, intStatus = 1 WHERE strDiscName = ?',
                    [
                        Input::get('percent'),
                        Input::get('amount'),
                        Input::get('desc'),
                        Input::get('name')
                    ]
                );

                return Redirect::to('/maintenance/ppd/discount')
                    ->with('message', '*Successfully added: '.Input::get('name'));
            }catch(PDOException $ex){}
        }
    }

    public function deleteDiscount(){
        DB::update(
            'UPDATE tblDiscounts SET intStatus = 0 WHERE strDiscCode = ?',
            [
                Input::get('del_id')
            ]
        );

        return Redirect::to('/maintenance/ppd/discount')
            ->with('message', 'Successfully deleted: '.Input::get('del_name'));
    }

    public function updateDiscount(){
        try{
            DB::update(
                'UPDATE tblDiscounts SET strDiscName = ?, dblDiscPerc = ?, decDiscAmt = ?, strDiscDesc = ? WHERE strDiscCode = ?',
                [
                    Input::get('name'),
                    Input::get('percent'),
                    Input::get('amount'),
                    Input::get('desc'),
                    Input::get('code')
                ]
            );

            return Redirect::to('/maintenance/ppd/discount')
                ->with('message', 'Successfully updated: '.Input::get('name'));
        }catch(PDOException $ex){

            return Redirect::to('/maintenance/ppd/discount')
                ->with('message', 'Failed updating: '.Input::get('name').'. Name might already be existing');
        }
    }

    public function isDiscountInactive($name){
        $status = DB::table('tblDiscounts')
                    ->select('intStatus')
                    ->where('strDiscName', $name)
                    ->first();

        if($status == null){
            return false;
        }else{
            if($status->intStatus == 1){
                return false;
            }else{
                return true;
            }
        }
        // return dd($status->intStatus);
    }

    //------------------------- PACKAGES ---------------------------------

    public function showPackages()
    {
        return View::make('maintenance/ppd/packages');
    }

    public function getNames(){
        $table = Input::get('table');
        $column = Input::get('column');

        $res = DB::select('SELECT '.$column.' FROM '.$table.' WHERE intStatus = 1');

        return Response::json($res);
    }

    public function getParticipatingProducts(){
        $res = DB::select(
                            'SELECT
                                p.strProdCode,
                                p.strProdType,
                                b.strPMBranName,
                                concat_ws(
                                    \' \',  
                                    (
                                        SELECT group_concat(g.strPMGenName SEPARATOR \' \') 
                                        FROM tblmedgennames mg LEFT JOIN tblprodmedgeneric g ON mg.strMedGenGenCode = g.strPMGenCode
                                        WHERE mg.strMedGenMedCode = m.strProdMedCode GROUP BY mg.strMedGenMedCode
                                    ),
                                    concat(m.decProdMedSize, \' \', u.strUOMName)
                                ) as \'MedName\',

                                concat(
                                    nm.strProdNMedName, 
                                    \' \', 
                                    concat_ws(\' \', g.strGenSizeName, s.decNMStanSize, un.strUOMName)
                                ) as \'NMedName\',

                                pr.decProdPricePerPiece,

                                pc.intPackProdQuantity

                            FROM tblproducts p

                            LEFT JOIN tblprodmed m
                            ON p.strProdCode = m.strProdMedCode
                            LEFT JOIN tblprodnonmed nm
                            ON p.strProdCode = nm.strProdNMedCode
                            LEFT JOIN tblProdPrice pr
                            ON p.strProdCode = pr.strProdPriceCode

                            LEFT JOIN tblprodmedbranded b
                            ON m.strProdMedBranCode = b.strPMBranCode
                            LEFT JOIN tbluom u 
                            ON m.strProdMedUOMCode = u.strUOMCode

                            LEFT JOIN tblnmedgeneral gt
                            ON nm.strProdNMedCode = gt.strNMGenCode
                            LEFT JOIN tblgensize g
                            ON gt.strNMGenSizeCode = g.strGenSizeCode
                            LEFT JOIN tblnmedstandard s
                            ON nm.strProdNMedCode = s.strNMStanCode
                            LEFT JOIN tbluom un
                            ON s.strNMStanUOMCode = un.strUOMCode

                            LEFT JOIN tblPackProducts pc
                            ON pc.strPackProdProdCode = p.strProdCode

                            WHERE pc.strPackProdCode = ?;',
                            [
                                Input::get('packcode')
                            ]
                        );
        return Response::json($res);
    }

    public function addPackage(){
        try{
            //insert package info

            $packcode = (new CodeController())->getPackageCode();
            
            DB::insert(
                'INSERT INTO tblPackages VALUES(?,?,?,?,?,now(),1)',
                [
                    $packcode,
                    Input::get('name'),
                    Input::get('start'),
                    Input::get('end'),
                    Input::get('pkgprice')
                ]);

            $prods = explode(";",Input::get('packprodcode'));
            $qtys = explode(";",Input::get('packprodqty'));
            $i = 0;

            foreach($prods as $prod){
                DB::insert('INSERT INTO tblPackProducts VALUES(?,?,?)',
                    [
                        $packcode,
                        $prod,
                        $qtys[$i]
                    ]);
                $i++;
            }   

            return Redirect::to('/maintenance/ppd/packages');
        }catch(PDOException $ex){
        }
    }

    public function updatePackage(){
        try{
            $packcode = Input::get('packcode');

            //update package details

            DB::update(
                'UPDATE tblPackages SET 
                    strPackName = ?,
                    datPackFrom = ?,
                    datPackTo = ?,
                    decPackPrice = ?,
                    dtmLastUpdate = now()
                    WHERE strPackCode = ?',
                [
                    Input::get('name'),
                    Input::get('start'),
                    Input::get('end'),
                    Input::get('pkgprice'),
                    $packcode
                ]
                );

            //delete all existing package details

            DB::table('tblPackProducts')
                ->where('strPackProdCode','=',$packcode)
                ->delete();

            //insert the new products

            $prods = explode(";",Input::get('packprodcode'));
            $qtys = explode(";",Input::get('packprodqty'));
            $i = 0;

            foreach($prods as $prod){
                DB::insert('INSERT INTO tblPackProducts VALUES(?,?,?)',
                    [
                        $packcode,
                        $prod,
                        $qtys[$i]
                    ]);
            }

            return Redirect::to('/maintenance/ppd/packages');
        }catch(PDOException $ex){}
    }
}
