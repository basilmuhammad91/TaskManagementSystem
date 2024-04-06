<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct(){
        $this->middleware(['auth', 'permission:roles']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        //Declare a global variable with relation orders!
        $role=Role::with('permissions');
        //Check if there is any search value
        if($request->search!=""){
            $role->where( 'name', 'LIKE', '%' . $request->search . '%' );
        }
        //Check in any type of sorting
        if($request->sortBy!=""){
            //If the sorting is not related to Relations
            $role=$role->orderBy($request->sortBy, ($request->sortDesc=="true")?"desc":"asc");
        }else{
            //Default Sorting
            $role=$role->orderBy("id", "desc");
        }
        //Pagination
        if(isset($request->numRows) && $request->numRows>0){
            return $role->paginate($request->numRows);
        }else{
            return $role->get();
        }
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
        //
       if(auth()->user()->can('create_role')){
            $this->validate($request, [
                'name'=>'required|string|max:64',
                'permissions'=>'nullable',
            ]);

            $role = Role::create([
                'name'=>$request->name,
            ]);

            if($request->has("permissions")){
                $role->givePermissionTo($request->permissions);
            }

            return response()->json("Record created successfully", 200);
        }else{
            return response()->json("Unauthorized", 401);
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
        //
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
        //
        if(auth()->user()->can('edit_role')){
            $role = Role::findOrfail($id);
            $this->validate($request, [
                'name'=>'required|string',
            ]);
            $role->update($request->all());

            if($request->has("permissions")){
                $role->syncPermissions($request->permissions);
            }
            return response()->json("Record updated successfully", 200);
        }else{
            return response()->json("Unauthorized", 401);
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
        //
       if(auth()->user()->can('delete_role')){
            $roles = Role::findOrFail( $id);
            $roles->delete();
            return response()->json("Record deleted successfully", 200);
        }else{
            return response()->json("Unauthorized", 401);
        }
    }
}
