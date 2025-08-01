<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        Request::validate([
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
        ]);
         $user = Auth::user();
          $events = Event::where('user_id', $user->id)
        ->isBetween(Request::get('starts_at'), Request::get('ends_at'))
        ->orderByDate()
        ->get();
        return Inertia::render('Events/Index', [
            'starts_at' => Request::get('starts_at'),
            'ends_at' => Request::get('ends_at'),
            'events' => $events,
        ]);
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        $data['starts_at'] = Carbon::createFromFormat('Y-m-d H:i', $data['starts_at']);
        $data['ends_at'] = Carbon::createFromFormat('Y-m-d H:i', $data['ends_at']);
        $data['user_id'] = Auth::id();
        Event::create($data);

        return Redirect::back();
    }

public function update(StoreEventRequest $request, Event $event)
    {
        $this->authorize('update', $event);
         $data = $request->validated();
         $data['starts_at'] = Carbon::createFromFormat('Y-m-d H:i', $data['starts_at']);
         $data['ends_at'] = Carbon::createFromFormat('Y-m-d H:i', $data['ends_at']);

        $event->update($data);

        return Redirect::back();
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $event->delete();

        return Redirect::back();
    }
}
