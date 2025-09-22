<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GradeItem;

class GradeItemController extends Controller
{
    public function store(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'weight' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
        ]);
        $data['class_subject_id'] = $id;
        $gi = GradeItem::create($data);
        return response()->json($gi, 201);
    }
}

