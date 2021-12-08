<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Models\Mark;
use App\Services\GameService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class GameWebController extends Controller
{
    private $gameService;

    private $user_info;
    public function __construct()
    {
        $this->middleware('auth:web');
        $this->middleware(function ($request, $next) {
            $this->gameService = new GameService(auth('web')->user());
            return $next($request);
        });

    }

    public function create(Request $r){
        $data = $r->validate([
            'columns' => 'required|integer',
            'rows' => 'required|integer',
            'mines' => 'required|integer'
        ]);

        $this->gameService->newGame($data['columns'],$data['rows'],$data['mines']);
        return redirect('list');
    }

    public function listGames(){
        return view('gamesList')
                ->with('gameList',$this->gameService->listGames());
    }

    public function loadGame($id){
        try{
            $this->gameService->loadGame($id);
            return view('game')
                ->with('field',$this->gameService->field);
        }catch(Exception $e){
            return Response::json(['success'=>false, 'error'=>$e->getMessage()],400);
        }
    }

    public function choose($id, Request $r){
        $r->validate([
            'column' => 'required|integer',
            'row' => 'required|integer'
        ]);

        try{
            $this->gameService->loadGame($id);
            $this->gameService->choose($r->input('row'),$r->input('column'));
            $message = 'Cell revealed!';
            Session::flash('message', $message);

            $status = $this->gameService->checkStatus();
            if($status === true){
                $message =  'YOU WON!';
                Session::flash('message', $message);
            }else if($status === false){
                $message =  'You Lost!';
                Session::flash('error', $message);
            }

            return redirect()->back();
        }catch(Exception $e){
            Session::flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

}
