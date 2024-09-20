<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class ToDoListController extends Controller
{
    /**
     * @purpose get all List
     *
     * @return void
     */
    public function index()
    {
        return view('tasks.index');
    }

    /**
     * @purpose Store the task in database
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:tasks']);
        $task = Task::create(['name' => $request->name]);
        return response()->json($task);
    }

    /**
     * @purpose Delete the task
     *
     * @param Task $task
     * @return void
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success' => true]);
    }

    /**
     * @purpose Change the task status to complete or pending
     *
     * @param Task $task
     * @return void
     */
    public function update(Task $task)
    {
        $task->status = !$task->status;
        $task->save();
        return response()->json($task);
    }

    /**
     * @purpose Show all List
     *
     * @return void
     */
    public function showAll()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }
}
