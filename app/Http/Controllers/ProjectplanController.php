<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectplanController extends Controller
{
        public function dashboard()
    {
        return view('project.indexprojectplan');
    }
}
