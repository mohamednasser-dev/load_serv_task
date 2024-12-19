<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function testMail()
    {
        $old_data = Invoice::whereId(9)->first();
        $new_data = Invoice::whereId(9)->first();
        return view('mail.send_invoiceUpdates_mail',compact('old_data','new_data'));
    }
}
