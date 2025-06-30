<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;

class MaterialController extends Controller
{
    public function index(Request $req, $courseId)
    {
        return Material::where('course_id', $courseId)
            ->orderBy('order')
            ->paginate($req->query('per_page', 10));
    }

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

    public function destroy(Material $material)
    {
        $material->delete();
        return response()->noContent();
    }
}
