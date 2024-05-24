@extends('layout')

@section('title')
<?= get_label('Plan_Requests', 'Plan Requests') ?>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between m-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="{{url('/home')}}"><?= get_label('home', 'Home') ?></a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= get_label('Plan Requests', 'Plan Requests') ?>
                    </li>
                </ol>
            </nav>
        </div>
        <div>
            <span data-bs-toggle="modal" data-bs-target="#create_todo_modal"><a href="javascript:void(0);" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('create_todo', 'Create todo') ?>"><i class='bx bx-plus'></i></a></span>
        </div>
    </div>

    @if (is_countable($planRequests) && count($planRequests) > 0)
    <div class="card mt-4">
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th><?= get_label('id', 'Id') ?></th>
                        <th><?= get_label('Plan Info', 'Plan Info') ?></th>
                        <th><?= get_label('Client Info', 'Client info') ?></th>
                        <th><?= get_label('Approved or not', 'Approved or not') ?></th>
                        <th><?= get_label('actions', 'Actions') ?></th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($planRequests as $request)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="mx-4">
                                    <p class="m-0">{{ $request->id }}</p>
                                    <h7 class="m-0 text-muted">{{ format_date($request->created_at,'H:i:s')}}</h7>

                                </span>
                            </div>
                        </td>
                        <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            Plan Name: {{ $request->plan->name }} <br>
                            Plan Amount: {{ $request->plan->amount }} $ <br>
                            Plan Duration: {{ $request->plan->duration }} days <br>
                            Plan Projects: {{ $request->plan->projects }} <br>
                            Plan Tasks per Project: {{ $request->plan->tasks_per_project }}
                        </td>
                        <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            Client Name: <a href="/clients/profile/{{ $request->client->id }}">{{ $request->client->first_name . ' ' . $request->client->last_name }}</a> <br>
                            Client Email: {{ $request->client->email }}
                        </td>
                        
                        
                        <td>
                            {{-- approved or not --}}
                            @if($request->is_approved)
                            <span class="badge bg-success">Approved</span>
                            @else
                            <span class="badge bg-danger">Not Approved</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex">
                                {{-- need approved button with confirmation model --}}
                                @if($request->is_approved == 0)
                                <button type="button" class="btn btn-success approved-plan" data-plan-id="{{ $request->id }}">Approved</button>
                                @endif
                                <form action="{{ route('plans.approve', $request->id) }}" method="post" class="d-none" id="approve-plan-form-{{ $request->id }}">
                                    @csrf
                                    @method('post')
                                </form>

                                <button type="button" class="btn delete-plan" data-plan-id="{{ $request->id }}"><i class='bx bx-trash text-danger mx-1'></i></button>
                                <form action="{{ route('plans.reject', $request->id) }}" method="post" class="d-none" id="delete-plan-form-{{ $request->id }}">
                                    @csrf
                                    @method('delete')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach



                </tbody>
            </table>
        <div class="p-3">
            {{ $planRequests->links() }}
        </div>

        </div>
    </div>

    @else
    <p>No  plan requests found.</p>
    @endif
</div>


<div class="modal fade" id="deletePlanModalReq" tabindex="-1" aria-labelledby="deletePlanModalReqLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePlanModalReqLabel">{{ __('Confirm Deletion') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ __('Are you sure you want to delete this plan reuqest.') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-danger confirm-delete">{{ __('Delete') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="approvedPlanModalReq" tabindex="-1" aria-labelledby="approvedPlanModalReqLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvedPlanModalReqLabel">{{ __('Confirm Assign Plan') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ __('Are you sure you want to assign this plan to this client.') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-success confirm-approved">{{ __('Approved') }}</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-plan');
        const modal = new bootstrap.Modal(document.getElementById('deletePlanModalReq'));

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

    document.addEventListener('DOMContentLoaded', function () {
        const approvedButtons = document.querySelectorAll('.approved-plan');
        const modalApproved = new bootstrap.Modal(document.getElementById('approvedPlanModalReq'));

        approvedButtons.forEach(button => {
            button.addEventListener('click', function () {
                const planId = this.getAttribute('data-plan-id');
                const form = document.getElementById('approve-plan-form-' + planId);

                modalApproved.show();

                document.querySelector('.confirm-approved').addEventListener('click', function () {
                    form.submit();
                });
            });
        });
    });

    
</script>

@endsection