<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use Validator;
use DB;

use App\Models\User;
use App\Models\OfficeUser;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        $users->load('offices');

    	return response()->json(compact('users'));
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
        	DB::beginTransaction();
			
			$validator = $this->validator($request->all());

			if($validator->fails()){

				return response()->json($validator->getMessageBag(), 400);
			}

			$user = User::create([
				'username'	=>	$request->username,
				'name'		=>	$request->name
				]);

			foreach ($request->offices as $office_id) {
				OfficeUser::create([
					'user_id'		=>	$user->id,
					'office_id'		=>	$office_id
					]);
			}


			DB::commit();

			return response()->json([
					'message'	=>	"success"
				], 200);


		} catch (\Exception $e) {

			DB::rollback();

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
        $user = User::find($id);

        $user->load('offices');

        return response()->json(compact('user'));
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
        	DB::beginTransaction();

			$user = User::findOrFail($id);

			$validator = $this->validator($request->all());

			if($validator->fails()){

				return response()->json($validator->getMessageBag(), 400);
			}

			$user->username		= $request->username;
			$user->name			= $request->name;

			$user->save();

			$office_users = OfficeUser::where('user_id', $user->id)
				->get();

			//delete if not on new list
			foreach ($office_users as $office_user) {
				if(! collect($request->offices)->contains($office_user->office_id) ){
					$office_user->delete();
				}
			}

			//insert if not existing
			foreach ($request->offices as $office_id) {
				if(! OfficeUser::find($office_id) ){
					OfficeUser::create([
						'user_id'		=>	$user->id,
						'office_id'		=>	$office_id
						]);
				}
			}

			DB::commit();

			return response()->json([
					'message'		=>		"success"
				], 200);


		} catch (\Exception $e) {

			DB::rollback();

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
        	
        	$user = User::findOrFail($id);

        	$user->delete();

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
			'username'		=>	'required',
			'offices'		=>	'required'
		]);

		return $validator;
	}
}