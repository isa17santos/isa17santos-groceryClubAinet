<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function show ()
    {
        return view('membership.index');
    }
}
