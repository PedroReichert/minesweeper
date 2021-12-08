@extends('layout.app')

@push('head')
    <link rel="stylesheet" href="{{asset('css/login.css')}}">
    <script src="{{asset('js/login.js')}}"></script>

@endpush

@section('content')
    
    <div class="row d-flex justify-content-center">
        <div class="col-md-6">
            <div class="card px-5 py-5" id="form1">
                <form action="{{ route('auth') }}" method='post'>
                    @csrf
                    <div class="form-data" v-if="!submitted">
                        <div class="forms-inputs mb-4"> <span>Email or username</span> <input autocomplete="off" type="text" id="email" name='email'>
                            <div class="invalid-feedback">A valid email is required!</div>
                        </div>
                        <div class="forms-inputs mb-4"> <span>Password</span> <input autocomplete="off" type="password" name="password">
                            <div class="invalid-feedback">Password must be 8 character!</div>
                        </div>
                        @if($errors->any())
                            <div class="invalid-feedback">{{$errors->first()}}</div>
                        @endif
                        <div class="mb-3"> <button type='submit' class="btn btn-dark w-100">Login</button> </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection