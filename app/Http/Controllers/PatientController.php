<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Carbon\Carbon;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{


    public function create(){

        return view('create', ['doctor' => Doctor::find(auth()->user()->doctor_id)]);
    }

    public function show($id){
        Carbon::setLocale('pt');
        return view('show', ['patient' => Patient::with('exams')->find($id)]);
    }

    public function store(Request $request){

        $request->validate([
            'cpf' => 'required|max:11|min:11',
            'imageProfile' => 'required',
            'nome' => 'required',
            'genero' => 'required',
            'email' => 'required',
            'dataNascimento' => 'required',
            'telefone' => 'required',
            'inicioTratamento' => 'required',
            'previsao' => 'required',
            'tratamento' => 'required',
            'imageExam' => 'required',
            'laudo' => 'required'

        ]);


        $patient = new Patient();
        $patient->id = $request->input('cpf');
        $patient->nome = $request->input('nome');
        $patient->genero = $request->input('genero');
        $patient->email = $request->input('email');
        $patient->dataNascimento = $request->input('dataNascimento');
        $patient->telefone = $request->input('telefone');
        $patient->inicioTratamento = $request->input('inicioTratamento');
        $patient->previsao = $request->input('previsao');
        $patient->tratamento = $request->input('tratamento');
        $patient->doctor_id = $request->input('foreignId');

        // UPLOAD IMG
            //PROFILE
        $filePath = 'patient/' . $patient->id . '/profile.jpg';
 
        $path = Storage::disk('s3')->put($filePath, file_get_contents($request->file('imageProfile')));
       
            //FIRST EXAM
        $imageName = md5($request->file('imageExam')->getFilename().strtotime("now")).".jpg";

        $filePath = 'patient/' . $patient->id .'/'. $imageName;
        
        $path = Storage::disk('s3')->put($filePath, file_get_contents($request->file('imageExam')));

        Exam::create([
            'patient_id' => $patient->id,
            'laudo' => $request->input('laudo'),
            'img' => $imageName
        ]);

        $patient->save();

        return redirect()->action([DoctorController::class, 'index']);
    }

}