<?php

namespace App\Http\Resources;

use App\Models\Mark;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'gameId'=>$this->id,
            'is_completed'=>$this->is_completed,
            'is_won'=>$this->is_won,
            'columnsCount'=>$this->columns,
            'rowsCount'=>$this->row,
            'minesCount'=>$this->marks->where('type',Mark::MINE)->count(),
            'markers'=>$this->marks
        ];
    }
}
