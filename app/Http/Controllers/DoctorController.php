<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Redirector;
use Carbon\Carbon;

class DoctorController extends Controller
{
    public function index(){
        Carbon::setLocale('pt');
        return view('home', ['doctor' => Doctor::find(auth()->user()->doctor_id)]);
    }

    public function create(){
        return view('create');
    }

    public function showDoc($id){
        Carbon::setLocale('pt');

        return view('showDoc', ['doctor' => Doctor::find($id)]);
    }
    public function store(Request $request)
    {

        $request->validate([
            'cro' => 'required|max:5|min:5',
            'imageProfile' => 'required',
            'genero' => 'required',
            'dataNascimento' => 'required',

        ]);


        $doctor = new Doctor();
        $doctor->id = $request->input('cro');
        $doctor->nome = auth()->user()->name;
        $doctor->genero = $request->input('genero');
        $doctor->email = auth()->user()->email;
        $doctor->dataNascimento = $request->input('dataNascimento');
        $doctor->user_id = auth()->user()->id;

        // UPLOAD IMG
        $dir = '/public/doctor/img/' . $doctor->id;
        
        Storage::putFileAs($dir, $request->file('imageProfile'), 'profile.jpg');

        $doctor->save();

        $user = User::find(auth()->user()->id);
        $user->doctor_id = $doctor->id;

        $user->save();
        
        return redirect()->action([DoctorController::class, 'index']);
    }
}
