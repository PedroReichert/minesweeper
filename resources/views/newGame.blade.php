@extends('layout.app')

@section('content')
    
    <div class="row d-flex justify-content-center">
        <div class="col-md-6">
            <div class="card px-5 py-5" id="form1">
                <form action="{{ route('new') }}" method='post'>
                    @csrf
                    <div class="form-data" v-if="!submitted">
                        <div class="forms-inputs mb-4"> <span>Rows</span> <input autocomplete="off" type="text" id="email" name='rows'>

                        </div>
                        <div class="forms-inputs mb-4"> <span>Columns</span> <input autocomplete="off" type="text" name="columns">

                        </div>
                        <div class="forms-inputs mb-4"> <span>Mines</span> <input autocomplete="off" type="text" name="mines">

                        </div>
                        @if($errors->any())
                            <div class="invalid-feedback">{{$errors->first()}}</div>
                        @endif
                        <div class="mb-3"> <button type='submit' class="btn btn-dark w-100">Create Game</button> </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection