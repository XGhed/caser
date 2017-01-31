<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Validator;
use DB;

use App\Models\Office;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $offices = Office::all();

        foreach ($offices as $office) {
        	$office->functions = json_decode($office->functions);
        }

    	return response()->json(compact('offices'));
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

			$office = Office::create([
				'name'			=>	$request->name,
				'description'	=>	$request->description,
				'functions'		=>	json_encode($request->functions),
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
        $office = Office::find($id);

        $office->functions = json_decode($office->functions);

        return response()->json(compact('office'));
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

			$office = Office::findOrFail($id);

			$office->name			= $request->name;
			$office->description	= $request->description;
			$office->functions		= json_encode($request->functions);


			$validator = $this->validator($office->toArray());

			if($validator->fails()){

				return response()->json($validator->getMessageBag(), 400);
			}


			$office->save();

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
        	
        	$office = Office::findOrFail($id);

        	$office->delete();

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
			'functions'		=>	'required'
		]);

		return $validator;
	}
}