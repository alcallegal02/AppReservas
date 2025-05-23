<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Obtener reservas del usuario autenticado
        $reservations = Auth::user()->reservations()
            ->with(['table.zone', 'timeSlot'])
            ->orderBy('reservation_date', 'desc')
            ->paginate(10);
            
        return view('dashboard', compact('reservations'));
    }
}
