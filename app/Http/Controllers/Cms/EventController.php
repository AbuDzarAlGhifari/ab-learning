<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        return Event::orderBy('order')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at'    => 'nullable|date',
            'end_at'      => 'nullable|date|after_or_equal:start_at',
            'image_url'   => 'nullable|url',
            'link'        => 'nullable|url',
            'order'       => 'integer',
        ]);

        $event = Event::create($data);
        return response()->json($event, 201);
    }

    public function show(Event $event)
    {
        return $event;
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_at'    => 'nullable|date',
            'end_at'      => 'nullable|date|after_or_equal:start_at',
            'image_url'   => 'nullable|url',
            'link'        => 'nullable|url',
            'order'       => 'integer',
        ]);

        $event->update($data);
        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return response()->noContent();
    }
}
