@extends('layout')

@section('title')
    {{ __('Create Plan') }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">{{ __('Update Plan') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('plans.update') }}">
                            @csrf

                            <input type="hidden" name="id" value="{{ $plan->id }}">
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('Name') }}</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $plan->name }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="projects" class="form-label">{{ __('Projects Allowed') }}</label>
                                <input type="number" class="form-control" id="projects" name="projects" min="0"  value="{{ $plan->projects }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="tasks_per_project" class="form-label">{{ __('Tasks per Project') }}</label>
                                <input type="number" class="form-control" id="tasks_per_project" name="tasks_per_project" value="{{ $plan->tasks_per_project }}" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">{{ __('Amount') }}</label>
                                <input type="number" class="form-control" id="amount" name="amount" min="0" value="{{ $plan->amount }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="duration" class="form-label">{{ __('Duration (In Days)') }}</label>
                                <input type="number" class="form-control" id="duration" name="duration" min="0" value="{{ $plan->duration }}" required>
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
