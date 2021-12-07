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

class GameController extends Controller
{
    private $gameService;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->gameService = new GameService(Auth::user());
    }

    public function create(Request $r){
        $data = $r->validate([
            'columns' => 'required|integer',
            'rows' => 'required|integer',
            'mines' => 'required|integer'
        ]);

        $this->gameService->newGame($data['columns'],$data['rows'],$data['mines']);
        return new GameResource($this->gameService->field->load('marks'));
    }

    public function listGames(){
        return GameResource::collection($this->gameService->listGames());
    }

    public function loadGame($id){
        try{
            $this->gameService->loadGame($id);
            return new GameResource($this->gameService->field->load('marks'));
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

            $status = $this->gameService->checkStatus();
            if($status === true){
                $message =  'YOU WON!';
            }else if($status === false){
                $message =  'You Lost!';
            }

            return Response::json(['success'=>true, 'data'=>$message]);
        }catch(Exception $e){
            Log::error('ChooseError', ['error'=>$e, 'request'=>$r->all()]);
            return Response::json(['success'=>false, 'error'=>$e->getMessage()],400);
        }
    }

    public function addFlag($id, Request $r){
        $r->validate([
            'column' => 'required|integer',
            'row' => 'required|integer',
            'type'=> 'required|in:'.implode(',',[Mark::FLAG, Mark::QUESTION])
        ]);
        try{
            $this->gameService->loadGame($id);
            $this->gameService->addFlag($r->input('row'),$r->input('column'), $r->input('type'));

            return Response::json(['success'=>true, 'data'=>'Cell flaged as '.$r->input('type')]);
        }catch(Exception $e){
            Log::error('FlagError', ['error'=>$e, 'request'=>$r->all()]);
            return Response::json(['success'=>false, 'error'=>$e->getMessage()],400);
        }

    }

    public function render($id){
        try{
            $this->gameService->loadGame($id);
            $field = $this->gameService->field;
            echo "<table border='1'>";
            for ($r = 1; $r <= $field->row; $r++) {
                echo "<tr>";
                for ($c = 1; $c <= $field->columns; $c++) {
                    echo '<td>';
                        $mark = $field->marks()->where([['column',$c],['row',$r]])->first();
                        if($mark->is_seen){
                            if($mark->type == 'MINE'){
                                echo 'B';
                            }else{
                                echo $mark->label;
                            }
                        }else{
                            echo '*';
                        }
    
                    echo '</td>';
                }
                echo "</tr>";
            }
            echo "</table>";
        }catch(Exception $e){
            return Response::json(['success'=>false, 'error'=>$e->getMessage()],400);
        }
    }

    
}
