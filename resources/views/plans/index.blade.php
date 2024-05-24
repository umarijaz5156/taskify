@extends('layout')

@section('title')
    {{ __('Plans') }}
@endsection
@php
    
    $user =   getAuthenticatedUser();

@endphp
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-11">
                
         @if($planReq)
            <div class="alert alert-danger mt-2" role="alert">
                <p>
                    Sorry, we're still working on fixing the automatic payment issue. Sorry to encounter an automatic payment issue.
                    You will soon receive an email from the support team with payment details to make the payment. After payment, your plan will be subscribed and you will be able to request your project tasks.
                </p>
            </div>
        @endif


            
                @if(session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('message'))
                <div class="alert alert-warning" role="alert">
                    {{ session('message') }}
                </div>
            @endif
            <!-- This snippet uses Font Awesome 5 Free as a dependency. You can download it at fontawesome.io! -->

<section class="py-5">
	<div class="container">
		<div class="row">
            @foreach($plans as $plan)
			<div class="col-lg-4 mt-3">
				<div class="card  mb-5 mb-lg-0 rounded-lg shadow">
					<div class="card-header" style="background-color: #257cff; color: white;">
						<h5 class="card-title text-white text-uppercase text-center">{{ $plan->name }}</h5>
						<h6 class="h1 text-white text-center">${{ $plan->amount }}<span class="h6 text-white"> {{ $plan->duration }} /Days</span></h6>
					</div>
					<div class="card-body  bg-light rounded-bottom">
						<ul class="list-unstyled mb-4 mt-4 text-black">
							<li class="mb-3"  data-bs-toggle="tooltip" data-bs-placement="top" title="Project, in this context, represents a comprehensive assignment, such as developing an entire website/software/CMS/Brand Tools Kit consisted of a series of tasks. You can avail alternation in the project particulars once in a subscription lifecycle."
                            ><span class="mr-3"><i class="fas fa-check text-success"></i></span> Project Allowed: <b style="color: blue">{{ $plan->projects }}</b></li>
							<li class="mb-3" data-bs-toggle="tooltip" data-bs-placement="top" title="A task denotes an individual assignment or work item, such as creating a page, implementing a modification, fixing a bug, or adding a new feature. Note: you have the flexibility to create a new task after closing an existing one, especially if the active task limit has been surpassed. Additionally, tasks that have been closed can be reopened at any time in your subscription lifecycle."
                            ><span class="mr-3"><i class="fas fa-check text-success"></i></span>  Active Task Request:  <b style="color: blue">{{ $plan->tasks_per_project }}</b></li>
							<li class="mb-3"><span class="mr-3"><i class="fas fa-check text-success"></i></span>Price: {{ $plan->amount }} $</li>
							<li class="mb-3"><span class="mr-3"><i class="fas fa-check text-success"></i></span>Payment Cycle: {{ $plan->duration }} Days</li>
							<li class="text-black mb-3"><span class="mr-3"><i class="fas fa-times"></i></span>Get Unlimited Work</li>
                            <li class=" mb-3"><span class="mr-3"><i class="fas fa-times"></i></span>Most tasks done within 24hr</li>
							<li class="text-black mb-3"><span class="mr-3"><i class="fas fa-times"></i></span>Support 24/7</li>
							<li class="text-black mb-3"><span class="mr-3"><i class="fas fa-times"></i></span>Downgrade/Upgrade Anytime</li>
                            @if($plan->name == 'Trial')
							<li class="text-black mb-3"><span class="mr-3"><i class="fas fa-times"></i></span>No Credit Card Required</li>
                            @else
							<li class="text-black mb-3"><span class="mr-3"><i class="fas fa-times"></i></span>Cancel Anytime</li>
                            @endif
						</ul>
                        @if($user->plan_id == $plan->id && $user->plan_end_date > now())
                        <p class="card-text text-success">Current Plan</p>
                    @elseif($plan->amount == 0 && $user->trail_plan_used == 1)
                        <p class="card-text text-danger">Trial Plan Used</p>
                    @else


            @if(!empty($planReq) && $plan->id === $planReq->plan_id)
                <p class="text-danger">Pending</p>
            @else
           
                <a href="#" class="btn text-uppercase rounded py-2 px-2 subscribeButton" data-bs-toggle="modal" data-bs-target="#confirmationModal" data-plan-id="{{ $plan->id }}">
                    {{ __('Subscribe') }}
                </a>
            @endif


                    <!--<a href="#" class="btn  text-uppercase rounded py-2 px-2 subscribeButton" data-bs-toggle="modal" data-bs-target="#confirmationModal" data-plan-id="{{ $plan->id }}">-->
                    <!--    {{ __('Subscribe') }}-->
                    <!--</a>-->
                    
                      
                                     @endif
					</div>
				</div>
			</div>
        @endforeach
		</div>
        <div class="text-center mt-5">
        <p>
            For more plan details please visit the plan page or reach out at 
            <a href="mailto:support@wpalleviate.com"><strong>support@wpalleviate.com</strong></a>.
        </p>
        </div>
        
	</div>
</section>
 <style>
    .subscribeButton {
    background-color: #f45a2a;  /* Button background color */
    color: white;               /* Button text color */
    transition: background-color 0.3s;
}

.subscribeButton:hover {
    background-color: #257cff; 
    color: white; 
}

 </style>           
<!-- Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to subscribe?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirmSubscribeButton">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript to handle subscription confirmation
    $(document).ready(function() {
        $('.subscribeButton').click(function() {
            var planId = $(this).data('plan-id');
            $('#confirmationModal').data('plan-id', planId);

        });

        $('.confirmSubscribeButton').click(function() {
            var planId = $('#confirmationModal').data('plan-id');
            var subscriptionRoute = "{{ route('plans.subscribe', ':planId') }}";
            subscriptionRoute = subscriptionRoute.replace(':planId', planId);
            window.location.href = subscriptionRoute;
        });
    });
</script>

      
    
@endsection