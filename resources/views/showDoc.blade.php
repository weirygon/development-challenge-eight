@extends('layout.main')

@section('titulo', 'Doutor')

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/css/show.css">

@endsection

@section('conteudo')

    {{$image}}

    <div class="container-block-patient">
        <div class="block-patient">
            <div class="head-patient">
                <div id="title">
                    <h1>Meus dados</h1>
                </div>
            </div>
            <hr />
            <div class="date">
                <div class="patient-data">
                    <div class="line1">
                        <div id="name">
                            <label>NOME:</label>
                            <label>{{ $doctor->nome }}</label>
                        </div>
                        <div id="genre">
                            <label>GENÊRO:</label>
                            @if( $doctor->genero )
                                <label>Masculino</label>
                            @else
                                <label>Feminino</label>
                            @endif
                        </div>
                    </div>
                    <div class="line2">
                        <div id="email">
                            <label>EMAIL:</label>
                            <label>{{ $doctor->email }}</label>
                        </div>
                        <div id="age">
                            <label>IDADE:</label>
                            <label>{{ $doctor->dataNascimento }}</label>
                        </div>
                    </div>
                </div>
                <div class="patient-image">
                    <img src="{{ $image }}" alt="pic-profile">
                </div>
            </div>
        </div>
    </div>
    <div class="btn-voltar">
        <form action="/">
        <input class="btn-voltar" type="submit" src="/storage/" value="VOLTAR" style="margin-bottom: 20px;">
        </form>
    </div>

@endsection