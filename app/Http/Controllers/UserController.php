<?php

namespace App\Http\Controllers;

use App\Models\PlanRequest;
use App\Models\Task;
use App\Models\User;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Project;
use App\Models\TaskUser;
use App\Models\Workspace;
use Illuminate\Http\Request;
use App\Services\DeletionService;
use GuzzleHttp\Promise\TaskQueue;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Contracts\Role as ContractsRole;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $workspace = Workspace::find(session()->get('workspace_id'));
       
        $users = $workspace->users;
        return view('users.users', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('guard_name', 'web')->get();
        return view('users.create_user', ['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $formFields = $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'zip' => 'required',
            'dob' => 'required',
            'doj' => 'required',
            'role' => 'required',
            'status' => 'required',
            'department' => 'required'
        ]);

        $workspace = Workspace::where('title','Client')->first();
     
        $dob = $request->input('dob');
        $doj = $request->input('doj');
        $formFields['dob'] = format_date($dob, null, "Y-m-d");
        $formFields['doj'] = format_date($doj, null, "Y-m-d");
        $formFields['password'] = bcrypt($formFields['password']);

        if ($request->hasFile('photo')) {
            $formFields['photo'] = $request->file('photo')->store('photos', 'public');
        } else {
            $formFields['photo'] = 'photos/no-image.jpg';
        }

        $status = getAuthenticatedUser()->hasRole('admin') && $request->has('status') && $request->input('status') == 1 ? 1 : 0;
        if ($status == 1) {
            $formFields['email_verified_at'] = now()->tz(config('app.timezone'));
        }
        $user = User::create($formFields);
                    Session::flash('message', 'User created successfully.');
// return response()->json(['error' => false]);
        try {
            if ($status == 0) {
                event(new Registered($user));
            }
            $workspace->users()->attach($user->id);
            $user->assignRole($request->input('role'));
            Session::flash('message', 'User created successfully.');
            return response()->json(['error' => false]);
        } catch (TransportExceptionInterface $e) {

            $user = User::findOrFail($user->id);
            $user->delete();
            return response()->json(['error' => true, 'message' => 'User couldn\'t be created, please check email settings.']);
        } catch (Throwable $e) {
            // Catch any other throwable, including non-Exception errors
       

            $user = User::findOrFail($user->id);
            $user->delete();
            return response()->json(['error' => true, 'message' => 'User couldn\'t be created, please check email settings.']);
        }
    }

    public function email_verification()
    {
        $user = getAuthenticatedUser();
        if (!$user->hasVerifiedEmail()) {
            return view('auth.verification-notice');
        } else {
            return redirect('/home');
        }
    }

    public function resend_verification_link(Request $request)
    {
        if (isEmailConfigured()) {
            $request->user()->sendEmailVerificationNotification();

            return back()->with('message', 'Verification link sent.');
        } else {
            return back()->with('error', 'Verification link couldn\'t sent.');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit_user($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::where('guard_name', 'web')->get();
        return view('users.edit_user', ['user' => $user, 'roles' => $roles]);
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
            'first_name' => ['required'],
            'last_name' => ['required'],
            'phone' => 'required',
            'role' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'zip' => 'required',
            'department' => 'required'

        ]);
        $user = User::findOrFail($id);

        $user->update($formFields);
        $user->syncRoles($request->input('role'));

        return back()->with('message', 'Profile details updated successfully.');
    }

    public function update_user(Request $request, $id)
    {

        $formFields = $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'phone' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'zip' => 'required',
            'dob' => 'required',
            'doj' => 'required',
            'status' => 'required',
            'department' => 'required'

        ]);
        $user = User::findOrFail($id);
        $dob = $request->input('dob');
        $doj = $request->input('doj');
        $formFields['dob'] = format_date($dob, null, "Y-m-d");
        $formFields['doj'] = format_date($doj, null, "Y-m-d");
        if ($request->hasFile('upload')) {
            if ($user->photo != 'photos/no-image.jpg' && $user->photo !== null)
                Storage::disk('public')->delete($user->photo);

            $formFields['photo'] = $request->file('upload')->store('photos', 'public');
        }

        $status = getAuthenticatedUser()->hasRole('admin') && $request->has('status') && $request->input('status') == 1 ? 1 : 0;
        $formFields['status'] = $status;

        $user->update($formFields);
        $user->syncRoles($request->input('role'));

        Session::flash('message', 'Profile details updated successfully.');
        return response()->json(['error' => false]);
    }

    public function update_photo(Request $request, $id)
    {
        if ($request->hasFile('upload')) {
            $old = User::findOrFail($id);
            if ($old->photo != 'photos/no-image.jpg' && $old->photo !== null)
                Storage::disk('public')->delete($old->photo);
            $formFields['photo'] = $request->file('upload')->store('photos', 'public');
            User::findOrFail($id)->update($formFields);
            return back()->with('message', 'Profile picture updated successfully.');
        } else {
            return back()->with('error', 'No profile picture selected.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = DeletionService::delete(User::class, $id, 'User');
        return redirect('/');
    }

    public function delete_user($id)
    {
        $user = User::findOrFail($id);
        $response = DeletionService::delete(User::class, $id, 'User');
        $user->todos()->delete();
        return $response;
    }

    public function delete_multiple_user(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'ids' => 'required|array', // Ensure 'ids' is present and an array
            'ids.*' => 'integer|exists:users,id' // Ensure each ID in 'ids' is an integer and exists in the table
        ]);

        $ids = $validatedData['ids'];

        // Perform deletion using validated IDs
        foreach ($ids as $id) {
            $user = User::findOrFail($id);
            DeletionService::delete(User::class, $id, 'User');
            $user->todos()->delete();
        }

        return response()->json(['error' => false, 'message' => 'User(s) deleted successfully.']);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            auth('web')->logout();
        } else {
            auth('client')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'Logged out successfully.');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        if (auth('web')->check() || auth('client')->check()) {
            return redirect('/home');
        }
        return view('auth.register');
    }

    public function authenticate(Request $request)
    {
        $formFields = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);
        if (!User::where('email', $formFields['email'])->first() && !Client::where('email', $formFields['email'])->first()) {
            return response()->json(['error' => true, 'message' => 'Account not found!']);
        }
        $logged_in = false;
        if (auth('web')->attempt($formFields)) {
            $user = auth('web')->user();
            if ($user->hasRole('admin') || $user->status == 1) {
                $logged_in = true;
            } else {
                return response()->json(['error' => true, 'message' => get_label('status_not_active', 'Your account is currently inactive. Please contact admin for assistance.')]);
            }
        }
        if (auth('client')->attempt($formFields)) {
            $user = auth('client')->user();
            if ($user->status == 1) {
                $logged_in = true;
            } else {
                return response()->json(['error' => true, 'message' => get_label('status_not_active', 'Your account is currently inactive. Please contact admin for assistance.')]);
            }
        }

        if ($logged_in) {
            $workspace_id = isset($user->workspaces[0]['id']) && !empty($user->workspaces[0]['id']) ? $user->workspaces[0]['id'] : 0;
            $my_locale = $locale = isset($user->lang) && !empty($user->lang) ? $user->lang : 'en';
            $data = ['workspace_id' => $workspace_id, 'my_locale' => $my_locale, 'locale' => $locale];
            session()->put($data);
            $request->session()->regenerate();

            Session::flash('message', 'Logged in successfully.');
            return response()->json(['error' => false]);
        } else {
            return response()->json(['error' => true, 'message' => 'Invalid credentials!']);
        }
    }

    public function clientRegister(Request $request){

        $formFields = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', 'email', 'unique:clients,email'],
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required',
        ]);

        $formFields['password'] = bcrypt($formFields['password']);

        if ($request->hasFile('profile')) {
            $formFields['photo'] = $request->file('profile')->store('photos', 'public');
        } else {
            $formFields['photo'] = 'photos/no-image.jpg';
        }

        // $dob = $request->input('dob');
        // $doj = $request->input('doj');
        $formFields['dob'] = today();
        $formFields['doj'] = today();


        // $formFields['doj'] = format_date($doj, null, "Y-m-d");

        $role_id = Role::where('name', 'client')->first()->id;
        $workspace = Workspace::where('title','Client')->first();
        $workspaceId = Workspace::where('title','Client')->first();
        $workspace = Workspace::find($workspaceId->id);
        $client = Client::create($formFields);

            if ($client) {
                event(new Registered($client));
                $workspace->clients()->attach($client->id);
            $client->assignRole($role_id);
            // need login this user and go to email/verify url

            Session::flash('message', 'We sent a confirmation email to your email address. Please check your email in inbox/spam folder to activate the account');
            return Redirect::route('login');
            }
            
        
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $workspace = Workspace::find(session()->get('workspace_id'));
        $projects = $user->hasRole('admin') ? $workspace->projects : $user->projects;
        $tasks = $user->hasRole('admin') ? $workspace->tasks->count() : $user->tasks->count();
        $users = $workspace->users;
        $clients = $workspace->clients;

        return view('users.user_profile', ['user' => $user, 'projects' => $projects, 'tasks' => $tasks, 'users' => $users, 'clients' => $clients, 'auth_user' => getAuthenticatedUser()]);
    }

    public function list()
    {
        $workspace = Workspace::find(session()->get('workspace_id'));
        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $users = $workspace->users();
        $users = $users->when($search, function ($query) use ($search) {
            return $query->where('first_name', 'like', '%' . $search . '%')
                ->orWhere('last_name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        });

        $totalusers = $users->count();

        $users = $users->orderBy($sort, $order)
            ->paginate(request("limit"))
            ->through(fn ($user) => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => "<span class='badge bg-label-" . (isset(config('taskhub.role_labels')[$user->getRoleNames()->first()]) ? config('taskhub.role_labels')[$user->getRoleNames()->first()] : config('taskhub.role_labels')['default']) . " me-1'>" . $user->getRoleNames()->first() . "</span>",
                'email' => $user->email,
                'phone' => $user->phone,
                'photo' => "<div class='avatar avatar-md pull-up' title='" . $user->first_name . " " . $user->last_name . "'>
                    <a href='/users/profile/" . $user->id . "'>
                    <img src='" . ($user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg')) . "' alt='Avatar' class='rounded-circle'>
                    </a>
                    </div>",
                'tasks' => count($user->hasRole('admin') ? $workspace->tasks : $user->tasks),
                'projects' => count($user->hasRole('admin') ? $workspace->projects : $user->projects),
                'status' => $user->status,
            ]);

        return response()->json([
            "rows" => $users->items(),
            "total" => $totalusers,
        ]);
    }



 public function showPlans()
    {
         
        $user = getAuthenticatedUser();
        if(!$user){
             return redirect('/');
        }
        $plans = Plan::all();
        if($user->plan_id == null || $user->plan_end_date < date('Y-m-d')){
            Session::flash('error', 'Your plan is not activate or expired. Please subscribe a plan to continue.');
        }
        
        $planReq = PlanRequest::where('client_id',$user->id)->where('is_approved',0)->first();


        return view('plans.index', ['plans' => $plans, 'user' => $user, 'planReq' => $planReq]);
    }

    public function subscribePlans($id){

        $user = getAuthenticatedUser();
        $plan = Plan::find($id);
        $ActivePlan = Plan::find($user->plan_id);
        
        if($plan->amount == 0){
            if($ActivePlan){
                if($ActivePlan->amount == 0){
                    Session::flash('message', 'You already use trail plan. Please buy a new plan now.');
                    return back();
                }
            }

            $client = Client::find($user->id);
            $client->plan_id = $plan->id;
            $client->trail_plan_used = 1;
            $client->plan_end_date = date('Y-m-d', strtotime('+'.$plan->duration.' days'));
            $client->save();
            Session::flash('message', 'Your Free Trail Plan subscribed successfully.');
            return redirect()->route('home');
        }else{

            $alreadyPlanReq = PlanRequest::where('client_id',$user->id)->where('is_approved',0)->first();
            

            if($alreadyPlanReq){
            Session::flash('message', 'You have sent an request already for furher assitance contact support team at support@wpalleviate.com');
                return back();
            }
            

            if($user->plan_id){
                            $currentPlan = Plan::findOrFail($user->plan_id);
            if($currentPlan->amount > $plan->amount){
                Session::flash('message', 'You can downgrade your plan after the current subscription plan expires.');
                return back();
             }
            }
            
            
                                   

          
      
                $newPlanReq = new PlanRequest;
                $newPlanReq->plan_id = $plan->id;
                $newPlanReq->client_id = $user->id;
                $newPlanReq->save();
                
                Session::flash('message', 'Your plan request has been submitted successfully. An administrator will send you an email for payment. Please wait for administrator approval.');
                return back();
            
        }
    }
}
