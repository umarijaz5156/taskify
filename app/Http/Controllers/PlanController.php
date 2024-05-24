<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Plan;
use App\Models\PlanRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    //


    public function showPlans()
    {
        $user = getAuthenticatedUser();
        if(!$user){
            return redirect()->route('home');
        }
        if(!getAuthenticatedUser()->hasRole('admin')){
            return redirect()->route('home');
        }
        $user = getAuthenticatedUser();
        $plans = Plan::all();
        return view('plans.admin_index', ['plans' => $plans, 'user' => $user]);
    }
 
    // createPlans 
    public function createPlan()
    {
        $user = getAuthenticatedUser();
        if(!$user){
            return redirect()->route('home');
        }
        if(!getAuthenticatedUser()->hasRole('admin')){
            return redirect()->route('home');
        }
        $user = getAuthenticatedUser();
        return view('plans.admin_create', ['user' => $user]);
    }

    // updatePlans
    public function updatePlan(Request $request)
    {
        $user = getAuthenticatedUser();
        if(!$user){
            return redirect()->route('home');
        }
        if(!getAuthenticatedUser()->hasRole('admin')){
            return redirect()->route('home');
        }
        // validation
        $this->validate($request, [
            'name' => 'required',
            'projects' => 'required|numeric|min:0',
            'tasks_per_project' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:0',
        ]);

        if($request->amount < 0 || $request->projects < 0 || $request->tasks_per_project < 0 || $request->duration < 0){
            Session::flash('error', 'Invalid input');
            return redirect()->back();
        }

        if($request->amount == 0){
            // aslo add where id not $id
            
            $plan = Plan::where('amount', 0)->where('id' ,'!=', $request->id)->first();
            if($plan){
                Session::flash('error', 'Plan with amount 0 already exists');
                return redirect()->back();
            }
        }
        $user = getAuthenticatedUser();
        $plan = Plan::find($request->id);
        $plan->name = $request->name;
        $plan->amount = $request->amount;
        $plan->projects = $request->projects;
        $plan->tasks_per_project = $request->tasks_per_project;
        $plan->duration = $request->duration;
        $plan->save();
        Session::flash('message', 'Plan updated successfully');
        return redirect()->route('plans.index');
    }

    // editPlan
    public function editPlan($id)
    {
        $user = getAuthenticatedUser();
        if(!$user){
            return redirect()->route('home');
        }
        if(!getAuthenticatedUser()->hasRole('admin')){
            return redirect()->route('home');
        }
      
        $user = getAuthenticatedUser();
        $plan = Plan::find($id);
        return view('plans.admin_update', ['plan' => $plan, 'user' => $user]);
    }

    // storePlans
    public function storePlan(Request $request)
    {
        $user = getAuthenticatedUser();
        if(!$user){
            return redirect()->route('home');
        }
        if(!getAuthenticatedUser()->hasRole('admin')){
            return redirect()->route('home');
        }
        // validation
        $this->validate($request, [
            'name' => 'required',
            'projects' => 'required|numeric|min:0',
            'tasks_per_project' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:0',
        ]);

        if($request->amount < 0 || $request->projects < 0 || $request->tasks_per_project < 0 || $request->duration < 0){
            Session::flash('error', 'Invalid input');
            return redirect()->back();
        }

        if($request->amount == 0){
            $plan = Plan::where('amount', 0)->first();
            if($plan){
                Session::flash('error', 'Plan with amount 0 already exists');
                return redirect()->back();
            }
        }
        $user = getAuthenticatedUser();
        $plan = new Plan();
        $plan->name = $request->name;
        $plan->amount = $request->amount;
        $plan->projects = $request->projects;
        $plan->tasks_per_project = $request->tasks_per_project;
        $plan->duration = $request->duration;
        $plan->save();
        Session::flash('message', 'Plan created successfully');
        return redirect()->route('plans.index');
    }

    // destroy
    public function destroy($id)
    {
        $user = getAuthenticatedUser();
        if(!$user){
            return redirect()->route('home');
        }
        if(!getAuthenticatedUser()->hasRole('admin')){
            return redirect()->route('home');
        }
        $plan = Plan::find($id);
        $clients = Client::where('plan_id', $id)->get();
        foreach($clients as $client){
            $client->plan_id = null;
            $client->save();
        }
        $plan->delete();
        Session::flash('message', 'Plan deleted successfully');
        return redirect()->route('plans.index');
    }


    public function showRequests(){

        $user = getAuthenticatedUser();
        if(!$user){
            return redirect()->route('home');
        }
        if(!getAuthenticatedUser()->hasRole('admin')){
            return redirect()->route('home');
        }
        $user = getAuthenticatedUser();
        // plan requests
        $planRequests = PlanRequest::with('plan', 'client')->latest()->paginate(5);
        return view('plans.admin_requests', ['planRequests' => $planRequests, 'user' => $user]);
        
    }

    public function approveRequest($id){

        $user = getAuthenticatedUser();
        if(!$user){
            return redirect()->route('home');
        }
        if(!getAuthenticatedUser()->hasRole('admin')){
            return redirect()->route('home');
        }
        $planRequest = PlanRequest::find($id);
        $planRequest->is_approved = 1;
        $planRequest->save();

        $client = Client::find($planRequest->client_id);
        $client->plan_id = $planRequest->plan_id;
        $client->plan_end_date = date('Y-m-d', strtotime('+'.$planRequest->plan->duration.' days'));
        $client->save();
        Session::flash('message', 'Request approved successfully');
        return redirect()->back();
    }

    public function rejectRequest($id)
    {
        $user = getAuthenticatedUser();
        if(!$user){
            return redirect()->route('home');
        }
        if(!getAuthenticatedUser()->hasRole('admin')){
            return redirect()->route('home');
        }
        $planRequest = PlanRequest::find($id);
        $planRequest->delete();
        Session::flash('message', 'Request Deleted successfully');
        return redirect()->back();
    }
}
