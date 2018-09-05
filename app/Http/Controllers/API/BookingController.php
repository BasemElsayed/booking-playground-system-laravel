<?php

namespace App\Http\Controllers\API;

use App\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Playground;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\User; 
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public $successStatus = 200;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [ 
            'bookedTimeFrom' => 'required', 
            'bookedTimeTo' => 'required', 
            'bookedDateFrom' => 'required', 
            'bookedDateTo' => 'required', 

        ]);

        
        if ($validator->fails()) 
        { 
            return response()->json($validator->errors(), 401);            
        }
        $mytime = Carbon::now();
        $format1 = 'Y-m-d';
        $format2 = 'H:i:s';
        $curDate = Carbon::parse($mytime)->format($format1);
        $curTime = Carbon::parse($mytime)->format($format2);

        $input = $request->all();

        if( ($request->input('bookedDateFrom') < $curDate) )
        {
            return response()->json('Sorry, Early Date.', 401);
        }

        if( ($request->input('bookedDateFrom') == $curDate) && ($request->input('bookedTimeFrom') < $curTime) )
            return response()->json('Sorry, Early Time.' , 401);

        if($request->input('bookedDateFrom') > $request->input('bookedDateTo'))
            return response()->json('Sorry, To Date Before From Date.' , 401);

        if( ($request->input('bookedDateFrom') == $request->input('bookedDateTo')) && ($request->input('bookedTimeFrom') >= $request->input('bookedTimeTo')) )
            return response()->json('Sorry, To Time Before From Time.' , 401);

            $bookingToTimeStr = $request->input('bookedTimeTo');
            $bookingTimeStr = $request->input('bookedTimeFrom');
            $bookingDateStr = $request->input('bookedDateFrom');
            $bookingToTime = Carbon::parse($bookingToTimeStr);
            $bookingTime = Carbon::parse($bookingTimeStr);
            $bookingDate = Carbon::parse($bookingDateStr)->format($format1);
        $HalfAnHour = $bookingTime->diffInSeconds($bookingToTime);
        if(($request->input('bookedDateFrom') == $request->input('bookedDateTo')) && $HalfAnHour < 1800)
            return response()->json('Sorry, minimum half an hour.' , 401);

       
    
        $totalDurationSameDay = $bookingTime->diffInSeconds($curTime);
        $last = Carbon::parse('23:59:59');
        $lastTime = Carbon::parse($last);

        $pa = Carbon::parse('00:00:00');
        $laspa = Carbon::parse($pa);
    
        $a=$lastTime->diffInSeconds($curTime);
        $result = $bookingTime->diffInSeconds($laspa);
        if($bookingDate == $curDate)
        {
            if($totalDurationSameDay < 21600)
            {
                return response()->json('Sorry, Can nott book before 6 hours', 401);
            }
        }
        else
        {
            if($a + $result < 21600)
            {
                return response()->json('Sorry, Can nott book before 6 hours', 401);
            }
        }
    
        
        $playground = Playground::find($request->input('playground_id'));

        if($playground)
        {
            if($playground->avaiableFrom > $request->input('bookedTimeFrom') || $playground->avaiableTo < $request->input('bookedTimeTo') )
            {
                return response()->json('Sorry, Not Available Time.' , 401);
            }
        }
        else
        {
            return response()->json('Sorry, Wrong Playground.', 401);
        }

        $bookings = Booking::where('playground_id', $playground->id )->get();
        foreach($bookings as $booking)
        {
            if($booking->bookedDateFrom == $request->input('bookedDateFrom') && $booking->bookedDateTo == $request->input('bookedDateTo'))
            {
                if(($booking->bookedTimeFrom <= $request->input('bookedTimeFrom')&&$booking->bookedTimeTo > $request->input('bookedTimeFrom')) || 
                ($booking->bookedTimeFrom < $request->input('bookedTimeTo')&&$booking->bookedTimeTo >= $request->input('bookedTimeTo')) )
                {
                    return response()->json( 'Sorry, Not Available Time.', 401);
                }
            } 
        }

        $input = $request->all();
        $booking = Booking::create($input); 
        $success['ID'] =  $booking->id;
        return response()->json($success, $this-> successStatus); 

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bookingnd  $bookingnd
     * @return \Illuminate\Http\Response
     */
    public function showPerUser()
    {
        //
        $user = Auth::user(); 
        
        //$bookings = Booking::where('user_id', $user->id )->get();
        $bookings = DB::table('playgrounds')
        ->join('bookings', function($join) use($user)
        {
            $join->on('bookings.playground_id', '=', 'playgrounds.id')
            ->where('bookings.user_id', '=', $user->id)
            ->select('bookings.id', 'bookings.playground_id', 'bookings.user_id');
        })->get();
        
        return response()->json($bookings, $this-> successStatus); 
    }


    public function approve($id)
    {
        
        $booking = Booking::findOrFail($id);
        $booking->update([

            'approved' => 1,

        ]);
        
        return $booking;

    }


    public function viewUnapproved()
    {
        $bookings = DB::table('playgrounds')
        ->join('bookings', function($join)
        {
            $join->on('bookings.playground_id', '=', 'playgrounds.id')
            ->where('bookings.approved', '=', 0);
        })->get();
        
        return response()->json($bookings, $this-> successStatus);
    }

    
    public function viewAll()
    {
        //
        $bookings = Booking::all();
        return response()->json($bookings, $this-> successStatus);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bookingnd  $bookingnd
     * @return \Illuminate\Http\Response
     */
    public function edit(Bookingnd $bookingnd)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bookingnd  $bookingnd
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bookingnd $bookingnd)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bookingnd  $bookingnd
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $booking = Booking::find($id);
        $mytime = Carbon::now();
        $format1 = 'Y-m-d';
        $format2 = 'H:i:s';
        $curDate = Carbon::parse($mytime)->format($format1);
        $curTime = Carbon::parse($mytime)->format($format2);
        $bookingTimeStr = $booking->bookedTimeFrom;
        $bookingDateStr = $booking->bookedDateFrom;
        $bookingTime = Carbon::parse($bookingTimeStr);
        $bookingDate = Carbon::parse($bookingDateStr)->format($format1);

        $totalDurationSameDay = $bookingTime->diffInSeconds($curTime);
        $last = Carbon::parse('23:59:59');
        $lastTime = Carbon::parse($last);

        $pa = Carbon::parse('00:00:00');
        $laspa = Carbon::parse($pa);

        $a=$lastTime->diffInSeconds($curTime);
        $result = $bookingTime->diffInSeconds($laspa);
       if($bookingDate == $curDate)
        {
            if($totalDurationSameDay < 21600)
            {
                return response()->json(["error" => "Sorry, Can not delete booking"], 401);
            }
        }
        else
        {
            if($a + $result < 21600)
            {
                return response()->json(["error" => 'Sorry, Can not delete booking'], 401);
            }
        }

        //DELETE COMMAND
        return response()->json(["error" => 'Deleted Successfully'], $this-> successStatus);
    }
}
