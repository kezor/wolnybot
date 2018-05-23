<?php

namespace App\Http\Controllers;

use App\Player;
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $players = Player::where('user_id', Auth::id())->get();
//        Auth::id();
        return view('home', [
            'players' => $players
        ]);
    }
}
