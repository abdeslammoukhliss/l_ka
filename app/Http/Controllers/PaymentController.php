<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function addPayment(Request $request)
    {
        $fields = $request->validate([
            'student' => 'required|integer|exists:users,id',
            'course' => 'required|integer|exists:courses,id',
            'date' => 'required|date_format:format,Y-m-d',
            'amount' => 'required|numeric',
        ]);

        $student = User::where('id',$fields['student'])->first();
        if($student->role!=3)
        {
            return response(['message'=>'this user is not a student'],422);
        }

        $student_course = DB::select('select g.id from students_groups sg join `groups` g on sg.group = g.id where sg.student = ? and g.course = ?;',[$fields['student'],$fields['course']]);
        if(sizeof($student_course)==0){
            return response(['message' => 'this user is not enrolled in that course'],422);
        }

        $payment = Payment::where([['course', '=',$fields['course']],['student',$fields['student']]])->first();

        if(is_null($payment))
        {
            return response(['message'=>'this student hasn\'t enrolled to this course'],422);
        }
        $payment_detail = new PaymentDetail();
        $payment_detail->amount = $fields['amount'];
        $payment_detail->payment = $payment->id;
        $payment_detail->date = $fields['date'];
        $payment_detail->save();

        $payment->rest = $payment->rest - $fields['amount'];
        $payment->save();

        return response(['message'=>'you add a payment detail successfully']);
    }

    public function getPayments()
    {
        $result = DB::select('select u.id, u.full_name, c.name as course, p.rest, c.price, p.id as payment_id from users u join payments p on u.id = p.student join courses c on p.course = c.id;');
        foreach($result as $item)
        {
            $details = DB::select('select amount , date from payments_details where payment = ?',[$item->payment_id]);
            $item->details = $details;
        }
        return response($result);
    }

    public function getStudentRest($student_id)
    {
        $student = User::where('id',$student_id)->first();
        if(is_null($student))
        {
            return response(['message'=>'this student does not exist'],422);
        }
        if($student->role != 3)
        {
            return response(['message'=>'this user is not a student'],422);
        }
        // $result = DB::select('select c.id, p.rest from payments p join courses c on p.course = c.id where p.student = ?',[$student_id]);
        $result = Payment::where('student',$student_id)->get(['course','rest']);
        return response($result);
    }
}
