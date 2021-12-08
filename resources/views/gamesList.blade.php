@extends('layout.app')

@section('content')
<div class="row d-flex justify-content-center">
    <div class="col-md-6">
        <div class="card px-5 py-5" id="form1">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">GameId</th>
                    <th scope="col">Created</th>
                    <th scope="col">Rows</th>
                    <th scope="col">Columns</th>
                    <th scope="col">Finished</th>
                    <th scope="col">Won</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($gameList as $i=>$game)
                        <tr>
                            <th scope="row">{{(($i+1))}}</th>
                            <td>{{(($game->id))}}</td>
                            <td>{{ date('d-m-Y H:i:s', strtotime($game->created_at))}}</td>
                            <td>{{ $game->row }}</td>
                            <td>{{ $game->columns }}</td>
                            <td>{{ $game->is_completed ? 'Yes':'No' }}</td>
                            <td>{{ $game->is_won ? 'Yes':'No' }}</td>
                            <td><a class="btn btn-dark" href="{{url('/play/'.$game->id)}}">Play</a> </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
            <a class="btn btn-dark" href="{{url('/newgame')}}">New Game</a>
        </div>
    </div>
</div>
@endsection