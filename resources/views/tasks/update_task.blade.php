@extends('layout')

@section('title')
<?= get_label('update_task', 'Update task') ?>
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
                    @if (Request::segment(1)=='projects')
                    <li class="breadcrumb-item">
                        <a href="{{url('/projects')}}"><?= get_label('projects', 'Projects') ?></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{url('/projects/information/'.$project->id)}}">{{$project->title}}</a>
                    </li>
                    @endif
                    <li class="breadcrumb-item">
                        <a href="{{url(Request::segment(1)=='projects'?'/projects/tasks/draggable/'.$project->id:'/tasks')}}"><?= get_label('tasks', 'Tasks') ?></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{url('/tasks/information/'.$task->id)}}">{{$task->title}}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= get_label('update', 'Update') ?>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <form action="{{url('/tasks/update/' . $task->id)}}" class="form-submit-event" method="POST">
                @csrf
                @method('PUT')
                @if (Request::segment(1)=='projects')
                <input type="hidden" name="redirect_url" value="/projects/tasks/list/{{$project->id}}">
                @else
                <input type="hidden" name="redirect_url" value="/tasks">
                @endif
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="title" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="text" id="title" name="title" placeholder="Enter Title" value="{{ $task->title }}">
                        @error('title')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <div class="form-group">
                            <label for="task_department">Task Department</label>
                            {{-- <select class="form-control" id="task_department" name="task_department">
                                <option value="Web development/designing" {{ $task->task_department == 'Web development/designing' ? 'selected' : '' }}>Web development/designing</option>
                                <option value="Graphic designing" {{ $task->task_department == 'Graphic designing' ? 'selected' : '' }}>Graphic designing</option>
                                <option value="SEO" {{ $task->task_department == 'SEO' ? 'selected' : '' }}>SEO</option>
                                <option value="Fix Bug" {{ $task->task_department == 'Fix Bug' ? 'selected' : '' }}>Fix Bug</option>
                                <option value="Content creation" {{ $task->task_department == 'Content creation' ? 'selected' : '' }}>Content creation</option>
                                <option value="Marketing" {{ $task->task_department == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="Data analysis" {{ $task->task_department == 'Data analysis' ? 'selected' : '' }}>Data analysis</option>
                                <option value="Customer support" {{ $task->task_department == 'Customer support' ? 'selected' : '' }}>Customer support</option>
                                <option value="Project management" {{ $task->task_department == 'Project management' ? 'selected' : '' }}>Project management</option>
                                <option value="Research and development" {{ $task->task_department == 'Research and development' ? 'selected' : '' }}>Research and development</option>
                                <option value="Quality assurance" {{ $task->task_department == 'Quality assurance' ? 'selected' : '' }}>Quality assurance</option>
                                <option value="Sales" {{ $task->task_department == 'Sales' ? 'selected' : '' }}>Sales</option>
                                <option value="Human resources" {{ $task->task_department == 'Human resources' ? 'selected' : '' }}>Human resources</option>
                                <option value="Legal" {{ $task->task_department == 'Legal' ? 'selected' : '' }}>Legal</option>
                            </select> --}}
                            <select class="form-select" id="task_department" name="task_department">
                                <option value="">Select Department</option>
                                <option value="Web Development/Designing" {{ $task->task_department == 'Web Development/Designing' ? 'selected' : '' }}>Web Development/Designing</option>
                                <option value="Fix Bug" {{ $task->task_department == 'Fix Bug' ? 'selected' : '' }}>Fix Bug</option>
                                <option value="Graphic Designing" {{ $task->task_department == 'Graphic Designing' ? 'selected' : '' }}>Graphic Designing</option>
                                <option value="SEO" {{ $task->task_department == 'SEO' ? 'selected' : '' }}>SEO</option>
                                <option value="Content Writing" {{ $task->task_department == 'Content Writing' ? 'selected' : '' }}>Content Writing</option>
                                <option value="Marketing" {{ $task->task_department == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="Domain/Hosting/cPanel Support" {{ $task->task_department == 'Domain/Hosting/cPanel Support' ? 'selected' : '' }}>Domain/Hosting/cPanel Support</option>
                                <option value="HR/Customer Support" {{ $task->task_department == 'HR/Customer Support' ? 'selected' : '' }}>HR/Customer Support</option>
                            </select>
                            
                        </div>
                        
                        
                    </div>
                </div>

                <?php
                $project = $task->project;
                ?>

                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="project_title" class="form-label"><?= get_label('project', 'Project') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="text" id="project_title" name="project_title" placeholder="Enter Title" value="{{ $project->title }}" readonly>
                        @error('title')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="status"><?= get_label('status', 'Status') ?> <span class="asterisk">*</span></label>
                        <div class="input-group">


                            <select class="form-select" id="status_id" name="status_id">
                                @foreach($statuses as $status)
                                <option value="{{$status->id}}" class="badge bg-label-{{$status->color}}" <?php if ($task->status->id == $status->id) {
                                                                                                                print_r('selected');
                                                                                                            } ?>>{{$status->title}} ({{$status->color}})</option>
                                @endforeach
                            </select>


                        </div>
                        <div class="mt-2">
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create_status_modal"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title=" <?= get_label('create_status', 'Create status') ?>"><i class="bx bx-plus"></i></button></a>
                            <a href="/status/manage" target="_blank"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="<?= get_label('manage_statuses', 'Manage statuses') ?>"><i class="bx bx-list-ul"></i></button></a>
                        </div>
                        @error('status_id')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="row" style="display: none;">

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="start_date"><?= get_label('starts_at', 'Starts at') ?> <span class="asterisk">*</span></label>
                        <input type="text" id="start_date" name="start_date" class="form-control" value="{{ format_date($task->start_date)}}">
                        @error('start_date')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="due_date"><?= get_label('ends_at', 'Ends at') ?> <span class="asterisk">*</span></label>
                        <input type="text" id="end_date" name="due_date" class="form-control" value="{{ format_date($task->due_date)}}">
                        @error('due_date')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3">
                        <label class="form-label" for="user_id"><?= get_label('select_users', 'Select users') ?> (<?= get_label('users_associated_with_project', 'Users associated with project') ?> <b>{{$project->title}}</b>)</label>
                        <div class="input-group">

                            <select id="" class="form-control js-example-basic-multiple" name="user_id[]" multiple="multiple" data-placeholder="<?= get_label('type_to_search', 'Type to search') ?>">
                                @foreach($users as $user)
                                <option value="{{$user->id}}" <?php if ($task_users->contains($user)) {
                                                                    echo "selected";
                                                                } ?>>{{$user->first_name}} {{$user->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="mb-3">
                        <label for="description" class="form-label"><?= get_label('description', 'Description') ?> <span class="asterisk">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Enter Description">{{ $task->description }}</textarea>
                        @error('description')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-2">
                    <button type="submit" class="btn btn-primary me-2" id="submit_btn"><?= get_label('update', 'Update') ?></button>
                    <button type="reset" class="btn btn-outline-secondary"><?= get_label('cancel', 'Cancel') ?></button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection