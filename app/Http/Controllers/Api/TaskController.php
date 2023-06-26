<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $tasks = $user->tasks()->orderBy('id', 'desc')->paginate(10);

            return response()->json([
                'status' => 'success',
                'message' => 'All tasks fetched',
                'data' => $tasks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        try {
            $user = $request->user();
            $data = $request->validated();
            $task = Task::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => $data['status'],
                'user_id' => $user->id
            ]);

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Task created',
                    'data' => $task
                ],
                201
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            $task = $user->tasks()->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Task details fetched',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $task = auth()->user()->tasks()->find($id);

            if (!$task) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Task not found'
                ], 400);
            }

            $updated = $task->fill($data)->save();

            if ($updated) {
                return response()->json(
                    [
                        'status' => 'success',
                        'message' => 'Task updated',
                        'data' => $task
                    ],
                    200
                );
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Task cannot be updated'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $task = auth()->user()->tasks()->find($id);
            if (!$task) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Task not found'
                ], 404);
            }

            if ($task->delete()) {
                return response()->json(null, 204);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred while deleting this task',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 500);
        }
    }
}
