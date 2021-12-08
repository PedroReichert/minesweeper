@extends('layout.app')

@section('content')
<div class="row d-flex justify-content-center">
    <div class="col-md-6">
        <div class="card px-5 py-5" id="form1">
            @if (Session::has('message'))
                <div class="alert alert-info">{{ Session::get('message') }}</div>
            @endif
            @if (Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            @endif
            <table class="table table-bordered">
                <tbody>
                    @for ($r = 1; $r <= $field->row; $r++)
                        <tr>
                            @for ($c = 1; $c <= $field->columns; $c++)
                                <td>
                                    @php
                                        $mark = $field->marks()->where([['column',$c],['row',$r]])->first();    
                                    @endphp
                                    @if($mark->is_seen)
                                        @if($mark->type == 'MINE')
                                            B
                                        @else
                                            {{($mark->label == 0)?'':$mark->label}}
                                        @endif
                                    @else
                                    <a class="btn btn-dark" href="{{url('/play/'.$field->id.'/choose?row='.$r.'&column='.$c)}}"></a>
                                    @endif

                                </td>
                            @endfor
                        </tr>
                    @endfor

                </tbody>
            </table>
            <a class="btn btn-dark" href="{{url('/list')}}">Back</a>
        </div>
    </div>
</div>
@endsection