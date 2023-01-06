<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Exam;
use App\Models\Patient;


class ExamController extends Controller
{
    public function store(Request $request){

        Carbon::setLocale('pt');

        $exam = new Exam();
        $exam->laudo = $request->laudo;
        $exam->patient_id = $request->foreignId;


        $dir = '/public/patient/img/' . $exam->patient_id;
        
        $imageName = md5($request->file('image')->getFilename().strtotime("now")).".jpg";
        Storage::putFileAs($dir, $request->file('image'), $imageName);

        $exam->img = $imageName;

        $exam->save();

        return view('show', ['patient' => Patient::with('exams')->find($exam->patient_id)]);
    }
}
