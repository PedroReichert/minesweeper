<?php

namespace App\Services;

use App\Models\Field;
use App\Models\Mark;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\ExecutableFinder;

class GameService{

    public $field;
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function listGames(){
        return $this->user->fields()->with('marks')->get();
    }

    public function addFlag($row, $column, $type){
        if(!$this->isOutOfBound($row,$column)){
            $mark = $this->field->marks()->where([['column',$column],['row',$row]])->first();
            if(!$mark->is_seen){
                $mark->flag = $type;
                $mark->save();
                return true;
            }else{
                throw new Exception("Cell is already revealed!");
            }
        }
        throw new Exception("Cell is out of boundaries!");
    }

    public function loadGame($id){
        $this->field = $this->user->fields()->find($id);
        if(is_null($this->field)){
            throw new Exception("Game does not exists or does not belongs to the logged user");
        }
    }

    public function newGame($columns, $rows, $minesCount){
        $this->field = Field::create([
            'columns'=>$columns,
            'row'=>$rows,
            'user_id'=>$this->user->id
        ]);

        while($minesCount){
            $mine = Mark::create([
                'field_id'=>$this->field->id,
                'column'=>rand(1,$columns),
                'row'=>rand(1,$rows),
                'type'=>Mark::MINE,
                'is_seen'=>0
            ]);
            $this->mines[] = $mine;
            $minesCount--;
        }

        for ($r = 1; $r <= $rows; $r++) {
            for ($c = 1; $c <= $columns; $c++) {
                $minesAround = $this->radar($r,$c, [Mark::MINE]);
                
                $mine = Mark::create([
                    'field_id'=>$this->field->id,
                    'column'=>$c,
                    'row'=>$r,
                    'type'=>Mark::LABEL,
                    'label'=>count($minesAround),
                    'is_seen'=>0
                ]);     
            }
        }
    }

    public function choose($row, $column){
        if($this->field->is_completed){
            throw new Exception('Game is over!');
        }

        if($this->isOutOfBound($row, $column)){
            throw new Exception('Choosed cell is out of boundaries!');
        }

        $markOne = $this->field->marks()->where([['row',$row],['column',$column]])->first();
    
        if(!is_null($markOne->flag)){
            throw new Exception('Can not choose a FLAGED cell');
        }
        
        $markOne->is_seen = 1;
        $markOne->save();

        $marks = $this->radar($row,$column, [Mark::LABEL]);
        foreach($marks as $mark){
            if(!is_null($mark->label) and !$mark->is_seen and is_null($mark->flag) and $mark->type != Mark::MINE){
                $mark->is_seen = 1;
                $mark->save();
                if($mark->label == 0){
                    array_merge($marks,$this->choose($mark->row, $mark->column));
                }else{
                    return $marks;
                }
            }
        }

        return $marks;        
    }

    public function radar($row, $column, Array $types){    
        $found = [];
        $possibilities = $this->mountPossibilities($row,$column);
        foreach($possibilities as $possibility){
            if($this->isOutOfBound($possibility['row'],$possibility['column'])){
                continue;
            }
            $find = $this->field
                ->marks()
                ->where([['row',$possibility['row']],['column',$possibility['column']]])
                ->whereIn('type',$types)
                ->first();
            
            if(!is_null($find)){
                $found[] = $find;
            }
        }
        
        return $found;
    }

    public function mountPossibilities($row, $column){
        return [
            ['row'=>$row - 1, 'column'=>$column - 1],
            ['row'=>$row - 1, 'column'=>$column],
            ['row'=>$row - 1, 'column'=>$column + 1],
            ['row'=>$row, 'column'=>$column - 1],
            //['row'=>$row, 'column'=>$column],
            ['row'=>$row, 'column'=>$column + 1],
            ['row'=>$row + 1, 'column'=>$column - 1],
            ['row'=>$row + 1, 'column'=>$column],
            ['row'=>$row + 1, 'column'=>$column + 1],
            
            
        ];
    }

    public function isOutOfBound($row, $column){
        if( $row > $this->field->row or 
            $column > $this->field->columns or
            $row < 0 or
            $column < 0){
            return true;
        }
        return false;
    }
    
    public function checkStatus(){
        if($this->field->marks()->where([['type',Mark::MINE],['is_seen',1]])->exists()){
            $this->field->is_completed = 1;
            $this->field->save();
            return false;
        }

        if($this->field->marks()->where('is_seen',1)->count() == ($this->field->row * $this->field->columns - $this->field->marks()->where('type',Mark::MINE)->count())){
            $this->field->is_completed = 1;
            $this->field->is_won = 1;
            $this->field->save();
            return true;
        }

        return null;
    }

}