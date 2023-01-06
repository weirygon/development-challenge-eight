@extends('layout.main')

@section('titulo', 'Medcloud')

@section('paragrafo', 'Pacientes')

@section('scripts')
   
    <link rel="stylesheet" type="text/css" href="/css/home.css">

@endsection

@section('conteudo')

    @if(isset($doctor))

    aaaaaaaaaa
    {{ Storage::disk('s3')->url('doctor/' . $dcotor->id . '/profile.jpg') }}

        @forelse($doctor->patients as $patient)

        
        <div class="patient">

            <div class="pic-profile">
                <img src="/storage/patient/img/{{ $patient->id }}/profile.jpg" alt="pic-profile">
            </div>

            <div class="description-profile">

                <a href="/patient/{{ $patient->id }}" class="name-profile">{{ $patient->nome }}</a>
                <p> Nascimento: {{ $patient->dataNascimento }}</p>

            </div>

        </div>
        @empty
        <h2 style="text-align: center">Nenhum paciente cadastrado!</h2> 
        @endforelse

    @else
        <script>
            window.location.href = "/doctor/create";
        </script>

    @endif

@endsection