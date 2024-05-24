@if (Request::is('projects/*') || Request::is('tasks/*') || Request::is('status/*'))
<div class="modal fade" id="create_status_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content form-submit-event" action="{{url('/status/store')}}" method="POST">
            <input type="hidden" name="dnr">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_status', 'Create status') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" id="nameBasic" class="form-control" name="title" placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span class="asterisk">*</span></label>
                        <select class="form-select" id="color" name="color">
                            <option class="badge bg-label-primary" value="primary" {{ old('color') == "primary" ? "selected" : "" }}>
                                <?= get_label('primary', 'Primary') ?>
                            </option>
                            <option class="badge bg-label-secondary" value="secondary" {{ old('color') == "secondary" ? "selected" : "" }}><?= get_label('secondary', 'Secondary') ?></option>
                            <option class="badge bg-label-success" value="success" {{ old('color') == "success" ? "selected" : "" }}><?= get_label('success', 'Success') ?></option>
                            <option class="badge bg-label-danger" value="danger" {{ old('color') == "danger" ? "selected" : "" }}><?= get_label('danger', 'Danger') ?></option>
                            <option class="badge bg-label-warning" value="warning" {{ old('color') == "warning" ? "selected" : "" }}><?= get_label('warning', 'Warning') ?></option>
                            <option class="badge bg-label-info" value="info" {{ old('color') == "info" ? "selected" : "" }}><?= get_label('info', 'Info') ?></option>
                            <option class="badge bg-label-dark" value="dark" {{ old('color') == "dark" ? "selected" : "" }}><?= get_label('dark', 'Dark') ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?></label>
                </button>
                <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('create', 'Create') ?></label></button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="edit_status_modal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{url('/status/update')}}" class="modal-content form-submit-event" method="POST">
            <input type="hidden" name="id" id="status_id">
            <input type="hidden" name="dnr">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_status', 'Update status') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" id="status_title" class="form-control" name="title" placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span class="asterisk">*</span></label>
                        <select class="form-select" id="status_color" name="color" required>
                            <option class="badge bg-label-primary" value="primary">
                                <?= get_label('primary', 'Primary') ?>
                            </option>
                            <option class="badge bg-label-secondary" value="secondary"><?= get_label('secondary', 'Secondary') ?></option>
                            <option class="badge bg-label-success" value="success"><?= get_label('success', 'Success') ?></option>
                            <option class="badge bg-label-danger" value="danger"><?= get_label('danger', 'Danger') ?></option>
                            <option class="badge bg-label-warning" value="warning"><?= get_label('warning', 'Warning') ?></option>
                            <option class="badge bg-label-info" value="info"><?= get_label('info', 'Info') ?></option>
                            <option class="badge bg-label-dark" value="dark"><?= get_label('dark', 'Dark') ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?></label>
                </button>
                <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
            </div>
        </form>
    </div>
</div>
@endif
@if (Request::is('projects/*') || Request::is('tags/*'))
<div class="modal fade" id="create_tag_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <form class="modal-content form-submit-event" action="{{url('/tags/store')}}" method="POST">
            <input type="hidden" name="dnr">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_tag', 'Create tag') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" id="nameBasic" class="form-control" name="title" placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span class="asterisk">*</span></label>
                        <select class="form-select" id="color" name="color">
                            <option class="badge bg-label-primary" value="primary" {{ old('color') == "primary" ? "selected" : "" }}>
                                <?= get_label('primary', 'Primary') ?>
                            </option>
                            <option class="badge bg-label-secondary" value="secondary" {{ old('color') == "secondary" ? "selected" : "" }}><?= get_label('secondary', 'Secondary') ?></option>
                            <option class="badge bg-label-success" value="success" {{ old('color') == "success" ? "selected" : "" }}><?= get_label('success', 'Success') ?></option>
                            <option class="badge bg-label-danger" value="danger" {{ old('color') == "danger" ? "selected" : "" }}><?= get_label('danger', 'Danger') ?></option>
                            <option class="badge bg-label-warning" value="warning" {{ old('color') == "warning" ? "selected" : "" }}><?= get_label('warning', 'Warning') ?></option>
                            <option class="badge bg-label-info" value="info" {{ old('color') == "info" ? "selected" : "" }}><?= get_label('info', 'Info') ?></option>
                            <option class="badge bg-label-dark" value="dark" {{ old('color') == "dark" ? "selected" : "" }}><?= get_label('dark', 'Dark') ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?></label>
                </button>
                <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('create', 'Create') ?></label></button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="edit_tag_modal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <form class="modal-content form-submit-event" action="/tags/update" method="POST">
            <input type="hidden" name="dnr">
            <input type="hidden" name="id" id="tag_id">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_tag', 'Update tag') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" id="tag_title" class="form-control" name="title" placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span class="asterisk">*</span></label>
                        <select class="form-select" id="tag_color" name="color">
                            <option class="badge bg-label-primary" value="primary">
                                <?= get_label('primary', 'Primary') ?>
                            </option>
                            <option class="badge bg-label-secondary" value="secondary"><?= get_label('secondary', 'Secondary') ?></option>
                            <option class="badge bg-label-success" value="success"><?= get_label('success', 'Success') ?></option>
                            <option class="badge bg-label-danger" value="danger"><?= get_label('danger', 'Danger') ?></option>
                            <option class="badge bg-label-warning" value="warning"><?= get_label('warning', 'Warning') ?></option>
                            <option class="badge bg-label-info" value="info"><?= get_label('info', 'Info') ?></option>
                            <option class="badge bg-label-dark" value="dark"><?= get_label('dark', 'Dark') ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?></label>
                </button>
                <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
            </div>
        </form>
    </div>
</div>
@endif
@if (Request::is('home') || Request::is('todos'))
<div class="modal fade" id="create_todo_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content form-submit-event" action="{{url('/todos/store')}}" method="POST">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_todo', 'Create todo') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('priority', 'Priority') ?> <span class="asterisk">*</span></label>
                        <select class="form-select" name="priority">
                            <option value="low" {{ old('priority') == "low" ? "selected" : "" }}><?= get_label('low', 'Low') ?></option>
                            <option value="medium" {{ old('priority') == "medium" ? "selected" : "" }}><?= get_label('medium', 'Medium') ?></option>
                            <option value="high" {{ old('priority') == "high" ? "selected" : "" }}><?= get_label('high', 'High') ?></option>
                        </select>
                    </div>
                </div>
                <label for="description" class="form-label"><?= get_label('description', 'Description') ?></label>
                <textarea class="form-control" name="description" placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn" class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="edit_todo_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{url('/todos/update')}}" class="modal-content form-submit-event" method="POST">
            <input type="hidden" name="id" id="todo_id">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_todo', 'Update todo') ?></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" id="todo_title" class="form-control" name="title" placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('priority', 'Priority') ?> <span class="asterisk">*</span></label>
                        <select class="form-select" id="todo_priority" name="priority">
                            <option value="low"><?= get_label('low', 'Low') ?></option>
                            <option value="medium"><?= get_label('medium', 'Medium') ?></option>
                            <option value="high"><?= get_label('high', 'High') ?></option>
                        </select>
                    </div>
                </div>
                <label for="description" class="form-label"><?= get_label('description', 'Description') ?></label>
                <textarea class="form-control" id="todo_description" name="description" placeholder="<?= get_label('please_enter_description', 'Please enter description') ?>"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('update', 'Update') ?></span></button>
            </div>
        </form>
    </div>
</div>
@endif

<div class="modal fade" id="default_language_modal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('confirm', 'Confirm!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('set_primary_lang_alert', 'Are you want to set as your primary language?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary" id="confirm"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="leaveWorkspaceModal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= get_label('confirm_leave_workspace', 'Are you sure you want leave this workspace?') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-danger" id="confirm"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create_language_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content form-submit-event" action="{{url('/settings/languages/store')}}" method="POST">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_language', 'Create language') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="For Example: English" />
                        @error('name')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('code', 'Code') ?> <span class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="code" placeholder="For Example: en" />
                        @error('code')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn" class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
            </div>
        </form>
    </div>
</div>
@if (Request::is('leave-requests') || Request::is('leave-requests/*'))
<div class="modal fade" id="create_leave_request_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content form-submit-event" action="{{url('/leave-requests/store')}}" method="POST">
            <input type="hidden" name="dnr">
            <input type="hidden" name="table" value="lr_table">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_leave_requet', 'Create leave request') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    @if (is_admin_or_leave_editor())
                    <label class="form-label" for="user_id"><?= get_label('select_user', 'Select user') ?> <span class="asterisk">*</span></label>
                    <div class="col-12 mb-3">
                        <select class="form-control" name="user_id">
                            <option value=""><?= get_label('select_user', 'Select user') ?></option>
                            @foreach ($users as $user)
                            <option value="{{$user->id}}" <?= $user->id == getAuthenticatedUser()->id ? 'selected' : '' ?>>{{$user->first_name.' '.$user->last_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-5 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('leave_from_date', 'Leave from date') ?> <span class="asterisk">*</span></label>
                        <input type="text" id="start_date" name="from_date" class="form-control" placeholder="" autocomplete="off">
                    </div>
                    <div class="col-5 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('leave_to_date', 'Leave to date') ?> <span class="asterisk">*</span></label>
                        <input type="text" id="end_date" name="to_date" class="form-control" placeholder="" autocomplete="off">
                    </div>
                    <div class="col-2 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('days', 'Days') ?> <span class="asterisk">*</span></label>
                        <input type="text" id="total_days" class="form-control" value="1" placeholder="" disabled>
                    </div>
                </div>
                <label for="description" class="form-label"><?= get_label('leave_reason', 'Leave reason') ?> <span class="asterisk">*</span></label>
                <textarea class="form-control" name="reason" placeholder="Enter leave reason"></textarea>
                @if (is_admin_or_leave_editor())
                <div class="row mt-4">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">

                            <input type="radio" class="btn-check" name="status" id="create_lr_pending" value="pending" checked>
                            <label class="btn btn-outline-primary" for="create_lr_pending"><?= get_label('pending', 'Pending') ?></label>

                            <input type="radio" class="btn-check" name="status" id="create_lr_approved" value="approved">
                            <label class="btn btn-outline-primary" for="create_lr_approved"><?= get_label('approved', 'Approved') ?></label>

                            <input type="radio" class="btn-check" name="status" id="create_lr_rejected" value="rejected">
                            <label class="btn btn-outline-primary" for="create_lr_rejected"><?= get_label('rejected', 'Rejected') ?></label>
                        </div>
                    </div>
                </div>
                @endif

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn" class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="edit_leave_request_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content form-submit-event" action="{{url('/leave-requests/update')}}" method="POST">
            <input type="hidden" name="dnr">
            <input type="hidden" name="table" value="lr_table">
            <input type="hidden" name="id" id="lr_id">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_leave_request', 'Update leave request') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">

                            <input type="radio" class="btn-check" name="status" id="update_lr_pending" value="pending" checked>
                            <label class="btn btn-outline-primary" for="update_lr_pending"><?= get_label('pending', 'Pending') ?></label>

                            <input type="radio" class="btn-check" name="status" id="update_lr_approved" value="approved">
                            <label class="btn btn-outline-primary" for="update_lr_approved"><?= get_label('approved', 'Approved') ?></label>

                            <input type="radio" class="btn-check" name="status" id="update_lr_rejected" value="rejected">
                            <label class="btn btn-outline-primary" for="update_lr_rejected"><?= get_label('rejected', 'Rejected') ?></label>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
            </div>
        </form>
    </div>
</div>
@endif

<div class="modal fade" id="create_contract_type_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content form-submit-event" action="{{url('/contracts/store-contract-type')}}" method="POST">
            <input type="hidden" name="dnr">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_contract_type', 'Create contract type') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="type" placeholder="Please enter contract type" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn" class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="edit_contract_type_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content form-submit-event" action="{{url('/contracts/update-contract-type')}}" method="POST">
            <input type="hidden" name="dnr">
            <input type="hidden" id="update_contract_type_id" name="id">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_contract_type', 'Update contract type') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="type" id="contract_type" placeholder="Please enter contract type" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn" class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
            </div>
        </form>
    </div>
</div>


@if (Request::is('payslips/create') || Request::is('payment-methods'))
<div class="modal fade" id="create_pm_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <form class="modal-content form-submit-event" action="{{url('/payment-methods/store')}}" method="POST">
            <input type="hidden" name="dnr">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_payment_method', 'Create payment method') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="Please enter title" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn" class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="edit_pm_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <form class="modal-content form-submit-event" action="{{url('/payment-methods/update')}}" method="POST">
            <input type="hidden" name="dnr">
            <input type="hidden" id="pm_id" name="id">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_payment_method', 'Update payment method') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="title" id="pm_title" placeholder="Please enter title" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn" class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
            </div>
        </form>
    </div>
</div>
@endif

@if (Request::is('payslips/create') || Request::is('allowances'))
<div class="modal fade" id="create_allowance_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <form class="modal-content form-submit-event" action="{{url('/allowances/store')}}" method="POST">
            <input type="hidden" name="dnr">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_allowance', 'Create allowance') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="Please enter title" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span class="asterisk">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text">{{$general_settings['currency_symbol']}}</span>
                            <input class="form-control" type="number" name="amount" step="0.01" placeholder="Please enter amount">
                        </div>
                        <p class="text-danger text-xs mt-1 error-message"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn" class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="edit_allowance_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <form class="modal-content form-submit-event" action="{{url('/allowances/update')}}" method="POST">
            <input type="hidden" name="dnr">
            <input type="hidden" name="id" id="allowance_id">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_allowance', 'Update allowance') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" class="form-control" id="allowance_title" name="title" placeholder="Please enter title" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span class="asterisk">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text">{{$general_settings['currency_symbol']}}</span>
                            <input class="form-control" type="number" id="allowance_amount" name="amount" step="0.01" placeholder="Please enter amount">
                        </div>
                        <p class="text-danger text-xs mt-1 error-message"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn" class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
            </div>
        </form>
    </div>
</div>
@endif



@if (Request::is('payslips/create') || Request::is('deductions'))
<div class="modal fade" id="create_deduction_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <form class="modal-content form-submit-event" action="{{url('/deductions/store')}}" method="POST">
            <input type="hidden" name="dnr">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_deduction', 'Create deduction') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="Please enter title" />
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span class="asterisk">*</span></label>
                        <select id="deduction_type" name="type" class="form-control">
                            <option value=""><?= get_label('please_select', 'Please select') ?></option>
                            <option value="amount"><?= get_label('amount', 'Amount') ?></option>
                            <option value="percentage"><?= get_label('percentage', 'Percentage') ?></option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3 d-none" id="amount_div">
                        <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span class="asterisk">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text">{{$general_settings['currency_symbol']}}</span>
                            <input class="form-control" type="number" name="amount" step="0.01" placeholder="Please enter amount">
                        </div>
                        <p class="text-danger text-xs mt-1 error-message"></p>
                    </div>
                    <div class="col-md-12 mb-3 d-none" id="percentage_div">
                        <label class="form-label" for=""><?= get_label('percentage', 'Percentage') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="number" name="percentage" placeholder="Please enter percentage">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn" class="btn btn-primary"><?= get_label('create', 'Create') ?></button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="edit_deduction_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <form class="modal-content form-submit-event" action="{{url('/deductions/update')}}" method="POST">
            <input type="hidden" name="dnr">
            <input type="hidden" id="deduction_id" name="id">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_deduction', 'Update deduction') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" class="form-control" id="deduction_title" name="title" placeholder="Please enter title" />
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('type', 'Type') ?> <span class="asterisk">*</span></label>
                        <select id="update_deduction_type" name="type" class="form-control">
                            <option value=""><?= get_label('please_select', 'Please select') ?></option>
                            <option value="amount"><?= get_label('amount', 'Amount') ?></option>
                            <option value="percentage"><?= get_label('percentage', 'Percentage') ?></option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3" id="update_amount_div">
                        <label class="form-label" for=""><?= get_label('amount', 'Amount') ?> <span class="asterisk">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text">{{$general_settings['currency_symbol']}}</span>
                            <input class="form-control" type="number" id="deduction_amount" name="amount" step="0.01" placeholder="Please enter amount">
                        </div>
                        <p class="text-danger text-xs mt-1 error-message"></p>
                    </div>
                    <div class="col-md-12 mb-3" id="update_percentage_div">
                        <label class="form-label" for=""><?= get_label('percentage', 'Percentage') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="number" id="deduction_percentage" name="percentage" placeholder="Please enter percentage">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" id="submit_btn" class="btn btn-primary"><?= get_label('update', 'Update') ?></button>
            </div>
        </form>
    </div>
</div>
@endif


@if (Request::is('notes'))
<div class="modal fade" id="create_note_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content form-submit-event" action="{{url('/notes/store')}}" method="POST">            
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_note', 'Create note') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" id="nameBasic" class="form-control" name="title" placeholder="Enter Title" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('description', 'Description') ?></label>
                        <textarea class="form-control" name="description" placeholder="Please enter description"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span class="asterisk">*</span></label>
                        <select class="form-select" name="color">
                            <option class="badge bg-label-success" value="info" {{ old('color') == "info" ? "selected" : "" }}><?= get_label('green', 'Green') ?></option>
                            <option class="badge bg-label-warning" value="warning" {{ old('color') == "warning" ? "selected" : "" }}><?= get_label('yellow', 'Yellow') ?></option>
                            <option class="badge bg-label-danger" value="danger" {{ old('color') == "danger" ? "selected" : "" }}><?= get_label('red', 'Red') ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?></label>
                </button>
                <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('create', 'Create') ?></label></button>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="edit_note_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content form-submit-event" action="{{url('/notes/update')}}" method="POST">
            <input type="hidden" name="id" id="note_id">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_note', 'Update note') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input type="text" class="form-control" id="note_title" name="title" placeholder="Enter Title" />
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('description', 'Description') ?></label>
                        <textarea class="form-control" id="note_description" name="description" placeholder="Please enter description"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span class="asterisk">*</span></label>
                        <select class="form-select" id="note_color" name="color">
                            <option class="badge bg-label-info" value="info" {{ old('color') == "info" ? "selected" : "" }}><?= get_label('green', 'Green') ?></option>
                            <option class="badge bg-label-warning" value="warning" {{ old('color') == "warning" ? "selected" : "" }}><?= get_label('yellow', 'Yellow') ?></option>
                            <option class="badge bg-label-danger" value="danger" {{ old('color') == "danger" ? "selected" : "" }}><?= get_label('red', 'Red') ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?></label>
                </button>
                <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('update', 'Update') ?></label></button>
            </div>
        </form>
    </div>
</div>



@endif


<div class="modal fade" id="deleteAccountModal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('delete_account_alert', 'Are you sure you want to delete your account?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <form id="formAccountDeactivation" action="/account/destroy/{{getAuthenticatedUser()->id}}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><?= get_label('yes', 'Yes') ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> '</button>
            </div>
            <div class="modal-body">
                <p><?= get_label('delete_alert', 'Are you sure you want to delete?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-danger" id="confirmDelete"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmDeleteSelectedModal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> '</button>
            </div>
            <div class="modal-body">
                <p><?= get_label('delete_selected_alert', 'Are you sure you want to delete selected record(s)?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-danger" id="confirmDeleteSelections"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="duplicateModal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><?= get_label('duplicate_warning', 'Are you sure you want to duplicate?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>

                <button type="submit" class="btn btn-primary" id="confirmDuplicate"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="timerModal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel2"><?= get_label('time_tracker', 'Time tracker') ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                    <div class="stopwatch">
                        <div class="stopwatch_time">
                            <input type="text" name="hour" id="hour" value="00" class="form-control stopwatch_time_input" readonly>
                            <div class="stopwatch_time_lable"><?= get_label('hours', 'Hours') ?></div>
                        </div>
                        <div class="stopwatch_time">
                            <input type="text" name="minute" id="minute" value="00" class="form-control stopwatch_time_input" readonly>
                            <div class="stopwatch_time_lable"><?= get_label('minutes', 'Minutes') ?></div>
                        </div>
                        <div class="stopwatch_time">
                            <input type="text" name="second" id="second" value="00" class="form-control stopwatch_time_input" readonly>
                            <div class="stopwatch_time_lable"><?= get_label('second', 'Second') ?></div>
                        </div>
                    </div>
                    <div class="selectgroup selectgroup-pills d-flex justify-content-around mt-3">
                        <label class="selectgroup-item">
                            <span class="selectgroup-button selectgroup-button-icon" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('start', 'Start') ?>" id="start" onclick="startTimer()"><i class="bx bx-play"></i></span>
                        </label>
                        <label class="selectgroup-item">
                            <span class="selectgroup-button selectgroup-button-icon" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('stop', 'Stop') ?>" id="end" onclick="stopTimer()"><i class="bx bx-stop"></i></span>
                        </label>
                        <label class="selectgroup-item">
                            <span class="selectgroup-button selectgroup-button-icon" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('pause', 'Pause') ?>" id="pause" onclick="pauseTimer()"><i class="bx bx-pause"></i></span>
                        </label>
                    </div>
                    <div class="form-group mb-0 mt-3">
                        <label class="label"><?= get_label('message', 'Message') ?>:</label>
                        <textarea class="form-control" id="time_tracker_message" placeholder="Please Enter Your Message" name="message"></textarea>
                    </div>
                </div>
                @if (getAuthenticatedUser()->can('manage_timesheet'))
                <div class="modal-footer justify-content-center">
                    <a href="/time-tracker" class="btn btn-primary"><i class="bx bxs-time"></i> <?= get_label('view_timesheet', 'View timesheet') ?></a>

                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="stopTimerModal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2"><?= get_label('warning', 'Warning!') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> '</button>
            </div>
            <div class="modal-body">
                <p><?= get_label('stop_timer_alert', 'Are you sure you want to stop the timer?') ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <?= get_label('close', 'Close') ?>
                </button>
                <button type="submit" class="btn btn-danger" id="confirmStop"><?= get_label('yes', 'Yes') ?></button>
            </div>
        </div>
    </div>
</div>