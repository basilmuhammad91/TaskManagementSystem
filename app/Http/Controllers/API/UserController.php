<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User; 

class UserController extends Controller
{
    public function __construct(){
        $this->middleware(['auth', 'permission:users']);
    }
    
    public function index(Request $request)
    {
        //Declare a global variable with relation orders!
        $user=User::with(['roles'=>function($query) use ($request) {
            if($request->sortBy=="roles[0].name"){
              $query->orderBy('name', ($request->sortDesc=="true")?"desc":"asc");
            }
          }, 'permissions']);
        //Check if there is any search value
        if($request->search!=""){
            $user->where( 'name', 'LIKE', '%' . $request->search . '%' )
            ->orWhere( 'email', 'LIKE', '%' . $request->search . '%' )
            ->orWhereHas('roles', function($query) use ($request){
                $query->where('name', 'LIKE', '%' . $request->search . '%');
            });
        }
        //Check in any type of sorting
        if($request->sortBy!=""){
            //If the sorting is not related to Relations
            if($request->sortBy != 'roles[0].name'){
                $user=$user->orderBy($request->sortBy, ($request->sortDesc=="true")?"desc":"asc");
            }
        }else{
            //Default Sorting
            $user=$user->orderBy("id", "desc");
        }
        //Pagination
        if(isset($request->numRows) && $request->numRows>0){
            return $user->paginate($request->numRows);
        }else{
            return $user->get();
        }
    }

    public function store(Request $request)
    {
        //
        if(auth()->user()->can('create_user')){
            $this->validate($request, [
                'name'=>'required|string|max:64',
                'email'=>'required|string|email|max:64|unique:users',
                'password'=>'required|string|min:8',
                'photo'=>'nullable',
            ]);
            if($request['photo']){
                $name=time().'.'.explode('/', explode(':', substr($request->photo,0,strpos($request->photo, ';')))[1])[1];
                \Image::make($request->photo)->save(public_path('images/users/').$name);
            }else{
                $name="profile.png";
            }

            $user = new User();
            $user->name=$request->name;
            $user->email=$request->email;
            $user->password=Hash::make($request->password);
            $user->photo=$name;

            if($request->has('role')){
                $user->assignRole($request->role);
            }
            $user->save();
            return response()->json("Record created successfully", 200);
        }else{
            return response()->json("Unauthorized", 401);
        }
    }

    public function update(Request $request, $id)
    {
        //
        if(auth()->user()->can('edit_user')){
            $user = User::findOrfail($id);
            $this->validate($request, [
                'name'=>'required|string|max:64',
                'email'=>'required|string|email|max:64|unique:users,email,'.$user->id,
                'password'=>'sometimes|string|min:8',
                'photo'=>'nullable',
            ]);
            if($request['password']!=""){
                $request->merge(['password'=>Hash::make($request['password'])]);
            }
            if($request['photo']!=$user->photo){
                $name=time().'.'.explode('/', explode(':', substr($request->photo,0,strpos($request->photo, ';')))[1])[1];
                $publicPath=public_path('images/users/'.$name);
                \Image::make($request->photo)->save($publicPath);
                if((file_exists(public_path('images/users/'.$user->photo)))&&($user->photo!="profile.png")){
                    @unlink(public_path('images/users/'.$user->photo));
                }
                $request->merge(['photo'=>$name]);
            }
            $user->update($request->all());

            if($request->has('role')){
                $userRole = $user->getRoleNames();
                foreach($userRole as $role){
                    $user->removeRole($role);
                }

                $user->assignRole($request->role);
            }
            $user->save();
            return response()->json("Record updated successfully", 200);
        }else{
            return response()->json("Unauthorized", 401);
        }
    }

    public function destroy($id)
    {
        //
        if(auth()->user()->can('delete_user')){
            $user = User::findOrfail($id);
            //Delete the user
            if((file_exists(public_path('images/users/'.$user->photo)))&&($user->photo!="profile.png")){
                @unlink(public_path('images/users/'.$user->photo));
            }
            $user->delete();
            return response()->json("Record deleted successfully", 200);
        }else{
            return response()->json("Unauthorized", 401);
        }
    }
}
