<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        return Section::orderBy('order')->get();
    }
    public function store(Request $r)
    {
        $data = $r->validate([
            'key' => 'required|string|unique:sections',
            'title' => 'nullable|string',
            'content' => 'nullable|string',
            'image_url' => 'nullable|string',
            'order' => 'integer'
        ]);
        return response()->json(Section::create($data), 201);
    }
    public function show(Section $section)
    {
        return $section;
    }
    public function update(Request $r, Section $section)
    {
        $data = $r->validate([
            'title' => 'nullable|string',
            'content' => 'nullable|string',
            'image_url' => 'nullable|string',
            'order' => 'integer'
        ]);
        $section->update($data);
        return $section;
    }
    public function destroy(Section $section)
    {
        $section->delete();
        return response()->noContent();
    }
}
