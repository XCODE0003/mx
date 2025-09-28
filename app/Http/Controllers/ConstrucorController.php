<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConstrucorController extends Controller
{
    public function createBlank()
    {
        return response()->json([
            'message' => 'Blank created successfully',
        ], 201);
    }
}
