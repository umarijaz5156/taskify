@extends('layout')

@section('title')
    <?= get_label('task_details', 'Task details') ?>
@endsection
@php
    $auth_user =getAuthenticatedUser();

@endphp
@section('content')
    <div class="container-fluid">
        <div class="align-items-center d-flex justify-content-between m-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/home') }}"><?= get_label('home', 'Home') ?></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ url('/tasks') }}"><?= get_label('tasks', 'Tasks') ?></a>
                        </li>
                        <li class="breadcrumb-item">{{ $task->title }}</li>
                        <li class="breadcrumb-item active"><?= get_label('view', 'View') ?></li>
                    </ol>
                </nav>
            </div>
            <div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">


                    <div class="card-body">
                        <div class="d-flex align-items-start align-items-sm-center justify-content-between gap-2">
                            <!-- Title on the left -->
                            <h2 class="card-header fw-bold">{{ $task->title }}</h2>
                            <!-- Status badge on the right -->
                            <div class="mb-3 col-md-6 " style="text-align:end">
                                <label class="form-label" for="status"><?= get_label('status', 'Status') ?></label>
                                <div class=" input-group-merge">
                                    <span class='badge bg-label-{{ $task->status->color }} me-1'>
                                        {{ $task->status->title }}</span>
                                </div>
                                                                <div class="mt-2 pt-3">
                                <strong>Created at: </strong><span> {{ $task->created_at }}</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <hr class="my-0" />
                    <div class="card-body">


                        <div class="row">   
                           
                        </div>

                        <div class="row">

                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="project"><?= get_label('project', 'Project') ?></label>
                                <div class="input-group input-group-merge">
                                    @php
                                        $project = $task->project;
                                    @endphp
                                    <input class="form-control px-2" type="text" id="project" placeholder=""
                                        value="{{ $project->title }}" readonly="">
                                </div>
                            </div>

                            
                            <div class="mb-3 col-md-6 text-end" >

                                <label class="form-label" for="start_date"><?= get_label('Team Member', 'Team Member') ?></label>
                                <div class="list-unstyled users-list m-0 avatar-group  align-items-center">
                                    <?php
                                $users = $task->users;
                                $clients = $task->project->clients;
                                if (count($users) > 0) { ?>
                                    @foreach($users as $user)
                                        <strong>{{ $user->department ?? 'Not Selected' }}:</strong> <span> {{ $user->first_name }} {{ $user->last_name }}</span><br>
                                @endforeach
                                    {{-- @foreach ($users as $user)
                                        <li class="avatar avatar-sm pull-up"
                                            title="{{ $user->first_name }} {{ $user->last_name }}"><a
                                                href="/users/profile/{{ $user->id }}" target="_blank">
                                                <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg') }}"
                                                    class="rounded-circle"
                                                    alt="{{ $user->first_name }} {{ $user->last_name }}">
                                            </a></li>
                                            {{ $user->department }}
                                    @endforeach --}}
                                    <?php } else { ?>
                                    <span class="badge bg-primary"><?= get_label('not_assigned', 'Not assigned') ?></span>

                                    <?php }
                                ?>
                                </div>
                                @if(!$auth_user->hasRole('Client'))
                                <div class="mb-3 text-end">
                                    <label class="form-label" for="end_date"><?= get_label('clients', 'Clients') ?></label>
                                    <ul style="    text-align: end;
                                    justify-content: end;" class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                                        <?php
                                    if ($clients->count() > 0) { ?>
    
                                        @foreach ($clients as $client)
                                            <li class="avatar avatar-sm pull-up"
                                                title="{{ $client->first_name }} {{ $client->last_name }}"><a
                                                    href="/clients/profile/{{ $client->id }}" target="_blank">
                                                    <img src="{{ $client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg') }}"
                                                        class="rounded-circle"
                                                        alt="{{ $client->first_name }} {{ $client->last_name }}">
                                                </a></li>
                                        @endforeach
                                        <?php } else { ?>
                                        <span class="badge bg-primary"><?= get_label('not_assigned', 'Not assigned') ?></span>
    
                                        <?php }
                                    ?>
                                    </ul>
                                </div>
                                @endif

                            </div>
                          
                        </div>

                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label"
                                    for="description"><?= get_label('description', 'Description') ?></label>
                                <div class="input-group input-group-merge">
                                    <textarea class="form-control" id="description" name="description" rows="5" readonly>{{ $task->description }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row bootstrap snippets" style="margin-right:0px;margin-left:0px">
                                        <div class="col-md-12">
                                            <!-- DIRECT CHAT PRIMARY -->
                                            <div class="box box-primary direct-chat direct-chat-primary">
                                                <div class="box-header with-border">
                                                    <h3 class="box-title">Initiate a conversation for the latest task updates from the team.</h3>

                                                    <div class="box-tools pull-right">
                                                        <span data-toggle="tooltip" title=""
                                                            class="badge bg-light-blue"
                                                            data-original-title="3 New Messages">3</span>
                                                        <button type="button" class="btn btn-box-tool"
                                                            data-widget="collapse"><i class="fa fa-minus"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-box-tool"
                                                            data-toggle="tooltip" title="Contacts"
                                                            data-widget="chat-pane-toggle">
                                                            <i class="fa fa-comments"></i></button>
                                                        <button type="button" class="btn btn-box-tool"
                                                            data-widget="remove"><i class="fa fa-times"></i></button>
                                                    </div>
                                                </div>
                                                <!-- /.box-header -->
                                                <div class="box-body">
                                                    <div class="direct-chat-messages">

                                                        @foreach ($messages as $message)
                                                            @if (!empty($message->user_id))
                                                                @php
                                                                    $clientId = $message->user_id;
                                                                    $client = \App\Models\User::find($clientId);
                                                                @endphp
                                                                <div class="row">
                                                                    <div class="direct-chat-msg col-md-6">
                                                                        <div class="direct-chat-info clearfix">
                                                                            <span class="direct-chat-name "
                                                                                style="float: left">
                                                                                {{ $client->first_name ?? ('' . ' ' . $client->last_name ?? '') }}
                                                                            </span>
                                                                            <span class="direct-chat-timestamp float-right"
                                                                                style="float: right">{{ $message->created_at }}</span>
                                                                        </div>
                                                                        <!-- /.direct-chat-info -->
                                                                        <img class="direct-chat-img"
                                                                            src="{{ $client->photo ? asset('storage/' . $client->photo) : asset('storage/photos/no-image.jpg') }}"
                                                                            alt="Message User Image"><!-- /.direct-chat-img -->
                                                                        <div class="direct-chat-text">
                                                                            {{ $message->message }}
                                                                            @if ($message->attachment)
                                                                                <div class="attachments">
                                                                                    @foreach (explode(',', $message->attachment) as $attachment)
                                                                                        <div class="attachment"
                                                                                            style="display: inline-block; margin-right: 10px;">
                                                                                            <a href="{{ asset('storage/' . trim($attachment)) }}"
                                                                                                target="_blank"
                                                                                                style="color:black">View
                                                                                                File</a>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- /.direct-chat-msg -->
                                                            @elseif (!empty($message->client_id))
                                                                @php
                                                                    $user_id = $message->client_id;
                                                                    $userData = \App\Models\Client::find($user_id);
                                                                @endphp
                                                                <!-- Message to the right -->
                                                                <div class="row">
                                                                    <div class="col-md-6"></div>
                                                                    <div class="direct-chat-msg col-md-6 right">
                                                                        <div class="direct-chat-info clearfix">
                                                                            <span class="direct-chat-name float-right"
                                                                                style="float: right">
                                                                                {{ $userData->first_name ?? ('' . ' ' . $userData->last_name ?? '') }}
                                                                            </span>
                                                                            <span class="direct-chat-timestamp pull-left"
                                                                                style="float: left">
                                                                                {{ $message->created_at }}
                                                                            </span>
                                                                        </div>
                                                                        <!-- /.direct-chat-info -->
                                                                        <img class="direct-chat-img"
                                                                            src="{{ $userData->photo ? asset('storage/' . $userData->photo) : asset('storage/photos/no-image.jpg') }}"
                                                                            alt="Message User Image"><!-- /.direct-chat-img -->
                                                                        <div class="direct-chat-text">
                                                                            {{ $message->message }}

                                                                            @if ($message->attachment)
                                                                                <div class="attachments">
                                                                                    @foreach (explode(',', $message->attachment) as $attachment)
                                                                                        <div class="attachment"
                                                                                            style="display: inline-block; margin-right: 10px;">
                                                                                            <a href="{{ asset('storage/' . trim($attachment)) }}"
                                                                                                target="_blank"
                                                                                                style="color:black">View
                                                                                                File</a>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif

                                                                        </div>

                                                                        <!-- /.direct-chat-text -->
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <!-- /.direct-chat-msg -->
                                                        @endforeach
                                                    </div>



                                                </div>
                                            </div>
                                            <!--/.direct-chat -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($task->status->title !== 'Complete')
                        <form action="{{ route('task_chat', $task->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('post')
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <textarea name="message" class="form-control" placeholder="Type your message here" rows="4" required style="width: 100%;"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12 text-end" style="justify-content:end;">
                                    <div class="input-group" style="justify-content:end;">
                                        <input type="file" multiple name="attachment[]" class="form-control d-none" id="attachment"
                                            accept="image/*, application/pdf, application/msword, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                        <label class="btn btn-primary ms-2" for="attachment">Select File</label>
                                    </div>
                                </div>
                            </div>
                            <div class="selected-files text-muted ms-2"></div>
                            <div class="row mb-3">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </div>
                            </div>
                            @error('attachment.*')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </form>

                        <hr class="mt-5 mb-2">
                        <h5>Close the Task</h5>
                        <form action="{{ route('task_close', $task->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('post')
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input required class="form-check-input" type="checkbox" name="close_task" id="close_task">
                                        <label class="form-check-label" for="close_task">Close Task</label>
                                    </div>
<textarea name="feedback" class="form-control mt-2" id="feedback" placeholder="Enter your feedback (optional)" rows="2" @if($task->feedback) readonly @endif>{{ $task->feedback }}</textarea>

                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </div>
                            </div>
                            @error('attachment.*')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </form>
                        @else
                        <b>feedback</b>
                        <p>{{ $task->feedback }}</p>

                        <form action="{{ route('open_status', $task->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('post')
                            <div class="row mb-3 mt-5">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" required name="open_task" id="open_task">
                                        <label class="form-check-label" for="open_task">Re-Open Task</label>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </div>
                            </div>
                        </form>
                            @endif
                        
                    </div>
                </div>
            </div>
        </div>

        <style>
            .box {
                position: relative;
                border-radius: 3px;
                background: #ffffff;
                border-top: 3px solid #d2d6de;
                margin-bottom: 20px;
                width: 100%;
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            }

            .box.box-primary {
                border-top-color: #3c8dbc;
            }

            .box.box-info {
                border-top-color: #00c0ef;
            }

            .box.box-danger {
                border-top-color: #dd4b39;
            }

            .box.box-warning {
                border-top-color: #f39c12;
            }

            .box.box-success {
                border-top-color: #00a65a;
            }

            .box.box-default {
                border-top-color: #d2d6de;
            }

            .box.collapsed-box .box-body,
            .box.collapsed-box .box-footer {
                display: none;
            }

            .box .nav-stacked>li {
                border-bottom: 1px solid #f4f4f4;
                margin: 0;
            }

            .box .nav-stacked>li:last-of-type {
                border-bottom: none;
            }

            .box.height-control .box-body {
                max-height: 300px;
                overflow: auto;
            }

            .box .border-right {
                border-right: 1px solid #f4f4f4;
            }

            .box .border-left {
                border-left: 1px solid #f4f4f4;
            }

            .box.box-solid {
                border-top: 0;
            }

            .box.box-solid>.box-header .btn.btn-default {
                background: transparent;
            }

            .box.box-solid>.box-header .btn:hover,
            .box.box-solid>.box-header a:hover {
                background: rgba(0, 0, 0, 0.1);
            }

            .box.box-solid.box-default {
                border: 1px solid #d2d6de;
            }

            .box.box-solid.box-default>.box-header {
                color: #444;
                background: #d2d6de;
                background-color: #d2d6de;
            }

            .box.box-solid.box-default>.box-header a,
            .box.box-solid.box-default>.box-header .btn {
                color: #444;
            }

            .box.box-solid.box-primary {
                border: 1px solid #3c8dbc;
            }

            .box.box-solid.box-primary>.box-header {
                color: #fff;
                background: #3c8dbc;
                background-color: #3c8dbc;
            }

            .box.box-solid.box-primary>.box-header a,
            .box.box-solid.box-primary>.box-header .btn {
                color: #fff;
            }

            .box.box-solid.box-info {
                border: 1px solid #00c0ef;
            }

            .box.box-solid.box-info>.box-header {
                color: #fff;
                background: #00c0ef;
                background-color: #00c0ef;
            }

            .box.box-solid.box-info>.box-header a,
            .box.box-solid.box-info>.box-header .btn {
                color: #fff;
            }

            .box.box-solid.box-danger {
                border: 1px solid #dd4b39;
            }

            .box.box-solid.box-danger>.box-header {
                color: #fff;
                background: #dd4b39;
                background-color: #dd4b39;
            }

            .box.box-solid.box-danger>.box-header a,
            .box.box-solid.box-danger>.box-header .btn {
                color: #fff;
            }

            .box.box-solid.box-warning {
                border: 1px solid #f39c12;
            }

            .box.box-solid.box-warning>.box-header {
                color: #fff;
                background: #f39c12;
                background-color: #f39c12;
            }

            .box.box-solid.box-warning>.box-header a,
            .box.box-solid.box-warning>.box-header .btn {
                color: #fff;
            }

            .box.box-solid.box-success {
                border: 1px solid #00a65a;
            }

            .box.box-solid.box-success>.box-header {
                color: #fff;
                background: #00a65a;
                background-color: #00a65a;
            }

            .box.box-solid.box-success>.box-header a,
            .box.box-solid.box-success>.box-header .btn {
                color: #fff;
            }

            .box.box-solid>.box-header>.box-tools .btn {
                border: 0;
                box-shadow: none;
            }

            .box.box-solid[class*='bg']>.box-header {
                color: #fff;
            }

            .box .box-group>.box {
                margin-bottom: 5px;
            }

            .box .knob-label {
                text-align: center;
                color: #333;
                font-weight: 100;
                font-size: 12px;
                margin-bottom: 0.3em;
            }

            .box>.overlay,
            .overlay-wrapper>.overlay,
            .box>.loading-img,
            .overlay-wrapper>.loading-img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%
            }

            .box .overlay,
            .overlay-wrapper .overlay {
                z-index: 50;
                background: rgba(255, 255, 255, 0.7);
                border-radius: 3px;
            }

            .box .overlay>.fa,
            .overlay-wrapper .overlay>.fa {
                position: absolute;
                top: 50%;
                left: 50%;
                margin-left: -15px;
                margin-top: -15px;
                color: #000;
                font-size: 30px;
            }

            .box .overlay.dark,
            .overlay-wrapper .overlay.dark {
                background: rgba(0, 0, 0, 0.5);
            }

            .box-header:before,
            .box-body:before,
            .box-footer:before,
            .box-header:after,
            .box-body:after,
            .box-footer:after {
                content: " ";
                display: table;
            }

            .box-header:after,
            .box-body:after,
            .box-footer:after {
                clear: both;
            }

            .box-header {
                color: #444;
                display: block;
                padding: 10px;
                position: relative;
            }

            .box-header.with-border {
                border-bottom: 1px solid #f4f4f4;
            }

            .collapsed-box .box-header.with-border {
                border-bottom: none;
            }

            .box-header>.fa,
            .box-header>.glyphicon,
            .box-header>.ion,
            .box-header .box-title {
                display: inline-block;
                font-size: 18px;
                margin: 0;
                line-height: 1;
            }

            .box-header>.fa,
            .box-header>.glyphicon,
            .box-header>.ion {
                margin-right: 5px;
            }

            .box-header>.box-tools {
                position: absolute;
                right: 10px;
                top: 5px;
            }

            .box-header>.box-tools [data-toggle="tooltip"] {
                position: relative;
            }

            .box-header>.box-tools.pull-right .dropdown-menu {
                right: 0;
                left: auto;
            }

            .btn-box-tool {
                padding: 5px;
                font-size: 12px;
                background: transparent;
                color: #97a0b3;
            }

            .open .btn-box-tool,
            .btn-box-tool:hover {
                color: #606c84;
            }

            .btn-box-tool.btn:active {
                box-shadow: none;
            }

            .box-body {
                border-top-left-radius: 0;
                border-top-right-radius: 0;
                border-bottom-right-radius: 3px;
                border-bottom-left-radius: 3px;
                padding: 10px;
            }

            .no-header .box-body {
                border-top-right-radius: 3px;
                border-top-left-radius: 3px;
            }

            .box-body>.table {
                margin-bottom: 0;
            }

            .box-body .fc {
                margin-top: 5px;
            }

            .box-body .full-width-chart {
                margin: -19px;
            }

            .box-body.no-padding .full-width-chart {
                margin: -9px;
            }

            .box-body .box-pane {
                border-top-left-radius: 0;
                border-top-right-radius: 0;
                border-bottom-right-radius: 0;
                border-bottom-left-radius: 3px;
            }

            .box-body .box-pane-right {
                border-top-left-radius: 0;
                border-top-right-radius: 0;
                border-bottom-right-radius: 3px;
                border-bottom-left-radius: 0;
            }

            .box-footer {
                border-top-left-radius: 0;
                border-top-right-radius: 0;
                border-bottom-right-radius: 3px;
                border-bottom-left-radius: 3px;
                border-top: 1px solid #f4f4f4;
                padding: 10px;
                background-color: #fff;
            }

            .direct-chat .box-body {
                border-bottom-right-radius: 0;
                border-bottom-left-radius: 0;
                position: relative;
                overflow-x: hidden;
                padding: 0;
            }

            .direct-chat.chat-pane-open .direct-chat-contacts {
                -webkit-transform: translate(0, 0);
                -ms-transform: translate(0, 0);
                -o-transform: translate(0, 0);
                transform: translate(0, 0);
            }

            .direct-chat-messages {
                -webkit-transform: translate(0, 0);
                -ms-transform: translate(0, 0);
                -o-transform: translate(0, 0);
                transform: translate(0, 0);
                /* padding: 10px; */
                /* height: 250px; */
                /* overflow: auto; */
            }

            .direct-chat-msg,
            .direct-chat-text {
                display: block;
            }

            .direct-chat-msg {
                margin-bottom: 10px;
            }

            .direct-chat-msg:before,
            .direct-chat-msg:after {
                content: " ";
                display: table;
            }

            .direct-chat-msg:after {
                clear: both;
            }

            .direct-chat-messages,
            .direct-chat-contacts {
                -webkit-transition: -webkit-transform .5s ease-in-out;
                -moz-transition: -moz-transform .5s ease-in-out;
                -o-transition: -o-transform .5s ease-in-out;
                transition: transform .5s ease-in-out;
            }

            .direct-chat-text {
                border-radius: 5px;
                position: relative;
                padding: 5px 10px;
                background: #d2d6de;
                border: 1px solid #d2d6de;
                margin: 5px 0 0 50px;
                color: #444;
            }

            .direct-chat-text:after,
            .direct-chat-text:before {
                position: absolute;
                right: 100%;
                top: 15px;
                border: solid transparent;
                border-right-color: #d2d6de;
                content: ' ';
                height: 0;
                width: 0;
                pointer-events: none;
            }

            .direct-chat-text:after {
                border-width: 5px;
                margin-top: -5px;
            }

            .direct-chat-text:before {
                border-width: 6px;
                margin-top: -6px;
            }

            .right .direct-chat-text {
                margin-right: 50px;
                margin-left: 0;
            }

            .right .direct-chat-text:after,
            .right .direct-chat-text:before {
                right: auto;
                left: 100%;
                border-right-color: transparent;
                border-left-color: #d2d6de;
            }

            .direct-chat-img {
                border-radius: 50%;
                float: left;
                width: 40px;
                height: 40px;
            }

            .right .direct-chat-img {
                float: right;
            }

            .direct-chat-info {
                display: block;
                margin-bottom: 2px;
                font-size: 12px;
            }

            .direct-chat-name {
                font-weight: 600;
            }

            .direct-chat-timestamp {
                color: #999;
            }

            .direct-chat-contacts-open .direct-chat-contacts {
                -webkit-transform: translate(0, 0);
                -ms-transform: translate(0, 0);
                -o-transform: translate(0, 0);
                transform: translate(0, 0);
            }

            .direct-chat-contacts {
                -webkit-transform: translate(101%, 0);
                -ms-transform: translate(101%, 0);
                -o-transform: translate(101%, 0);
                transform: translate(101%, 0);
                position: absolute;
                top: 0;
                bottom: 0;
                height: 250px;
                width: 100%;
                background: #222d32;
                color: #fff;
                overflow: auto;
            }

            .contacts-list>li {
                border-bottom: 1px solid rgba(0, 0, 0, 0.2);
                padding: 10px;
                margin: 0;
            }

            .contacts-list>li:before,
            .contacts-list>li:after {
                content: " ";
                display: table;
            }

            .contacts-list>li:after {
                clear: both;
            }

            .contacts-list>li:last-of-type {
                border-bottom: none;
            }

            .contacts-list-img {
                border-radius: 50%;
                width: 40px;
                float: left;
            }

            .contacts-list-info {
                margin-left: 45px;
                color: #fff;
            }

            .contacts-list-name,
            .contacts-list-status {
                display: block;
            }

            .contacts-list-name {
                font-weight: 600;
            }

            .contacts-list-status {
                font-size: 12px;
            }

            .contacts-list-date {
                color: #aaa;
                font-weight: normal;
            }

            .contacts-list-msg {
                color: #999;
            }

            .direct-chat-danger .right>.direct-chat-text {
                background: #dd4b39;
                border-color: #dd4b39;
                color: #fff;
            }

            .direct-chat-danger .right>.direct-chat-text:after,
            .direct-chat-danger .right>.direct-chat-text:before {
                border-left-color: #dd4b39;
            }


            .direct-chat-warning .right>.direct-chat-text {
                background: #f39c12;
                border-color: #f39c12;
                color: #fff;
            }

            .direct-chat-warning .right>.direct-chat-text:after,
            .direct-chat-warning .right>.direct-chat-text:before {
                border-left-color: #f39c12;
            }

            .direct-chat-info .right>.direct-chat-text {
                background: #00c0ef;
                border-color: #00c0ef;
                color: #fff;
            }

            .direct-chat-info .right>.direct-chat-text:after,
            .direct-chat-info .right>.direct-chat-text:before {
                border-left-color: #00c0ef;
            }

            .direct-chat-success .right>.direct-chat-text {
                background: #00a65a;
                border-color: #00a65a;
                color: #fff;
            }

            .direct-chat-success .right>.direct-chat-text:after,
            .direct-chat-success .right>.direct-chat-text:before {
                border-left-color: #00a65a;
            }

            .message-container {
                padding: 10px;
                border-radius: 10px;
                margin-bottom: 10px;
            }

            .client-message {
                background-color: #007bff;
                color: #fff;
            }

            .user-message {
                background-color: #f8f9fa;
            }
        </style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('close_task');
        const feedbackTextarea = document.getElementById('feedback');

        checkbox.addEventListener('change', function() {
            if (this.checked) {
                feedbackTextarea.style.display = 'block';
            } else {
                feedbackTextarea.style.display = 'none';
            }
        });
    });
</script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const attachmentInput = document.getElementById('attachment');

                attachmentInput.addEventListener('change', function() {
                    const selectedFilesDisplay = document.querySelector('.selected-files');

                    selectedFilesDisplay.innerHTML = '';

                    if (this.files.length > 0) {
                        for (let i = 0; i < this.files.length; i++) {
                            const fileName = this.files[i].name;
                            const fileItem = document.createElement('div');
                            fileItem.textContent = fileName;
                            selectedFilesDisplay.appendChild(fileItem);
                        }
                    } else {
                        selectedFilesDisplay.textContent = '';
                    }
                });
            });
        </script>


    </div>
@endsection
