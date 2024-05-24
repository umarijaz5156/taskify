<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Status;
use App\Models\Project;
use App\Models\Workspace;
use App\Models\ProjectUser;
use Illuminate\Http\Request;
use App\Models\ProjectClient;
use App\Services\DeletionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request as FacadesRequest;

class ProjectsController extends Controller
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
    public function index(Request $request, $type = null)
    {
        $status = isset($_REQUEST['status']) && $_REQUEST['status'] !== '' ? $_REQUEST['status'] : "";
        $selectedTags = (request('tags')) ? request('tags') : [];
        $where = [];
        if ($status != '') {
            $where['status_id'] = $status;
        }
        $is_favorite = 0;
        if ($type === 'favorite') {
            $where['is_favorite'] = 1;
            $is_favorite = 1;
        }
        $sort = (request('sort')) ? request('sort') : "id";
        $order = 'desc';
        if ($sort == 'newest') {
            $sort = 'created_at';
            $order = 'desc';
        } elseif ($sort == 'oldest') {
            $sort = 'created_at';
            $order = 'asc';
        } elseif ($sort == 'recently-updated') {
            $sort = 'updated_at';
            $order = 'desc';
        } elseif ($sort == 'earliest-updated') {
            $sort = 'updated_at';
            $order = 'asc';
        }
        $projects = $this->user->hasRole('admin') ? $this->workspace->projects() : $this->user->projects();
        $projects->where($where);
        if (!empty($selectedTags)) {
            $projects->whereHas('tags', function ($q) use ($selectedTags) {
                $q->whereIn('tags.id', $selectedTags);
            });
        }

        $projects = $projects->orderBy($sort, $order)->paginate(6);
        return view('projects.grid_view', ['projects' => $projects, 'auth_user' => $this->user, 'selectedTags' => $selectedTags, 'is_favorite' => $is_favorite]);
    }

    public function list_view(Request $request, $type = null)
    {
        $projects = $this->user->hasRole('admin') ? $this->workspace->projects : $this->user->projects;
        $users = $this->workspace->users;
        $clients = $this->workspace->clients;
        $is_favorites = 0;
        if ($type === 'favorite') {
            $is_favorites = 1;
        }
        return view('projects.projects', ['projects' => $projects, 'users' => $users, 'clients' => $clients, 'is_favorites' => $is_favorites]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // get auth user role

        $client = $this->user->hasRole('Client') ?? false;

        $users = $this->workspace->users;
        $clients = $this->workspace->clients;

        if($client == false){
            return view('projects.create_project', ['users' => $users, 'clients' => $clients, 'auth_user' => $this->user, 'client' => $client]);

        }
        $plan = Plan::findOrFail($this->user->plan_id);


        $projects = Project::where('created_by', $this->user->id)->where('status_id', '!=', 2)->get();
        if (count($projects) >= $plan->projects) {
 return redirect()->back()->with('error', 'You have reached the maximum number of projects allowed for your plan.');
       
        } else {
                return view('projects.create_project', ['users' => $users, 'clients' => $clients, 'auth_user' => $this->user, 'client' => $client]);
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
            'start_date' => ['required', 'before_or_equal:end_date'],
            'end_date' => ['required'],
            'budget' => ['nullable'],
            'description' => ['required'],
        ]);

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $formFields['start_date'] = format_date($start_date, null, "Y-m-d");
        $formFields['end_date'] = format_date($end_date, null, "Y-m-d");

        $formFields['workspace_id'] = $this->workspace->id;
        $formFields['created_by'] = $this->user->id;



        $userIds = $request->input('user_id') ?? [];
        $clientIds = $request->input('client_id') ?? null;
        $tagIds = $request->input('tag_ids') ?? [];
        // Set creator as a participant automatically
        if (Auth::guard('client')->check()) {
                            $clientIds = $this->user->id;


        } else if (Auth::guard('web')->check() && !in_array($this->user->id, $userIds)) {
            array_splice($userIds, 0, 0, $this->user->id);
        }


        $clientUser = Client::findOrFail($clientIds);
        if($clientUser->plan_id == null){
              Session::flash('error', 'Your client 
              not select his plan.');
             return response()->json(['error' => false]);
        }
         $plan = Plan::findOrFail($clientUser->plan_id);


        $projects = Project::where('created_by', $clientIds)->where('status_id', '!=', 2)->get();

        if (count($projects) >= $plan->projects) {
                Session::flash('error', 'Your client have reached the maximum number of projects allowed for his plan.');
             return response()->json(['error' => false]);
        }else{
            
        }
        
        $new_project = Project::create($formFields);

$clientNewIds = [$clientIds];


        $project_id = $new_project->id;
       
        $project = Project::find($project_id);
        $project->users()->attach($userIds);
        $project->clients()->attach($clientNewIds);
        $project->tags()->attach($tagIds);
         if($clientIds){
            $project->created_by = $clientIds;
            $project->save();
        }

        Session::flash('message', 'Project created successfully.');
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

        $project = Project::findOrFail($id);
        $tags = $project->tags;
        return view('projects.project_information', ['project' => $project, 'tags' => $tags]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $users = $this->workspace->users;
        $clients = $this->workspace->clients;
        return view('projects.update_project', ["project" => $project, "users" => $users, "clients" => $clients]);
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
            'budget' => ['nullable'],
            'start_date' => ['required', 'before_or_equal:end_date'],
            'end_date' => ['required'],
            'description' => ['required'],
        ]);

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $formFields['start_date'] = format_date($start_date, null, "Y-m-d");
        $formFields['end_date'] = format_date($end_date, null, "Y-m-d");

        $userIds = $request->input('user_id') ?? [];
        $clientIds = $request->input('client_id');
        $tagIds = $request->input('tag_ids') ?? [];
        $project = Project::findOrFail($id);
        $clientNewIds = [$clientIds];

        // Set creator as a participant automatically
        if (User::where('id', $project->created_by)->exists() && !in_array($project->created_by, $userIds)) {
            array_splice($userIds, 0, 0, $project->created_by);
        } elseif (Client::where('id', $project->created_by)->exists() && !in_array($project->created_by, $clientNewIds)) {
            array_splice($clientNewIds, 0, 0, $project->created_by);
        }
        
         $clientUser = Client::findOrFail($clientIds);
         $plan = Plan::findOrFail($clientUser->plan_id);


        $projects = Project::where('id', '!=', $id)->where('created_by', $clientIds)->where('status_id', '!=', 2)->get();
        if (count($projects) >= $plan->projects) {
                Session::flash('error', 'Your client have reached the maximum number of projects allowed for his plan.');
             return response()->json(['error' => false]);
        }else{
            
        }
        

$clientNewIds = [$clientIds];
        
        
        $project->update($formFields);
        $project->users()->sync($userIds);
        $project->clients()->sync($clientNewIds);
        $project->tags()->sync($tagIds);

        Session::flash('message', 'Project updated successfully.');
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
        $response = DeletionService::delete(Project::class, $id, 'Project');
        return $response;
    }

    public function destroy_multiple(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:projects,id' // Ensure each ID in 'ids' is an integer and exists in the 'projects' table
        ]);

        $ids = $validatedData['ids'];

        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            DeletionService::delete(Project::class, $id, 'Project');
        }

        return response()->json(['error' => false, 'message' => 'Project(s) deleted successfully.']);
    }



    public function list(Request $request, $id = '', $type = '')
    {
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $status = isset($_REQUEST['status']) && $_REQUEST['status'] !== '' ? $_REQUEST['status'] : "";
        $user_id = (request('user_id')) ? request('user_id') : "";
        $client_id = (request('client_id')) ? request('client_id') : "";
        $start_date_from = (request('project_start_date_from')) ? request('project_start_date_from') : "";
        $start_date_to = (request('project_start_date_to')) ? request('project_start_date_to') : "";
        $end_date_from = (request('project_end_date_from')) ? request('project_end_date_from') : "";
        $end_date_to = (request('project_end_date_to')) ? request('project_end_date_to') : "";
        $is_favorites = (request('is_favorites')) ? request('is_favorites') : "";
        $where = [];
        if ($status != '') {
            $where['status_id'] = $status;
        }

        if ($is_favorites) {
            $where['is_favorite'] = 1;
        }

        if ($id) {
            $id = explode('_', $id);
            $belongs_to = $id[0];
            $belongs_to_id = $id[1];
            if ($belongs_to == 'user') {
                $belongs_to = User::find($belongs_to_id);
            }
            if ($belongs_to == 'client') {
                $belongs_to = Client::find($belongs_to_id);
            }
            $projects = $belongs_to->projects();
        } else {
            $projects = $this->user->hasRole('admin') ? $this->workspace->projects() : $this->user->projects();
        }
        if ($user_id) {
            $user = User::find($user_id);
            $projects = $user->projects();
        }
        if ($client_id) {
            $client = Client::find($client_id);
            $projects = $client->projects();
        }
        if ($start_date_from && $start_date_to) {
            $projects->whereBetween('start_date', [$start_date_from, $start_date_to]);
        }
        if ($end_date_from && $end_date_to) {
            $projects->whereBetween('end_date', [$end_date_from, $end_date_to]);
        }
        $projects->when($search, function ($query) use ($search) {
            return $query->where('title', 'like', '%' . $search . '%');
        });
        $projects->where($where);
        $totalprojects = $projects->count();

        $projects = $projects->orderBy($sort, $order)
            ->paginate(request("limit"))
            ->through(
                fn ($project) => [
                    'id' => $project->id,
                    'title' => "<a href='/projects/information/" . $project->id . "' target='_blank'><strong>" . $project->title . "</strong></a> <a href='javascript:void(0);' class='mx-2'><i class='bx " . ($project->is_favorite ? 'bxs' : 'bx') . "-star favorite-icon text-warning' data-favorite=" . $project->is_favorite . " data-id=" . $project->id . " title='" . ($project->is_favorite ? get_label('remove_favorite', 'Click to remove from favorite') : get_label('add_favorite', 'Click to mark as favorite')) . "'></i></a>",
                    'users' => $project->users,
                    'clients' => $project->clients,
                    'start_date' => format_date($project->start_date),
                    'end_date' => format_date($project->end_date),
                    'budget' => !empty($project->budget) && $project->budget !== null ? $project->budget : '-',
                    'status_id' => "<span class='badge bg-label-" . $project->status->color . " me-1'>" . $project->status->title . "</span>",
                ]
            );
        foreach ($projects->items() as $project => $collection) {
            foreach ($collection['clients'] as $i => $client) {
                $collection['clients'][$i] = "<a href='/clients/profile/" . $client->id . "' target='_blank'><li class='avatar avatar-sm pull-up'  title='" . $client['first_name'] . " " . $client['last_name'] . "'>
                <img src='" . ($client['photo'] ? asset('storage/' . $client['photo']) : asset('storage/photos/no-image.jpg')) . "' alt='Avatar' class='rounded-circle' />
                </li></a>";
            };
        }

        foreach ($projects->items() as $project => $collection) {
            foreach ($collection['users'] as $i => $user) {
                $collection['users'][$i] = "<a href='/users/profile/" . $user->id . "' target='_blank'><li class='avatar avatar-sm pull-up'  title='" . $user['first_name'] . " " . $user['last_name'] . "'>
                <img src='" . ($user['photo'] ? asset('storage/' . $user['photo']) : asset('storage/photos/no-image.jpg')) . "' class='rounded-circle' />
                </li></a>";
            };
        }

        return response()->json([
            "rows" => $projects->items(),
            "total" => $totalprojects,
        ]);
    }

    public function update_favorite(Request $request, $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json(['error' => true, 'message' => 'Project not found']);
        }

        $isFavorite = $request->input('is_favorite');

        // Update the project's favorite status
        $project->is_favorite = $isFavorite;
        $project->save();
        return response()->json(['error' => false]);
    }

    public function duplicate($id)
    {
        // Define the related tables for this meeting
        $relatedTables = ['users', 'clients', 'tasks', 'tags']; // Include related tables as needed

        // Use the general duplicateRecord function
        $duplicate = duplicateRecord(Project::class, $id, $relatedTables);

        if (!$duplicate) {
            if (request()->has('reload') && request()->input('reload') === 'true') {
                Session::flash('error', 'Project duplication failed.');
            }
            return response()->json(['error' => true, 'message' => 'Project duplication failed.']);
        }

        if (request()->has('reload') && request()->input('reload') === 'true') {
            Session::flash('message', 'Project duplicated successfully.');
        }
        return response()->json(['error' => false, 'message' => 'Project duplicated successfully.']);
    }
}
