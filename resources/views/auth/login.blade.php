@extends('layout.head')

@section('titulo', 'Medcloud')

@section('paragrafo', 'Login')

@section('scripts')
   
    <link rel="stylesheet" type="text/css" href="/css/login.css">

@endsection

@section('conteudo')

<div class="card-body">
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group row">
            <label for="email" class="col-md-4 col-form-label text-md-right">Seu Email</label>

            <div class="col-md-6">
                <input id="email" type="email"
                    class="form-control @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}" required autocomplete="email" autofocus>

                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="password" class="col-md-4 col-form-label text-md-right">Sua
                Senha</label>

            <div class="col-md-6">
                <input id="password" type="password"
                    class="form-control @error('password') is-invalid @enderror" name="password"
                    required autocomplete="current-password">

                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-8 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    Entrar no Sistema
                </button>


                <a class="btn btn-link" href="{{ route('register') }}">
                    Registrar-se
                </a>
            </div>
        </div>
    </form>
</div>
Teste 1
@endsection
