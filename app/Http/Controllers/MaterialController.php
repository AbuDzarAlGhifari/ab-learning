<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;

class MaterialController extends Controller
{
    // Teacher only: index materials for a course
    public function index($courseId)
    {
        return Material::where('course_id', $courseId)
            ->orderBy('order')
            ->get();
    }

    // Teacher only: store new material
    public function store(Request $req, $courseId)
    {
        $data = $req->validate([
            'title'     => 'required|string',
            'content'   => 'required|string',
            'video_url' => 'nullable|url',
            'order'     => 'integer',
        ]);
        $data['course_id'] = $courseId;
        $material = Material::create($data);
        return response()->json($material, 201);
    }

    // Teacher only: update material
    public function update(Request $req, Material $material)
    {
        $data = $req->validate([
            'title'     => 'sometimes|required|string',
            'content'   => 'sometimes|required|string',
            'video_url' => 'nullable|url',
            'order'     => 'integer',
        ]);
        $material->update($data);
        return response()->json($material);
    }

    // Teacher only: delete material
    public function destroy(Material $material)
    {
        $material->delete();
        return response()->noContent();
    }
}
