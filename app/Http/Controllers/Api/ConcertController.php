<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConcertResource;
use App\Http\Resources\LocationSeatRowResource;
use App\Http\Resources\TicketResource;
use App\Models\Booking;
use App\Models\Concert;
use App\Models\LocationSeat;
use App\Models\LocationSeatRow;
use App\Models\Reservation;
use App\Models\Show;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Psy\Util\Str;

class ConcertController extends Controller
{

    /*
        Return all concerts
    */
    public function getConcerts() {
        return ConcertResource::collection(Concert::orderBy('artist', 'asc')->get());
    }

    /*
        Return one concert by id
    */
    public function showConcert(int $concert) {
        $concert = Concert::find($concert);

        // Check if concert exists
        if(!isset($concert)) {
            return response()->json([
                'error' => 'A concert with this ID does not exist',
            ], 404);
        }
        return new ConcertResource($concert);
    }

    /*
        Returns seating information
    */
    public function seating(int $concert_id, int $show_id) {
        $show = Show::find($show_id);

        // Validate a concert and a show
        if(!isset($show) || $show->concert_id !== $concert_id) {
            return response()->json([
                'error' => 'A concert or show with this ID does not exist',
            ], 404);
        }

        // Get seat rows
        $seatsRow = LocationSeatRow::where('show_id', $show->id)->orderBy('order', 'asc')->get();
        return response()->json([
            'rows' => LocationSeatRowResource::collection($seatsRow),
        ]);
    }


    /*
        Create or update reservation
    */

    public function reservation(Request $request, int $concert_id, int $show_id) {
        $show = Show::find($show_id);

        // Validate a concert and a show
        if(!isset($show) || $show->concert_id !== $concert_id) {
            return response()->json([
                'error' => 'A concert or show with this ID does not exist',
            ], 404);
        }

        // Reservation token validation
        $token = $request->input('reservation_token');
        if(isset($token)) {
            $reservation = Reservation::where('token', $token)->first();
            if(!isset($reservation)) {
                return response()->json([
                    'error' => 'Invalid reservation token',
                ], 403);
            }
        } else {
            $token = \Illuminate\Support\Str::random(8);
        }

        // Reservations (seats) validation
        $errors = [];
        $reservations = $request->input('reservations');
        $reservationSeats = [];
        if(is_array($reservations) && count($reservations) > 0) {
            foreach ($reservations as $reservationSeat) {
//                if ($reservationSeat->)
                // Row is required validation
                if(!isset($reservationSeat['row'])) {
                    $errors['reservations'] = 'The Row field is required.';
                }
                // Seat is required validation
                if(!isset($reservationSeat['seat'])) {
                    $errors['reservations'] = 'The Seat field is required.';
                }

                // Return validation errors
                if(count($errors) > 0) {
                    return $this->outputErrors($errors);
                }

                // Seat invalid checker
                $seatRow = LocationSeat::where('location_seat_row_id', $reservationSeat['row'])->where('number', $reservationSeat['seat'])->first();
                if (!isset($seatRow)) {
                    $errors['reservations'] = 'Seat ' . $reservationSeat['seat'] .  ' in row '. $reservationSeat['row'] . ' is invalid.';
                }

                // Return validation errors
                if(count($errors) > 0) {
                    return $this->outputErrors($errors);
                }

                // TO:DO that is not working
                if (isset($seatRow->reservation_id) && isset($reservation->id) && $seatRow->reservation_id !== $reservation->id) {
                    $errors['reservations'] = 'Seat ' . $reservationSeat['seat'] .  ' in row '. $reservationSeat['row'] . ' is already taken.';
                }

                // Return validation errors
                if(count($errors) > 0) {
                    return $this->outputErrors($errors);
                }

                $reservationSeats[] = $seatRow->id;
            }
        } else {
            if (isset($reservation)) {
                LocationSeat::where('reservation_id', $reservation->id)->update(['reservation_id' => null]);
            }
        }

        // Duration validaiton
        $duration = $request->input('duration');
        if(!isset($duration)) {
            $duration = 300;
        }

        if(isset($duration) && ($duration < 1 || $duration > 300)) {
            $errors['duration'] = 'The duration must be between 1 and 300.';
            // Return validation errors
            if(count($errors) > 0) {
                return $this->outputErrors($errors);
            }
        }
        if(!isset($reservation)) {
            $reservation = Reservation::create([
               'token' => $token,
               'expires_at' => new \Carbon\Carbon(time() + $duration),
            ]);
        }

        LocationSeat::whereIn('id', $reservationSeats)->update(['reservation_id' => null]);
        LocationSeat::whereIn('id', $reservationSeats)->update(['reservation_id' => $reservation->id]);

        // Return reservation
        return response()->json([
            'reserved' => true,
            'reservation_token' => $reservation->token,
            'reserved_until' => $reservation->expires_at,
        ], 201);
    }

    /*
        Upgrades a reservation to a full ticket
    */
    public function booking(Request $request, int $concert_id, int $show_id) {
        $show = Show::find($show_id);
        $errors = [];
        // Validate a concert and a show
        if(!isset($show) || $show->concert_id !== $concert_id) {
            return response()->json([
                'error' => 'A concert or show with this ID does not exist',
            ], 404);
        }

        // Validate inputs
        $validation = [
            'reservation_token' => $request->input('reservation_token'),
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'zip' => $request->input('zip'),
            'country' => $request->input('country'),
        ];

        foreach ($validation as $key => $value) {
          if (!isset($value)) {
              $errors[$key] = 'The ' . $key . ' is required.';
              continue ;
          }

            if (!is_string($value)) {
                $errors[$key] = 'The ' . $key . ' must be a string.';
                continue ;
            }
        };

        // Return validation errors
        if(count($errors) > 0) {
            return $this->outputErrors($errors);
        }

        // Validate token
        $reservation = Reservation::where('token', $validation['reservation_token'])->first();
        if(!isset($reservation)) {
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }
        $booking = Booking::create($validation);
        $tickets =  $reservation->seats->map(function ($seat) use ($booking) {
            $ticket = Ticket::create([
                'code' => \Illuminate\Support\Str::random(10),
                'booking_id' => $booking->id,
                'created_at' => new \Carbon\Carbon(),
            ]);
            $seat->ticket_id = $ticket->id;
            $seat->save();
            return $ticket;
        });

        return response()->json([
            'tickets' => TicketResource::collection($tickets),
        ], 201);

    }


    private function outputErrors($errors) {
        return response()->json([
            'error' => 'Validation failed',
            'fields' => $errors,
        ], 422);
    }
}
