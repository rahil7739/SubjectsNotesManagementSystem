<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $subjects = $request->user()->role === RoleEnum::Admin
            ? Subject::with('user')->withCount('notes')->latest()->get()
            : $request->user()->subjects()->withCount('notes')->latest()->get();

        return response()->json(['data' => $subjects]);
    }

    public function store(Request $request): JsonResponse
    {
        // dd("vcvvcvcvv");

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
        ]);
        $userId = $request->user()->role === RoleEnum::Admin
            ? ($data['user_id'] ?? null)
            : $request->user()->id;
        abort_if($userId === null, 422, 'user_id is required for administrators.');
        $subject = Subject::create(collect($data)->only(['name', 'description'])->merge(['user_id' => $userId])->all());

        return response()->json(['data' => $subject], 201);
    }

    public function show(Request $request, int $subject): JsonResponse
    {
        $record = $this->findAccessible($request, $subject);

        return response()->json(['data' => $record->load('notes.attachments', 'user')]);
    }

    public function update(Request $request, int $subject): JsonResponse
    {
        $record = $this->findAccessible($request, $subject);
        $record->update($request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
        ]));

        return response()->json(['data' => $record->fresh()]);
    }

    public function destroy(Request $request, int $subject): JsonResponse
    {
        $this->findAccessible($request, $subject)->delete();

        return response()->json(['message' => 'Subject moved to deleted records.']);
    }

    private function findAccessible(Request $request, int $id): Subject
    {
        $query = Subject::query();
        if ($request->user()->role !== RoleEnum::Admin) {
            $query->where('user_id', $request->user()->id);
        }

        return $query->findOrFail($id);
    }
}
