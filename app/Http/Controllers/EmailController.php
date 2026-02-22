<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\welcomeemail;

class EmailController extends Controller {
 public function sendEmail(){
    $toemail = "abdulsamad59357@gmail.com";
    $message = "Hello , You Are Hired";
    $subject = "Your Application Was Submitted ";
    $request =  Mail::to($toemail)->send(new Welcomeemail($message,$subject));
    dd($request);
}
}
 