<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {

        return view('home');
    }

    public function booking()
    {

        return view('booking');
    }

    public function createBooking(Request $request)
    {
        Log::info(" create booking request:" . json_encode($request->all()));
        
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'checkin' => 'required|date',
            'checkout' => 'required|date',
            'no_of_members' => 'required|integer',
            'message' => 'nullable|string',
        ]);

        try {
            // Create a new booking
            $booking = new Booking();
            $booking->name = $request->input('name');
            $booking->phone = $request->input('phone');
            $booking->checkin = $request->input('checkin');
            $booking->checkout = $request->input('checkout');
            $booking->no_of_members = $request->input('no_of_members');
            $booking->message = $request->input('message');
            $booking->save();
            Log::info(" create booking success:" . json_encode($booking));
        } catch (\Throwable $th) {
            Log::info(" create booking error :" . $th->getMessage());
            return redirect()->back()->with('status', 'Booking fialed!');
        }



        // Redirect back with a success message or handle errors
        return redirect()->back()->with('status', 'Booking successful!');
    }
}
