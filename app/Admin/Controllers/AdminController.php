<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;

class AdminController extends Controller {

    public function __construct() {
        //
    }

    public function home() {
        return view('admin.home');
    }
}