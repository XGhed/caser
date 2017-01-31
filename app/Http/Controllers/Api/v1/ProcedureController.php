<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Validator;
use DB;

use App\Models\Procedure;

class ProcedureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $procedures = Procedure::all();

        foreach ($procedures as $procedure) {
        	$procedure->steps = json_decode($procedure->steps);
        }

    	return response()->json(compact('procedures'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
			
			$validator = $this->validator($request->all());

			if($validator->fails()){

				return response()->json($validator->getMessageBag(), 400);
			}

			$procedure = Procedure::create([
				'name'			=>	$request->name,
				'description'	=>	$request->description,
				'status'		=>	$request->status,
				'steps'			=>	json_encode($request->steps),
				]);


			return response()->json([
					'message'	=>	"success"
				], 200);


		} catch (\Exception $e) {

			return response()->json([
					'message'	=>	$e->getMessage()
				], 400);
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $procedure = Procedure::find($id);

        $procedure->steps = json_decode($procedure->steps);

        return response()->json(compact('procedure'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

			$procedure = Procedure::findOrFail($id);

			$procedure->name		= $request->name;
			$procedure->description	= $request->description;
			$procedure->status		= $request->status;
			$procedure->steps			= json_encode($request->steps);


			$validator = $this->validator($procedure->toArray());

			if($validator->fails()){

				return response()->json($validator->getMessageBag(), 400);
			}


			$procedure->save();

			return response()->json([
					'message'		=>		"success"
				], 200);


		} catch (\Exception $e) {

			return response()->json([
					'message'       =>      $e->getMessage()
				], 400);
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
        	
        	$procedure = Procedure::findOrFail($id);

        	$procedure->delete();

        	return response()->json([
					'message'		=>		"success"
				], 200);

        } catch (\Exception $e) {

        	return response()->json([
					'message'       =>      $e->getMessage()
				], 400);
        }
    }

    /**
    * Validator
    */
    public function validator($data){

		$validator = Validator::make($data, [
		 	'name'			=>	'required',
		 	'description'	=>	'required',
		 	'status'		=>	'required',
			'steps'			=>	'required'
		]);

		return $validator;
	}
}