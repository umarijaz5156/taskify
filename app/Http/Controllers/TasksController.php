<?php

namespace App\Http\Controllers;

use PDO;
use App\Models\Task;
use App\Models\User;
use App\Models\Client;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\Status;
use App\Models\Project;
use App\Models\TaskChat;
use App\Models\Workspace;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Services\DeletionService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Request as FacadesRequest;

class TasksController extends Controller
{
    protected $workspace;
    protected $user;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // fetch session and use it in entire class with constructor
            $this->workspace = Workspace::find(session()->get('workspace_id'));
            $this->user = getAuthenticatedUser();
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id = '')
    {
        $project = (object)[];
        if ($id) {
            $project = Project::findOrFail($id);
            $tasks = $project->tasks;
        } else {
            $tasks = $this->user->hasRole(['admin', 'Project Manager']) ? $this->workspace->tasks : $this->user->tasks();
        }

        $tasks = $tasks->count();
        $users = $this->workspace->users;
        $clients = $this->workspace->clients;
        $projects = $this->user->hasRole('admin') ? $this->workspace->projects : $this->user->projects;
        return view('tasks.tasks', ['project' => $project, 'tasks' => $tasks, 'users' => $users, 'clients' => $clients, 'projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id = '')
    {
        $project = (object)[];
        $projects = [];
        if ($id) {
            $project = Project::find($id);
            $users = $project->users;
        } else {
            $projects = $this->user->hasRole('admin') ? $this->workspace->projects : $this->user->projects;
            $users = $this->workspace->users;
        }

        $client = $this->user->hasRole('Client') ?? false;

        if($this->user->hasRole('admin')){
              return view('tasks.create_task', ['project' => $project, 'projects' => $projects, 'users' => $users, 'client' => $client]);
        }

        if($client == false){
            return view('tasks.create_task', ['project' => $project, 'projects' => $projects, 'users' => $users, 'client' => $client]);

        }

        $plan = Plan::findOrFail($this->user->plan_id);
        $tasks = Task::where('created_by', $this->user->id)->where('status_id', '!=', 2)->get();
        if ($plan->tasks_per_project > count($tasks)) {

            return view('tasks.create_task', ['project' => $project, 'projects' => $projects, 'users' => $users, 'client' => $client]);
        } else {
            return redirect()->back()->with('error', 'You have reached the maximum number of tasks per project allowed for your plan.');
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $formFields = $request->validate([
            'title' => ['required'],
            'status_id' => ['required'],
            'start_date' => ['required', 'before_or_equal:due_date'],
            'due_date' => ['required'],
            'description' => ['required'],
            'project' => ['required']
        ]);
        $project_id = $request->input('project');


        $formFields['task_department'] = $request->input('task_department');

        $start_date = $request->input('start_date');
        $due_date = $request->input('due_date');
        $formFields['start_date'] = format_date($start_date, null, "Y-m-d");
        $formFields['due_date'] = format_date($due_date, null, "Y-m-d");

        $formFields['workspace_id'] = $this->workspace->id;

        $formFields['project_id'] = $project_id;
        $project = Project::findOrfail($request->project);
        $projectManagers = $project->users()->whereHas('roles', function ($query) {
            $query->where('name', 'Project Manager');
        })->get();
        $userIds = $request->input('user_id');
       $projectManagersIds = $projectManagers ? $projectManagers->pluck('id')->toArray() : [];
       if($projectManagersIds){
                if($userIds == null){
                    $userIds = [];
                }
               $userIds = array_merge($userIds, $projectManagersIds);
             $userIds = array_unique($userIds);
       }
       if($userIds == null){
        $userIds = [];
    }     

       $project = Project::findOrFail($project_id);
         $clientUser = Client::findOrFail($project->created_by);

         $plan = Plan::findOrFail($clientUser->plan_id);
        $tasks = Task::where('created_by', $project->created_by)->where('status_id', '!=', 2)->get();
          if (count($tasks) >= $plan->tasks_per_project) {

            Session::flash('error', 'Your client have reached the maximum number of tasks per project allowed his your plan.');
        return response()->json(['error' => false]);
           
        }


        $formFields['created_by'] = $project->created_by;

        $new_task = Task::create($formFields);
        $task_id = $new_task->id;
        $task = Task::find($task_id);
        $task->users()->attach($userIds);

        $adminUserIds = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->pluck('id')->toArray();
        
        $relatedUserIds = $userIds;
        $relatedUserIds = array_merge($relatedUserIds, $adminUserIds);
        $relatedUserIds[] = $task->created_by; // Add the created_by value to the array
        $authUserId = $this->user->id;
        $relatedUserIds = array_diff($relatedUserIds, [$authUserId]);
            $taskTitle = $task->title;
            $authUserName =   $this->user->first_name . ' ' . $this->user->last_name;
        foreach ($relatedUserIds as $userId) {
            $notification = new Notification();
            $notification->action_id = $task->id;
            $notification->notification_type = 'Task'; 
            $notification->from_id = $authUserId; 
            $notification->to_id = $userId;
            $notification->message = "Task '$taskTitle'($task_id) has been created by $authUserName.";
            $notification->save();
        }


        Session::flash('message', 'Task created successfully.');
        return response()->json(['error' => false]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::with('users', 'project')->findOrFail($id);
        $messages = TaskChat::where('task_id', $id)->get();
        $user = $this->user;
        return view('tasks.task_information', ['task' => $task, 'messages' => $messages, 'user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $project = $task->project;
        // $users = $task->project->users;
        $users = $this->workspace->users;

        $task_users = $task->users;
        return view('tasks.update_task', ["project" => $project, "task" => $task, "users" => $users, "task_users" => $task_users]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $formFields = $request->validate([
            'title' => ['required'],
            'status_id' => ['required'],
            'start_date' => ['required', 'before_or_equal:due_date'],
            'due_date' => ['required'],
            'description' => ['required'],
        ]);

        $task = Task::findOrFail($id);

        $formFields['task_department'] = $request->input('task_department');

        $start_date = $request->input('start_date');
        $due_date = $request->input('due_date');
        $formFields['start_date'] = format_date($start_date, null, "Y-m-d");
        $formFields['due_date'] = format_date($due_date, null, "Y-m-d");

        $project = Project::findOrfail($task->project_id);
        $projectManagers = $project->users()->whereHas('roles', function ($query) {
            $query->where('name', 'Project Manager');
        })->get();
        $userIds = $request->input('user_id');
        if($userIds == null) {
            $userIds = [];
        }
        $projectManagersIds = $projectManagers->pluck('id')->toArray();

        $userIds = array_merge($userIds, $projectManagersIds);
        $userIds = array_unique($userIds);
        
        $states = Status::where('title', 'Progress')->first();
        $oldUsers = $task->users->pluck('id')->toArray();
        
        // Check if there are differences between old and new user IDs
        $difference = array_diff($userIds, $oldUsers);

        // Check if status_id has changed
        $statusChanged = $request->status_id != $task->status_id;
        
        if (empty($difference) && !$statusChanged) {
            // No differences in user IDs and status_id not changed, no action needed
            $formFields['status_id'] = $request->status_id;
        } else {
            // Differences found in user IDs or status_id changed, send notifications
            $formFields['status_id'] = $request->status_id;
        
            // Send notifications for differences in user IDs
            foreach ($difference as $userId) {
                $notification = new Notification();
                $notification->action_id = $task->id;
                $notification->notification_type = 'Task'; 
                $notification->from_id = $this->user->id; 
                $notification->to_id = $userId;
                $notification->message = "You have been assigned to the task '$task->title'($task->id).";
                $notification->save();
            }
        
            // Send notifications if status_id changed
            if ($statusChanged) {
                $NewStates = Status::findOrFail($request->status_id);
                $relatedUserIds = array_merge($userIds, [$task->created_by]);
                $relatedUserIds = array_diff($relatedUserIds, [$this->user->id]);
                $authUserName = $this->user->first_name . ' ' . $this->user->last_name;
                foreach ($relatedUserIds as $userId) {
                    $notification = new Notification();
                    $notification->action_id = $task->id;
                    $notification->notification_type = 'Task'; 
                    $notification->from_id = $this->user->id; 
                    $notification->to_id = $userId;
                    $notification->message = "Task '$task->title'($task->id) status has changed to '$NewStates->title' by $authUserName.";
                    $notification->save();
                }
            }
        }
        
        // else{
        //     if (collect($userIds)->diff($projectManagersIds)->isNotEmpty()) {
        //         if($request->status_id == $task->status_id){
    
        //             $formFields['status_id'] = $request->status_id;
    
        //         }else{
        //             $formFields['status_id'] = $states->id;
    
        //         }
    
        //         $adminUserIds = User::whereHas('roles', function ($query) {
        //             $query->where('name', 'admin');
        //         })->pluck('id')->toArray();
                
        //         $relatedUserIds = $userIds;
        //         $relatedUserIds = array_merge($relatedUserIds, $adminUserIds);
        //         $relatedUserIds[] = $task->created_by; // Add the created_by value to the array
        //         $authUserId = $this->user->id;
        //         $relatedUserIds = array_diff($relatedUserIds, [$authUserId]);
        //             $taskTitle = $task->title;
        //             $authUserName =   $this->user->first_name . ' ' . $this->user->last_name;
        //         foreach ($relatedUserIds as $userId) {
        //             $notification = new Notification();
        //             $notification->action_id = $task->id;
        //             $notification->notification_type = 'Task'; 
        //             $notification->from_id = $authUserId; 
        //             $notification->to_id = $userId;
        //             $notification->message = "Task '$taskTitle'($id) Status is change with Progress by $authUserName.";
        //             $notification->save();
        //         }
            
        //     }
        // }

      

        $task->save();



        $task->update($formFields);
        $task->users()->sync($userIds);

        Session::flash('message', 'Task updated successfully.');
        return response()->json(['error' => false]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = DeletionService::delete(Task::class, $id, 'Task');
        return $response;
    }

    public function destroy_multiple(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:tasks,id' // Ensure each ID in 'ids' is an integer and exists in the table
        ]);

        $ids = $validatedData['ids'];

        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            DeletionService::delete(Task::class, $id, 'Task');
        }

        return response()->json(['error' => false, 'message' => 'Task(s) deleted successfully.']);
    }


    public function list($id = '')
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $status = isset($_REQUEST['status']) && $_REQUEST['status'] !== '' ? $_REQUEST['status'] : "";
        $user_id = (request('user_id')) ? request('user_id') : "";
        $client_id = (request('client_id')) ? request('client_id') : "";
        $project_id = (request('project_id')) ? request('project_id') : "";
        $start_date_from = (request('task_start_date_from')) ? request('task_start_date_from') : "";
        $start_date_to = (request('task_start_date_to')) ? request('task_start_date_to') : "";
        $end_date_from = (request('task_end_date_from')) ? request('task_end_date_from') : "";
        $end_date_to = (request('task_end_date_to')) ? request('task_end_date_to') : "";
        $where = [];
        if ($status != '') {
            $where['status_id'] = $status;
        }
        if ($id) {
            $id = explode('_', $id);
            $belongs_to = $id[0];
            $belongs_to_id = $id[1];
            if ($belongs_to == 'project') {
                $belongs_to = Project::find($belongs_to_id);
            }
            if ($belongs_to == 'user') {
                $belongs_to = User::find($belongs_to_id);
            }
            if ($belongs_to == 'client') {
                $belongs_to = Client::find($belongs_to_id);
            }
            $tasks = $belongs_to->tasks();
        } else {
            $tasks = $this->user->hasRole('admin') ? $this->workspace->tasks() : $this->user->tasks();
        }
        if ($user_id) {
            $user = User::find($user_id);
            $tasks = $user->tasks();
        }
        if ($client_id) {
            $client = Client::find($client_id);
            $tasks = $client->tasks();
        }
        if ($project_id) {
            $where['project_id'] = $project_id;
        }
        if ($start_date_from && $start_date_to) {
            $tasks->whereBetween('start_date', [$start_date_from, $start_date_to]);
        }
        if ($end_date_from && $end_date_to) {
            $tasks->whereBetween('due_date', [$end_date_from, $end_date_to]);
        }
        if ($search) {
            $tasks = $tasks->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            });
        }
        $tasks->where($where);
        $totaltasks = $tasks->count();

        $tasks = $tasks->orderBy($sort, $order)
            ->paginate(request("limit"))
            ->through(
                fn ($task) => [
                    'id' => $task->id,
                    'title' => "<a href='/tasks/information/" . $task->id . "' target='_blank'><strong>" . $task->title . "</strong></a>",
                    'project_id' => "<a href='/projects/information/" . $task->project->id . "' target='_blank'><strong>" . $task->project->title . "</strong></a> <a href='javascript:void(0);' class='mx-2'><i class='bx " . ($task->project->is_favorite ? 'bxs' : 'bx') . "-star favorite-icon text-warning' data-favorite=" . $task->project->is_favorite . " data-id=" . $task->project->id . " title='" . ($task->project->is_favorite ? get_label('remove_favorite', 'Click to remove from favorite') : get_label('add_favorite', 'Click to mark as favorite')) . "'></i></a>",
                    'users' => $task->users,
                    'clients' => $task->project->clients,
                    'start_date' => format_date($task->start_date),
                    'end_date' => format_date($task->end_date),
                    // 'status' => "<span class='badge bg-label-" . config('taskhub.task_status_labels')[$task->status] . " me-1'>" . $task->status . "</span>",
                    'status_id' => "<span class='badge bg-label-" . $task->status->color . " me-1'>" . $task->status->title . "</span>",
                ]
            );

        foreach ($tasks->items() as $task => $collection) {
            foreach ($collection['users'] as $i => $user) {
                $collection['users'][$i] = "<a href='/users/profile/" . $user->id . "' target='_blank'><li class='avatar avatar-sm pull-up'  title='" . $user['first_name'] . " " . $user['last_name'] . "'>
                <img src='" . ($user['photo'] ? asset('storage/' . $user['photo']) : asset('storage/photos/no-image.jpg')) . "' class='rounded-circle' />
                </li></a>";
            };
        }

        foreach ($tasks->items() as $task => $collection) {
            foreach ($collection['clients'] as $i => $client) {
                $collection['clients'][$i] = "<a href='/clients/profile/" . $client->id . "' target='_blank'><li class='avatar avatar-sm pull-up'  title='" . $client['first_name'] . " " . $client['last_name'] . "'>
                <img src='" . ($client['photo'] ? asset('storage/' . $client['photo']) : asset('storage/photos/no-image.jpg')) . "' alt='Avatar' class='rounded-circle' />
                </li></a>";
            };
        }

        return response()->json([
            "rows" => $tasks->items(),
            "total" => $totaltasks,
        ]);
    }

    public function dragula($id = '')
    {
        $project = (object)[];
        if ($id) {
            $project = Project::findOrFail($id);

            if($this->user->hasRole('Project Manager')){
                $tasks = $this->user->hasRole('admin') ? $project->tasks : $this->user->project_tasks($id)->get();
                $total_tasks = $tasks->count();

            }else{
                $tasks = $this->user->hasRole('admin') ? $project->tasks : $this->user->project_tasks($id)->all();
                $total_tasks = count($tasks);

            }
            // $tasks = $this->user->hasRole('admin') ? 
            $this->user->project_tasks($id); // Remove the get() method call;

        } else {
            $tasks = $this->user->hasRole('admin') ? $this->workspace->tasks : $this->user->tasks()->get();
            $total_tasks = $tasks->count();

        }
        

        return view('tasks.board_view', ['project' => $project, 'tasks' => $tasks, 'total_tasks' => $total_tasks]);
    }

    public function updateStatus($id, $newStatus)
    {
        $task = Task::findOrFail($id);
        $task->status_id = $newStatus;
        if ($task->save())
            return response()->json(['error' => false, 'message' => 'Task status updated successfully!']);
        else
            return response()->json(['error' => true, 'message' => 'Task status couldn\'t updated!']);
    }

    public function duplicate($id)
    {
        // Define the related tables for this meeting
        $relatedTables = ['users']; // Include related tables as needed

        // Use the general duplicateRecord function
        $duplicate = duplicateRecord(Task::class, $id, $relatedTables);

        if (!$duplicate) {
            if (request()->has('reload') && request()->input('reload') === 'true') {
                Session::flash('error', 'Task duplication failed.');
            }
            return response()->json(['error' => true, 'message' => 'Task duplication failed.']);
        }
        if (request()->has('reload') && request()->input('reload') === 'true') {
            Session::flash('message', 'Task duplicated successfully.');
        }
        return response()->json(['error' => false, 'message' => 'Task duplicated successfully.']);
    }


    public function task_chat(Request $request,$id){

        $validatedData = $request->validate([
            'message' => 'required|string',
            'attachment.*' => 'nullable|file|max:10048|mimes:jpeg,jpg,png,gif,pdf,doc,docx,xls,xlsx',
        ]);
       
        $adminUserIds = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->pluck('id')->toArray();
        $chatMessage = new TaskChat();
        $chatMessage->task_id = $id;
        $chatMessage->message = $validatedData['message'];

          $role = $this->user->roles[0]->name;

          if($role == 'Client'){
            $chatMessage->client_id = $this->user->id;
          }else{
            $chatMessage->user_id = $this->user->id;
          }

        $attachments = [];

            if ($request->hasFile('attachment')) {

                foreach ($request->file('attachment') as $file) {
                    $fileName = $file->getClientOriginalName();
                    $path = $file->storeAs('attachments', $fileName, 'public');
                    $attachments[] = $path;
                }
            }
                
            $attachmentPaths = implode(',', $attachments);

            $chatMessage->attachment = $attachmentPaths;
            $chatMessage->save();
     
            $task = Task::findOrFail($id);
            $relatedUserIds = $task->users->pluck('id')->merge([$task->created_by])->unique()->toArray();
            $relatedUserIds = array_merge($relatedUserIds, $adminUserIds);

            $authUserId = $this->user->id;
            $relatedUserIds = array_diff($relatedUserIds, [$authUserId]);
                $taskTitle = $task->title;
                $authUserName =   $this->user->first_name . ' ' . $this->user->last_name;
            foreach ($relatedUserIds as $userId) {
                $notification = new Notification();
                $notification->action_id = $task->id;
                $notification->notification_type = 'Task'; 
                $notification->from_id = $authUserId; 
                $notification->to_id = $userId;
                $notification->message = "New message related to task '$taskTitle'($id) from $authUserName.";
                $notification->save();
            }
        
    
        return redirect()->back()->with('success', 'Chat message sent successfully!');
        
    }

    public function notificationRead($id){

        $notification = Notification::findOrFail($id);
        $notification->is_read = 1;
        $notification->save();
        return true;
    }

    public function openStatus(Request $request,$id){

        $adminUserIds = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->pluck('id')->toArray();
        if($request->open_task == 'on'){
            
            if($this->user->hasRole('admin')){
                $task=Task::findOrFail($id);
                  $user = Client::findOrFail($task->created_by);
                  $plan = Plan::findOrFail($user->plan_id);
                  $tasks = Task::where('created_by', $user->id)->where('status_id', '!=', 2)->get();
            }else{
                $plan = Plan::findOrFail($this->user->plan_id);
                $tasks = Task::where('created_by', $this->user->id)->where('status_id', '!=', 2)->get();
            }
             
        if ($plan->tasks_per_project > count($tasks)) {

            $states = Status::where('title','Progress')->first();
            $task=Task::findOrFail($id);
            $task->status_id = $states->id;
            $task->save();


            
            $relatedUserIds = $task->users->pluck('id')->merge([$task->created_by])->unique()->toArray();
            $relatedUserIds = array_merge($relatedUserIds, $adminUserIds);

            $authUserId = $this->user->id;
            $relatedUserIds = array_diff($relatedUserIds, [$authUserId]);
                $taskTitle = $task->title;
                $authUserName =   $this->user->first_name . ' ' . $this->user->last_name;
            foreach ($relatedUserIds as $userId) {
                $notification = new Notification();
                $notification->action_id = $task->id;
                $notification->notification_type = 'Task'; 
                $notification->from_id = $authUserId; 
                $notification->to_id = $userId;
                $notification->message = "Task '$taskTitle'($id) has been Re-Open by $authUserName.";
                $notification->save();
            }
        

            return redirect()->back()->with('success', 'Task Re-Open successfully!');
            
        } else {
            return redirect()->back()->with('error', 'You have reached the maximum number of tasks per project allowed for your plan.');
        }
            
            
           

        }
    }


    public function task_close(Request $request,$id){

        $adminUserIds = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->pluck('id')->toArray();

        if($request->close_task == 'on'){
            $states = Status::where('title','Complete')->first();
            $task=Task::findOrFail($id);
            $task->status_id = $states->id;
            $task->feedback = $request->feedback ?? '';
            $task->save();


            $relatedUserIds = $task->users->pluck('id')->merge([$task->created_by])->unique()->toArray();
            $relatedUserIds = array_merge($relatedUserIds, $adminUserIds);

            $authUserId = $this->user->id;
            $relatedUserIds = array_diff($relatedUserIds, [$authUserId]);
                $taskTitle = $task->title;
                $authUserName =   $this->user->first_name . ' ' . $this->user->last_name;
            foreach ($relatedUserIds as $userId) {
                $notification = new Notification();
                $notification->action_id = $task->id;
                $notification->notification_type = 'Task'; 
                $notification->from_id = $authUserId; 
                $notification->to_id = $userId;
                $notification->message = "Task '$taskTitle'($id) has been closed by $authUserName.";
                $notification->save();
            }
        


            return redirect()->back()->with('success', 'Task Close successfully!');

        }
    }
}
