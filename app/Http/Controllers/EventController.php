<?php

namespace App\Http\Controllers;

use App\Events\EventMeeting;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;
class EventController extends Controller
{
    public function create(Request $request){
        if($request->ajax()) {

            $data = Event::whereDate('start', '>=', $request->start)
                      ->whereDate('end',   '<=', $request->end)
                      ->get(['id', 'title', 'start', 'end']);

            return response()->json($data);
       }
        return view('frontend.calender');
    }
    public function store(Request $request)
    {
        switch ($request->type) {
            case 'add':
                $event = Event::create([
                    'title' => $request->title,
                    'start' => $request->start,
                    'end' => $request->end,
                ]);
                broadcast(new EventMeeting($event));
                return response()->json($event);
                break;

          case 'update':
            $event = Event::find($request->id);
            if (!$event) {
                return response()->json(['message' => 'Event not found'], 404);
            }

            // Convert the datetime to the correct format
            $start = Carbon::parse($request->start)->format('Y-m-d H:i:s');
            $end = Carbon::parse($request->end)->format('Y-m-d H:i:s');

            $event->update([
                'title' => $request->title,
                'start' => $start,
                'end' => $end,
            ]);

            broadcast(new EventMeeting($event));

            return response()->json($event);
            break;


            case 'delete':
                $event = Event::find($request->id);
                if (!$event) {
                    return response()->json(['message' => 'Event not found'], 404);
                }

                $event->delete();
                broadcast(new EventMeeting($event));
                return response()->json(['message' => 'Event deleted successfully']);
                break;

            default:
                return response()->json(['message' => 'Invalid request type'], 400);
                break;
        }
    }

}
