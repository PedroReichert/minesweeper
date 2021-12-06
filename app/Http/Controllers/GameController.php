<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Models\Mark;
use App\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->gameService->loadGame($id);
        return new GameResource($this->gameService->field->load('marks'));
    }

    public function choose($id, Request $r){
        $r->validate([
            'column' => 'required|integer',
            'row' => 'required|integer'
        ]);

        $this->gameService->loadGame($id);
        $this->gameService->choose($r->input('row'),$r->input('column'));
        return new GameResource($this->gameService->field->load('marks'));

    }

    public function render($id){
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
    }

    
}
