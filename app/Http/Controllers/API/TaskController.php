<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskUser;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware(['auth', 'permission:tasks']);
    }
    public function index(Request $request)
    {
        if(Auth::user()->hasRole('Super Admin'))
        {
            $task=Task::with(['user', 'users']);
            if($request->search!=""){
                $task->where( 'name', 'LIKE', '%' . $request->search . '%' );
            }
            if($request->sortBy!=""){
                $task=$task->orderBy($request->sortBy, ($request->sortDesc=="true")?"desc":"asc");
            }else{
                $task=$task->orderBy("id", "desc");
            }
            if(isset($request->numRows) && $request->numRows>0){
                return $task->paginate($request->numRows);
            }else{
                return $task->get();
            }
        }
        else{
            $taskUserIds = TaskUser::where('user_id', auth()->user()->id)->pluck('task_id');
            $task=Task::with(['user', 'users'])->whereIn('id', $taskUserIds)
            ->orWhere('user_id', auth()->user()->id);
            if($request->search!=""){
                $task->where( 'name', 'LIKE', '%' . $request->search . '%' );
            }
            if($request->sortBy!=""){
                $task=$task->orderBy($request->sortBy, ($request->sortDesc=="true")?"desc":"asc");
            }else{
                $task=$task->orderBy("id", "desc");
            }
            if(isset($request->numRows) && $request->numRows>0){
                return $task->paginate($request->numRows);
            }else{
                return $task->get();
            }
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
        if(!auth()->user()->can('create_task'))
        {
            return response()->json("Unauthorized", 401);
        }
        $this->validate($request, [
            'name' => 'required|string|max:64',
        ]);
    
        $task = new Task();
        $task->name = $request->name;
        $task->is_completed = $request->is_completed;
        $task->user_id = auth()->user()->id;
        $task->save();
    
        $userIds = $request->input('assign_to', []);
        
        $ids = collect($userIds)->pluck('id');
        $task->users()->attach($ids);
    
        return response()->json("Record created successfully", 200);
    }

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
    public function update(Request $request, Task $task)
    {
        if(!auth()->user()->can('edit_task'))
        {
            return response()->json("Unauthorized", 401);
        }
        $this->validate($request, [
            'name' => 'required|string|max:64',
        ]);

        $task->update([
            'name' => $request->name,
            'is_completed' => $request->is_completed,
        ]);        

        $userIds = $request->input('assign_to', []);
        $ids = collect($userIds)->pluck('id');
        $task->users()->sync($ids);

        return response()->json("Record updated successfully", 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(auth()->user()->can('delete_task')){
            $task = Task::findOrFail($id);
            $task->users()->detach();
            $task->delete();
            return response()->json("Record deleted successfully", 200);
        }else{
            return response()->json("Unauthorized", 401);
        }
    }
}
