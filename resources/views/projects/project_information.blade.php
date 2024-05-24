@extends('layout')

@section('title')
<?= get_label('project_details', 'Project details') ?>
@endsection
@php
    $auth_user =getAuthenticatedUser();

@endphp
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between m-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="{{url('/home')}}"><?= get_label('home', 'Home') ?></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{url('/projects')}}"><?= get_label('projects', 'Projects') ?></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{url('/projects/information/'.$project->id)}}">{{$project->title}}</a>
                    </li>
                    <li class="breadcrumb-item active"><?= get_label('view', 'View') ?></li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{url('/projects/tasks/create/' . $project->id)}}"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('create_task', 'Create task') ?>"><i class="bx bx-plus"></i></button></a>
            <a href="{{url('/projects/tasks/draggable/' . $project->id)}}"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('tasks', 'Tasks') ?>"><i class="bx bx-task"></i></button></a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="demo-inline-spacing">
                                @foreach ($tags as $tag)
                                <span class="badge bg-{{$tag->color}}">{{$tag->title}}</span>
                                @endforeach
                            </div>
                            <h2 class="fw-bold mt-4 mb-4">{{ $project->title }} <a href="javascript:void(0);" class="mx-2">
                                    <i class='bx {{$project->is_favorite ? "bxs" : "bx"}}-star favorite-icon text-warning' data-id="{{$project->id}}" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="{{$project->is_favorite ? get_label('add_favorite', 'Click to remove from favorite') : get_label('remove_favorite', 'Click to mark as favorite')}}" data-favorite="{{$project->is_favorite ? 1 : 0}}"></i>
                                </a></h2>
                            <span class='badge bg-label-{{$project->status->color}} me-1'> {{$project->status->title}}</span>
                        </div>
                        <div class="col-md-3 mt-4">
                            <div class="mb-3 text-center">
                                <label class="form-label" for="start_date"><?= get_label('Team Member', 'Team Member') ?></label>
                                <?php
                                $users = $project->users;
                                if (count($users) > 0) { ?>
                                    <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center flex-wrap justify-content-center">
                                        @foreach($users as $user)
                                    <li>
                                        <strong>{{ $user->department ?? 'Null' }}:</strong> <a href="/users/profile/{{ $user->id }}"> {{ $user->first_name }} {{ $user->last_name }}</a>
                                    </li>
                                @endforeach
                                    </ul>
                                <?php } else { ?>
                                    <span class="badge bg-primary"><?= get_label('not_assigned', 'Not assigned') ?></span>
                                <?php } ?>
                            </div>
                        </div>
                        @if(!$auth_user->hasRole('Client'))

                        <div class="col-md-3 mt-4">
                            <div class="mb-3 text-center">
                                <label class="form-label" for="end_date"><?= get_label('clients', 'Clients') ?></label>
                                <?php
                                $clients = $project->clients;
                                if (count($clients) > 0) { ?>
                                    <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                        @foreach($clients as $client)
                                        <li class="avatar avatar-sm pull-up" title="{{$client->first_name}} {{$client->last_name}}"><a href="/clients/profile/{{$client->id}}" target="_blank">
                                                <img src="{{$client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg')}}" class="rounded-circle" alt="{{$client->first_name}} {{$client->last_name}}">
                                            </a></li>
                                        @endforeach
                                    </ul>
                                <?php } else { ?>
                                    <span class="badge bg-primary"><?= get_label('not_assigned', 'Not assigned') ?></span>
                                <?php } ?>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <hr class="my-0" />
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
                            <div class="card h-100">
                                <span class="badge bg-label-info m-2"><?= get_label('reload_page_to_change_chart_colors', 'Reload the page to change chart colors!') ?></span>
                                <div class="card-header d-flex align-items-center justify-content-between pt-3 pb-1">
                                    <div class="card-title mb-0">
                                        <h5 class="m-0 me-2"><?= get_label('task_statistics', 'Task statistics') ?></h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div id="taskStatisticsChart"></div>
                                    </div>
                                    <div class="m-3 mb-4">
                                        <a href="{{ url('/projects/tasks/list/' . $project->id) }}"><i class='bx bx-task text-primary'></i> <b><?= $auth_user->hasRole(['admin', 'Project Manager']) ? count($project->tasks) : $auth_user->project_tasks($project->id)->count(); ?></b> <?= get_label('tasks', 'Tasks') ?></span>

                                    </div>
                                    <?php $total_tasks_count = 0; ?>
                                    <ul class="p-0 m-0">
                                        @foreach ($statuses as $status)
                                        <li class="d-flex mb-4 pb-1">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-{{$status->color}}"><i class="bx bx-task"></i></span>
                                            </div>
                                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <a href="/tasks?project={{$project->id}}&status={{ $status->id }}">
                                                        <h6 class="mb-0">{{ $status->title }}</h6>
                                                    </a>
                                                </div>
                                                <div class="user-progress">
                                                    <?php
                                                    $statusCount = $project->tasks->where('status_id', $status->id)->count();
                                                    $total_tasks_count += $statusCount;
                                                    ?>
                                                    <div class="status-count">
                                                        <small class="fw-semibold">{{$statusCount}}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>

                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-menu"></i></span>
                                        </div>
                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h5 class="mb-0"><?= get_label('total', 'Total') ?></h5>
                                            </div>
                                            <div class="user-progress">
                                                <div class="status-count">
                                                    <h5 class="mb-0">{{$total_tasks_count}}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-lg-4 col-md-12 col-6 mb-4">
                            <!-- "Starts at" card -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                        <div class="avatar flex-shrink-0">
                                            <i class="menu-icon tf-iconsbx bx bx-calendar-check bx-md text-success"></i>
                                        </div>
                                    </div>
                                    <span class="fw-semibold d-block mb-1"><?= get_label('starts_at', 'Starts at') ?></span>
                                    <h3 class="card-title mb-2">{{ format_date($project->start_date) }}</h3>
                                </div>
                            </div>
                            @php
                            use Carbon\Carbon;
                            $fromDate = Carbon::parse($project->from_date);
                            $toDate = Carbon::parse($project->to_date);
                            $duration = $fromDate->diffInDays($toDate) + 1;
                            @endphp
                            <div class="card mt-4">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                        <div class="avatar flex-shrink-0">
                                            <i class="menu-icon tf-iconsbx bx bx-time bx-md text-primary"></i>
                                        </div>
                                    </div>
                                    <span class="fw-semibold d-block mb-1"><?= get_label('duration', 'Duration') ?></span>
                                    <h3 class="card-title mb-2">{{ $duration . ' day' . ($duration > 1 ? 's' : '') }}</h3>
                                </div>
                            </div>
                        </div> --}}

                        <div class="col-lg-4 col-md-12 col-6 mb-4">
                            <!-- "Ends at" card -->
                            {{-- <div class="card">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                        <div class="avatar flex-shrink-0">
                                            <i class="menu-icon tf-icons bx bx-calendar-x bx-md text-danger"></i>
                                        </div>
                                    </div>
                                    <span class="fw-semibold d-block mb-1"><?= get_label('ends_at', 'Ends at') ?></span>
                                    <h3 class="card-title mb-2">{{ format_date($project->end_date) }}</h3>
                                </div>
                            </div> --}}
                            <div class="card mt-4">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                        <div class="avatar flex-shrink-0">
                                            <i class="menu-icon tf-icons bx bx-purchase-tag-alt bx-md text-warning"></i>
                                        </div>
                                    </div>
                                    <span class="fw-semibold d-block mb-1"><?= get_label('budget', 'Budget') ?></span>
                                    <h3 class="card-title mb-2">{{ isset($project->budget) ? $general_settings['currency_symbol'] . ' ' . $project->budget : '-' }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h5><?= get_label('description', 'Description') ?></h5>
                                    </div>
                                    <p>
                                        <!-- Add your project description here -->
                                        {{ ($project->description !== null && $project->description !== '') ? $project->description : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
<?php

$titles = [];
$task_counts = [];
$bg_colors = [];
$total_tasks = 0;

$ran = array('#63ed7a', '#ffa426', '#fc544b', '#6777ef', '#FF00FF', '#53ff1a', '#ff3300', '#0000ff', '#00ffff', '#99ff33', '#003366', '#cc3300', '#ffcc00', '#ff00ff', '#ff9900', '#3333cc', '#ffff00');
$backgroundColor = array_rand($ran);
$d = $ran[$backgroundColor];
foreach ($statuses as $status) {

    $task_count = $project->tasks->where('status_id', $status->id)->count();
    array_push($task_counts, $task_count);

    array_push($titles, "'" . $status->title . "'");

    $k = array_rand($ran);
    $v = $ran[$k];
    array_push($bg_colors, "'" . $v . "'");
    $total_tasks += $task_count;
}
$titles = implode(",", $titles);
$task_counts = implode(",", $task_counts);
$bg_colors = implode(",", $bg_colors);
?>

<script>
    var labels = [<?= $titles ?>];
    var task_data = [<?= $task_counts ?>];
    var bg_colors = [<?= $bg_colors ?>];
    var total_tasks = [<?= $total_tasks ?>];
    //labels
    var total = '<?= get_label('total', 'Total') ?>';
    var add_favorite = '<?= get_label('add_favorite', 'Click to mark as favorite') ?>';
    var remove_favorite = '<?= get_label('remove_favorite', 'Click to remove from favorite') ?>';
</script>

<script src="{{asset('assets/js/apexcharts.js')}}"></script>
<script src="{{asset('assets/js/pages/project-information.js')}}"></script>
@endsection