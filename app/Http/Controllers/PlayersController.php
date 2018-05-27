<?php

namespace App\Http\Controllers;


use App\Player;
use App\Service\GameService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use \Illuminate\Http\Request;

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

    public function updateData(Request $request, $playerId)
    {
        $player = Player::find($playerId);

        if ($player) {
            $gameService = new GameService($player);
            if ($gameService->isPlayerLoggedIn()) {
                $gameService->updateStock();
                $gameService->updateSpacesData();
                $request->session()->flash('success', 'Player data updated.');
            } else {
                $request->session()->flash('error', 'Error when try to log in the player.');
            }
        }
        return redirect('home');
    }

    public function show($playerId)
    {
        return view('player.show', [
            'player' => Player::find($playerId)
        ]);
    }

}