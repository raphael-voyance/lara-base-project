<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    /**
     * Affiche la page d'index de la documentation
     */
    public function index()
    {
        return view('documentation.index');
    }

    /**
     * Affiche la documentation des plugins
     */
    public function plugins()
    {
        return view('documentation.plugins');
    }
}
