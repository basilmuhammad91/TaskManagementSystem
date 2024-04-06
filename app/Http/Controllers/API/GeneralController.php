<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class GeneralController extends Controller
{
    public function __construct(){
        $this->middleware(['auth']);
    }

    public function getAllUsers()
    {
        return User::all();
    }
}
