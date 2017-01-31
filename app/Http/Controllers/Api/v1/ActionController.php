<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Validator;
use DB;

use App\Models\Action;

class ActionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $actions = Action::all();

        foreach ($actions as $action) {
        	$action->usages = json_decode($action->usages);
        }

    	return response()->json(compact('actions'));
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

			$action = Action::create([
				'name'			=>	$request->name,
				'usages'		=>	json_encode($request->usages),
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
        $action = Action::find($id);

        $action->usages = json_decode($action->usages);

        return response()->json(compact('action'));
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

			$action = Action::findOrFail($id);

			$action->name		= $request->name;
			$action->usages		= json_encode($request->usages);


			$validator = $this->validator($action->toArray());

			if($validator->fails()){

				return response()->json($validator->getMessageBag(), 400);
			}


			$action->save();

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
        	
        	$action = Action::findOrFail($id);

        	$action->delete();

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
			'usages'		=>	'required'
		]);

		return $validator;
	}
}