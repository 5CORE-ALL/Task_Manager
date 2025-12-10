<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\LogsTaskActivity;

class ExampleTaskController extends Controller
{
    use LogsTaskActivity;

    /**
     * Example of how to use task activity logging in your existing controllers
     */
    
    public function store(Request $request)
    {
        // Your existing task creation logic here
        $taskName = $request->input('name');
        
        // Create the task (your existing code)
        // $task = Task::create($request->all());
        
        // Log the activity
        $this->logTaskCreation($taskName, 'Task created with priority: ' . $request->input('priority'));
        
        return response()->json(['success' => 'Task created successfully']);
    }

    public function update(Request $request, $id)
    {
        // Your existing task update logic here
        // $task = Task::find($id);
        // $task->update($request->all());
        
        $taskName = $request->input('name'); // or get from existing task
        
        // Log the activity
        $this->logTaskEdit($taskName, 'Task updated with new deadline: ' . $request->input('deadline'));
        
        return response()->json(['success' => 'Task updated successfully']);
    }

    public function destroy($id)
    {
        // Your existing task deletion logic here
        // $task = Task::find($id);
        $taskName = 'Sample Task Name'; // Get this from your task model
        
        // Delete the task (your existing code)
        // $task->delete();
        
        // Log the activity
        $this->logTaskDeletion($taskName, 'Task permanently deleted');
        
        return response()->json(['success' => 'Task deleted successfully']);
    }

    public function restore($id)
    {
        // Your existing task restoration logic here
        // $task = Task::withTrashed()->find($id);
        // $task->restore();
        
        $taskName = 'Sample Task Name'; // Get this from your task model
        
        // Log the activity
        $this->logTaskRestoration($taskName, 'Task restored from deletion');
        
        return response()->json(['success' => 'Task restored successfully']);
    }
}
