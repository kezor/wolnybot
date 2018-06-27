<?php

namespace App\Http\Controllers;


use App\Building\Farmland;
use App\Jobs\ProcessFarmland;
use App\Player;
use App\Product;
use App\Service\GameService;
use App\Task;
use App\Tasks\CollectPlants;
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
                $gameService->updateStock();
                $gameService->updateBuildings();
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

    public function addTask($playerId)
    {
        $player = Player::find($playerId);
        $farmId = Input::get('farm_id');
        $spaceId = Input::get('space_id');
        $plantToSeed = Input::get('plant_id');

        $farmland = Farmland::find($spaceId);

        $productToSeed = Product::where('pid', $plantToSeed)
            ->where('player_id', $player->id)
            ->first();

        $job = new CollectPlants();
        $job->productToSeed = $productToSeed;
        $job->farmland = $farmland;
        $job->goal = 10000;

        $task = new Task();
        $task->job = serialize($job);
//        $task->jobData = $job->toJson();
        $task->player_id = $player->id;
        $task->status = Task::TASK_STATUS_ACTIVE;
        $task->save();

        $processFarmland = new ProcessFarmland($task);

        $this->dispatch($processFarmland);


        return back();
    }

}