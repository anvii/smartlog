<?php

namespace App\Http\Controllers;

use App\Models\Client;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function apiIndex()
    {
        return Client::orderBy('id')->get();
    }
}
