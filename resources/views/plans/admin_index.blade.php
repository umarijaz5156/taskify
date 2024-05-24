@extends('layout')

@section('title')
    {{ __('Plans') }}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-11">
                @if(session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('message'))
                    <div class="alert alert-success" role="alert">
                        {{ session('message') }}
                    </div>
                @endif
                @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
                
                <div class="mt-4 float-end">
                    <a href="{{ route('plans.create') }}" class="btn btn-success">{{ __('Create Plan') }}</a>
                </div>
                <div class="container">
                    <div class="row mt-5 justify-content-center">
                        @foreach($plans as $plan)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header"><h4> {{ $plan->name }}</h4></div>
                                    <div class="card-body">
                                        <p class="card-text">Projects Allowed: {{ $plan->projects }}</p>
                                        <p class="card-text">Tasks per Project: {{ $plan->tasks_per_project }}</p>
                                        <p class="card-text">Price: {{ $plan->amount }} $</p>
                                        <p class="card-text">Duration: {{ $plan->duration }} (IN Days)</p>

                                        <a href="{{ route('plans.edit', $plan->id) }}" class="btn btn-secondary">{{ __('Edit') }}</a>
                                        <button type="button" class="btn btn-danger delete-plan" data-plan-id="{{ $plan->id }}">{{ __('Delete') }}</button>
                                        <form action="{{ route('plans.destroy', $plan->id) }}" method="post" class="d-none" id="delete-plan-form-{{ $plan->id }}">
                                            @csrf
                                            @method('delete')
                                        </form>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
            </div>
        </div>
    </div>

   <!-- Modal -->
   <div class="modal fade" id="deletePlanModal" tabindex="-1" aria-labelledby="deletePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePlanModalLabel">{{ __('Confirm Deletion') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ __('Are you sure you want to delete this plan? Its also delete plan subscrption of clients.') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-danger confirm-delete">{{ __('Delete') }}</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-plan');
        const modal = new bootstrap.Modal(document.getElementById('deletePlanModal'));

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const planId = this.getAttribute('data-plan-id');
                const form = document.getElementById('delete-plan-form-' + planId);

                modal.show();

                document.querySelector('.confirm-delete').addEventListener('click', function () {
                    form.submit();
                });
            });
        });
    });
</script>
@endsection


