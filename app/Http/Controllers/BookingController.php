<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BookingController extends Controller
{
    // constants for halves
    private function halfRanges() {
        return [
            'first_half'  => ['00:00:00', '11:59:59'],
            'second_half' => ['12:00:00', '23:59:59'],
        ];
    }

    // map booking_type + slot/custom times to start/end
    private function resolveTimes(array $data): array
{
    // Full day: covers entire day
    if ($data['booking_type'] === 'full_day') {
        return ['00:00:00', '23:59:59'];
    }

    // Half day: first_half or second_half
    if ($data['booking_type'] === 'half_day') {
        $ranges = [
            'first_half'  => ['00:00:00', '11:59:59'],  // ends just before 12:00
            'second_half' => ['12:00:00', '23:59:59'],  // starts exactly at 12:00
        ];
        $slot = $data['slot'] ?? null;
        if (!isset($ranges[$slot])) {
            throw new \InvalidArgumentException('Invalid half-day slot');
        }
        return $ranges[$slot];
    }

    // Custom: user-defined times
    if ($data['booking_type'] === 'custom') {
        $from = Carbon::parse($data['from_time'])->format('H:i:s');
        $to   = Carbon::parse($data['to_time'])->format('H:i:s');

        if (strtotime($to) <= strtotime($from)) {
            throw new \InvalidArgumentException('Custom booking end time must be after start time');
        }

        // Prevent overlapping exactly with half-day boundaries
        // Adjust to avoid booking ending exactly at 12:00 being counted in both halves
        if ($from === '12:00:00') {
            $from = '12:00:01';
        }
        if ($to === '12:00:00') {
            $to = '11:59:59';
        }

        return [$from, $to];
    }

    throw new \InvalidArgumentException('Invalid booking type');
}



    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:150',
            'customer_email' => 'required|email|max:150',
            'booking_date'   => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'booking_type'   => ['required', Rule::in(['full_day','half_day','custom'])],
            'slot'           => 'required_if:booking_type,half_day|nullable|in:first_half,second_half',
            'from_time'      => 'required_if:booking_type,custom|date_format:H:i|nullable',
            'to_time'        => 'required_if:booking_type,custom|date_format:H:i|nullable',
        ]);

        $payload = $request->only([
            'customer_name','customer_email','booking_date','booking_type','slot','from_time','to_time'
        ]);

        [$start, $end] = $this->resolveTimes($payload);
        $start = Carbon::parse($start)->format('H:i:s');
        $end   = Carbon::parse($end)->format('H:i:s');

        if (strtotime($end) <= strtotime($start)) {
            return response()->json([
                'errors' => ['to_time' => ['End time must be after start time']]
            ], 422);
        }

        $date = $payload['booking_date'];

        return DB::transaction(function () use ($date, $start, $end, $payload) {

            $existingBookings = Booking::where('booking_date', $date)
                ->lockForUpdate()
                ->get(['start_time','end_time','booking_type','slot']);

            // Check for overlaps
            foreach ($existingBookings as $booking) {

                $existingStart = strtotime($booking->start_time);
                $existingEnd   = strtotime($booking->end_time);
                $newStart      = strtotime($start);
                $newEnd        = strtotime($end);

                if (!($newEnd <= $existingStart || $newStart >= $existingEnd)) {
                    return response()->json([
                        'message' => 'Requested slot overlaps with existing booking'
                    ], 409);
                }
            }

            // Create booking
            $booking = Booking::create([
                'booking_date'   => $date,
                'start_time'     => $start,
                'end_time'       => $end,
                'customer_name'  => $payload['customer_name'],
                'customer_email' => $payload['customer_email'],
                'booking_type'   => $payload['booking_type'],
                'slot'           => $payload['slot'] ?? null,
            ]);

            return response()->json([
                'message' => 'Booking created',
                'booking' => $booking
            ], 201);
        });
    }

}
