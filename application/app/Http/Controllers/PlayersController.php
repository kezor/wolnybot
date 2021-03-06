<?php

namespace App\Http\Controllers;

use App\Player;
use App\Product;
use App\Service\GameService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

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
                $gameService->update();
                Session::flash('success', 'Player data updated.');
            } else {
                Session::flash('error', 'Error when try to log in the player.');
            }
        }
        return Redirect::back();
    }

    public function show($playerId)
    {
        $player = Player::find($playerId);
        return view('player.show', [
            'player' => $player,
            'plantsToSeed' => $this->getPlantsToDropdown($player)
        ]);
    }

    private function getPlantsToDropdown(Player $player)
    {
        $plants = [];

        /** @var Product $product */
        foreach ($player->products as $product) {
            if($product->isPlant()){
                $plants[$product->getPid()] = $product->getName();
            }
        }
        return $plants;
    }

}