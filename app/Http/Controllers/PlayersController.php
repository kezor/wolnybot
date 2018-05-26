<?php

namespace App\Http\Controllers;


use App\Player;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class PlayersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('player.create');
    }

    public function store()
    {
        $player = new Player();

        $player->username = Input::get('username');
        $player->password = Input::get('password');
        $player->server_id = Input::get('server_id');
        $player->user_id = Auth::id();
        $player->active = false;

        $player->save();

        return redirect('/home');
    }

}