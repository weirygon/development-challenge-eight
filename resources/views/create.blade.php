@extends('layout.main')

@section('titulo', 'Cadastro')

@if (request()->path() == 'doctor/create')
    @section('paragrafo', 'Cadastrar Doutor')
@else
    @section('paragrafo', 'Cadastrar Paciente')
@endif
@section('scripts')

    <link rel="stylesheet" type="text/css" href="/css/create.css">

@endsection

@section('conteudo')

<div class="cadastro-box">
    <!-- Div para cadastrar informações do paciente --->

    @if ($errors->any())
        
        <br>
        <div class="alert alert-danger">
        
            @foreach ($errors->all() as $erro)
                <p>{{ $erro }}</p>
            @endforeach

        </div>

    @endif
    
    @if (request()->path() == 'doctor/create')
        <form action="/doctor/store" method="POST" enctype="multipart/form-data" >
    @else
        <form action="/patient/store" method="POST" enctype="multipart/form-data" >

    @endif

    @if(isset($doctor))
        <input name="foreignId" type="hidden" value="{{ $doctor->id }}">
    @endif
        
        @csrf

        <div class="box-paciente">

            <div class="paciente" id="form" >

                <div class="line1">
                    <div class="name">
                        <label for="nome">NOME</label>
                        @if (request()->path() == 'doctor/create')
                            <p>{{auth()->user()->name}}</p>
                        @else
                        <input class="paciente_name" type="text" name="nome" placeholder="Nome Completo" maxlenght="30">
                        @endif
                    </div>
                    <div class="form-img" style="margin-top: 30px">
                        <label></label>
                        <input type="file" id="image" name="imageProfile" class="from-control-file">  
                    </div>
                </div>

                <div class="line2">
                    <label for="genero">GÊNERO</label>

                        <div class="line2-option">
                            <div class="form-check form-check-inline">
                                <input type="radio" name="genero" value="0" class="form-check-input">
                                <label for="feminino" class="form-check-label">Feminino</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="genero"  value="1" class="form-check-input"> 
                                <label for="masculino" class="form-check-label">Masculino</label>
                        </div>
                    </div>
                </div>
                <div class="line3">
                    <div class="esq-line3">
                        <label for="email">EMAIL</label>
                        @if (request()->path() == 'doctor/create')
                            <p>{{auth()->user()->email}}</p>
                        @else
                            <input class="paciente" type="email" name="email" placeholder="E-mail (ex: abc@email.com)" maxlenght="40">
                        @endif
                    </div>

                    <div class="dir-line3">
                        @if (request()->path() == 'doctor/create')
                            <label for="email">CRO</label>
                            <input class="paciente" type="text" name="cro" placeholder="Sem pontuação" maxlenght="5">
                        @else
                            <label for="email">CPF</label>
                            <input class="paciente" type="text" name="cpf" placeholder="Sem pontuação" maxlenght="11">
                        @endif
                        
                    </div>
                </div>
                <div class="line4">
                    <div class="esq-line4">
                        <label for="dataNascimento">DATA DE NASCIMENTO</label>
                        <input class="paciente" type="date" name="dataNascimento">
                    </div>
                    @if (request()->path() != 'doctor/create')
                    <div class="dir-line4">
                        <label for="telefone">TELEFONE</label>
                        <input class="paciente" type="text" name="telefone" placeholder="Telefone (xx xxxx-xxxx)" maxlenght="30">
                    </div>
                    @endif
                </div>

                @if (request()->path() != 'doctor/create')

                <div class="line5">
                    <div class="esq-line5">
                        <label for="inicioTratamento">INICIO DO TRATAMENTO</label>
                        <input class="paciente" type="date" name="inicioTratamento">
                    </div>
                    <div class="dir-line5">
                        <label for="previsao">PREVISÃO</label>
                        <input class="paciente" type="date" name="previsao">
                    </div>
                </div>
                @endif
            </div>
        </div>
        @if (request()->path() != 'doctor/create')
        <!-- Div para caixa de texto tratamento --->
        <div class="anamnese" method="POST">
            <div class="head-anamnese">
                <h1>ANAMESE/TRATAMENTO</h1>
                <hr />
            </div>
            <div class="text-tratamento">
                <textarea rows="8" cols="50" name="tratamento" id="comment" maxlength="2000" minlength="20"></textarea>
                <label for="tratamento">(máx. 2000 caracteres)</label>
            </div>
        </div>
        <!-- Div para envio dos exames e o laudo do profissional --->
        <div class="exame-box" method="POST" enctype="multipart/form-data">
            <div class="head-exame-box">
                <h1>ARQUIVO EXAME</h1>
                <hr />
            </div>
            <div class="right-left-exame">
                <div class="form-img">
                    <label></label>
                    <input type="file" id="imageExam" name="imageExam" class="from-control-file">  
                </div>
                <div class="laudo-exame">
                    <div class="text-laudo">
                        <h1>Laudo</h1>
                        <textarea rows="6" cols="35" name="laudo" id="comment" maxlength="2000" minlength="20"></textarea>
                        <label for="laudo">(máx. 1000 caracteres)</label>
                    </div>
                </div>
            </div>

        </div>
        
        @endif
        <div class="paciente">
            <input class="paciente" src="/" type="submit" value="CADASTRAR">
        </div>
        
    </form>
    
</div>


@endsection