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

        if(auth()->user()->doctor_id != $id){   #Requets different id
            return redirect()->action([DoctorController::class, 'index']);
        }


        return view('showDoc', ['doctor' => Doctor::find($id),'image' => Storage::disk('s3')->temporaryUrl('doctor/' . $id . '/profile.jpg', now()->addMinutes(1)) ]);
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

        // UPLOAD IMG AWS

        $fileName = $request->file('imageProfile')->getClientOriginalName();
        $filePath = 'doctor/' . $doctor->id . '/profile.jpg';


        try { 

            $doctor->save();

        } catch(\Illuminate\Database\QueryException $ex){ 
            

            return redirect()->back()->withErrors(['errors' => 'The CRO already used']);

        }

        

        $user = User::find(auth()->user()->id);
        $user->doctor_id = $doctor->id;

        $user->save();
        $path = Storage::disk('s3')->put($filePath, file_get_contents($request->file('imageProfile')));

        return redirect()->action([DoctorController::class, 'index']);
    }
}
