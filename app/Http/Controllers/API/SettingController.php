<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function __construct(){
        $this->middleware(['auth', 'permission:application']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        return Setting::first();
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        if(auth()->user()->can('application')){
            $setting = Setting::findOrfail($id);
            $this->validate($request, [
                'company_name'=>'required|string|max:64',
                'logo'=>'nullable',
            ]);
            if($request['logo']!=$setting->logo){
                $logo=time().'.'.explode('/', explode(':', substr($request->logo,0,strpos($request->logo, ';')))[1])[1];
                $publicPath=public_path('images/logo/'.$logo);
                \Image::make($request->logo)->save($publicPath);
                if(file_exists(public_path('images/logo/'.$setting->logo))){
                    @unlink(public_path('images/logo/'.$setting->logo));
                }
                $request->merge(['logo'=>$logo]);
            }
            $setting->update($request->all());
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
    }
}
