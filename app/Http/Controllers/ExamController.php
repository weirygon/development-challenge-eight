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

        //UPLOAD
        $imageName = md5($request->file('imageExam')->getFilename().strtotime("now")).".jpg";
        $filePath = 'patient/' . $request->input('foreignId') .'/'. $imageName;
        
        $exam->img = $imageName;

        $exam->save();
        $path = Storage::disk('s3')->put($filePath, file_get_contents($request->file('imageExam')));

        return redirect()->back();
    }
}
