@extends('layout')

@section('title')
<?= get_label('contracts', 'Contracts') ?>
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
                        <?= get_label('contracts', 'Contracts') ?>
                    </li>

                </ol>
            </nav>
        </div>
        <div>
            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create_contract_modal"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title=" <?= get_label('create_contract', 'Create contract') ?>"><i class="bx bx-plus"></i></button></a>
            <a href="{{url('/contracts/contract-types')}}"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('contract_types', 'Contract types') ?>"><i class='bx bx-list-ul'></i></button></a>
        </div>
    </div>

    <div class="card mt-4">
        <div class="table-responsive text-nowrap">
            @if ($contracts > 0)

            <div class="row mt-4 mx-2">
                <div class="mb-3 col-md-3">
                    <div class="input-group input-group-merge">
                        <input type="text" id="contract_start_date_between" class="form-control" placeholder="<?= get_label('from_date_between', 'From date between') ?>" autocomplete="off">
                    </div>
                </div>
                <div class="mb-3 col-md-3">
                    <div class="input-group input-group-merge">
                        <input type="text" id="contract_end_date_between" class="form-control" placeholder="<?= get_label('to_date_between', 'To date between') ?>" autocomplete="off">
                    </div>
                </div>


                <div class="col-md-3">
                    <select class="form-select" id="project_filter" aria-label="Default select example">
                        <option value=""><?= get_label('select_project', 'Select project') ?></option>
                        @foreach ($projects as $project)
                        <option value="{{$project->id}}">{{$project->title}}</option>
                        @endforeach
                    </select>
                </div>

                @if (!isClient())
                <div class="col-md-3">
                    <select class="form-select" id="client_filter" aria-label="Default select example">
                        <option value=""><?= get_label('select_client', 'Select client') ?></option>
                        @foreach ($clients as $client)
                        <option value="{{$client->id}}">{{$client->first_name.' '.$client->last_name}}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-3">
                    <select class="form-select" id="type_filter" aria-label="Default select example">
                        <option value=""><?= get_label('select_type', 'Select type') ?></option>
                        @foreach ($contract_types as $type)
                        <option value="{{$type->id}}">{{$type->type}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select class="form-select" id="status_filter" aria-label="Default select example">
                        <option value=""><?= get_label('select_status', 'Select status') ?></option>
                        <option value="signed"><?= get_label('signed', 'Signed') ?></option>
                        <option value="not_signed"><?= get_label('not_signed', 'Not signed') ?></option>
                        <option value="partially_signed"><?= get_label('partially_signed', 'Partially signed') ?></option>
                    </select>
                </div>
            </div>

            <input type="hidden" name="start_date_from" id="contract_start_date_from">
            <input type="hidden" name="start_date_to" id="contract_start_date_to">

            <input type="hidden" name="end_date_from" id="contract_end_date_from">
            <input type="hidden" name="end_date_to" id="contract_end_date_to">

            <input type="hidden" id="data_type" value="contracts">
            <input type="hidden" id="data_table" value="contracts_table">

            <div class="mx-2 mb-2">
                <table id="contracts_table" data-toggle="table" data-loading-template="loadingTemplate" data-url="/contracts/list" data-icons-prefix="bx" data-icons="icons" data-show-refresh="true" data-total-field="total" data-trim-on-search="false" data-data-field="rows" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-side-pagination="server" data-show-columns="true" data-pagination="true" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-query-params="queryParams">
                    <thead>
                        <tr>
                            <th data-checkbox="true"></th>
                            <th data-sortable="true" data-formatter="idFormatter"><?= get_label('id', 'ID') ?></th>
                            <th data-sortable="true" data-field="title"><?= get_label('title', 'Title') ?></th>
                            <th data-sortable="false" data-field="client"><?= get_label('client', 'Client') ?></th>
                            <th data-sortable="false" data-field="project"><?= get_label('project', 'Project') ?></th>
                            <th data-sortable="false" data-field="contract_type"><?= get_label('type', 'Type') ?></th>
                            <th data-sortable="true" data-field="start_date"><?= get_label('starts_at', 'Starts at') ?></th>
                            <th data-sortable="true" data-field="end_date"><?= get_label('ends_at', 'Ends at') ?></th>
                            <th data-sortable="false" data-field="duration" data-visible="false"><?= get_label('duration', 'Duration') ?></th>
                            <th data-sortable="true" data-field="value"><?= get_label('value', 'Value') ?> ({{$general_settings['currency_symbol']}})</th>
                            <th data-sortable="true" data-field="promisor_sign" data-visible="false"><?= get_label('promisor_sign_status', 'Promisor sign status') ?></th>
                            <th data-sortable="true" data-field="promisee_sign" data-visible="false"><?= get_label('promisee_sign_status', 'Promisee sign status') ?></th>
                            <th data-sortable="true" data-field="status"><?= get_label('status', 'Status') ?></th>
                            <th data-sortable="true" data-field="description" data-visible="false"><?= get_label('description', 'Description') ?></th>
                            <th data-sortable="false" data-field="created_by"><?= get_label('created_by', 'Created by') ?></th>
                            <th data-sortable="true" data-field="created_at"><?= get_label('created_at', 'Created at') ?></th>
                            <th data-sortable="true" data-field="updated_at" data-visible="false"><?= get_label('updated_at', 'Updated at') ?></th>
                            <th data-formatter="actionsFormatter"><?= get_label('actions', 'Actions') ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
            @else
            <?php
            $type = 'Contracts'; ?>
            <x-empty-state-card :type="$type" />

            @endif
        </div>
    </div>
</div>
<div class="modal fade" id="create_contract_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content form-submit-event" action="{{url('/contracts/store')}}" method="POST">
            <input type="hidden" name="has_modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('create_contract', 'Create contract') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                    <div class="row">

                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Please enter title">
                        </div>

                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('value', 'Value') ?> <span class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{$general_settings['currency_symbol']}}</span>
                                <input type="number" name="value" class="form-control" min="0" placeholder="Please enter value">
                            </div>
                            <p class="text-danger text-xs mt-1 error-message"></p>
                        </div>

                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('starts_at', 'Starts at') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="start_date" name="start_date" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('ends_at', 'Ends at') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="end_date" name="end_date" class="form-control" placeholder="" autocomplete="off">
                        </div>
                        @if(!isClient())
                        <label class="form-label" for=""><?= get_label('select_client', 'Select client') ?> <span class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-control" name="client_id">
                                <option value=""><?= get_label('select', 'Select') ?></option>
                                @foreach ($clients as $client)
                                <option value="{{$client->id}}">{{$client->first_name.' '.$client->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <label class="form-label" for=""><?= get_label('select_project', 'Select project') ?> <span class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-control" name="project_id">
                                <option value=""><?= get_label('select', 'Select') ?></option>
                                @foreach ($projects as $project)
                                <option value="{{$project->id}}">{{$project->title}}</option>
                                @endforeach
                            </select>
                        </div>

                        <label class="form-label" for=""><?= get_label('select_contract_type', 'Select contract type') ?> <span class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-control" name="contract_type_id">
                                <option value=""><?= get_label('select', 'Select') ?></option>
                                @foreach ($contract_types as $type)
                                <option value="{{$type->id}}">{{$type->type}}</option>
                                @endforeach
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create_contract_type_modal"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title=" <?= get_label('create_contract_type', 'Create contract type') ?>"><i class="bx bx-plus"></i></button></a>
                                <a href="/contracts/contract-types"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="<?= get_label('manage_contract_types', 'Manage contract types') ?>"><i class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                    </div>
                    <label for="description" class="form-label"><?= get_label('description', 'Description') ?> <span class="asterisk">*</span></label>
                    <textarea class="form-control" name="description" id="contract_description" placeholder="Please enter description"></textarea>
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

<div class="modal fade" id="edit_contract_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content form-submit-event" action="{{url('/contracts/update')}}" method="POST">
            <input type="hidden" name="has_modal">
            <input type="hidden" id="contract_id" name="id">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"><?= get_label('update_contract', 'Update contract') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-body">
                    <div class="row">

                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="Please enter title">
                        </div>

                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('value', 'Value') ?> <span class="asterisk">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">{{$general_settings['currency_symbol']}}</span>
                                <input type="number" id="value" name="value" class="form-control" min="0" placeholder="Please enter value">
                            </div>
                            <p class="text-danger text-xs mt-1 error-message"></p>
                        </div>

                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('starts_at', 'Starts at') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="update_start_date" name="start_date" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-6 mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('ends_at', 'Ends at') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="update_end_date" name="end_date" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <label class="form-label" for=""><?= get_label('select_client', 'Select client') ?> <span class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-control" id="client_id" name="client_id">
                                <option value=""><?= get_label('select', 'Select') ?></option>
                                @foreach ($clients as $client)
                                <option value="{{$client->id}}">{{$client->first_name.' '.$client->last_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <label class="form-label" for=""><?= get_label('select_project', 'Select project') ?> <span class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-control" id="project_id" name="project_id">
                                <option value=""><?= get_label('select', 'Select') ?></option>
                                @foreach ($projects as $project)
                                <option value="{{$project->id}}">{{$project->title}}</option>
                                @endforeach
                            </select>
                        </div>

                        <label class="form-label" for=""><?= get_label('select_contract_type', 'Select contract type') ?> <span class="asterisk">*</span></label>
                        <div class="col-12 mb-3">
                            <select class="form-control" id="contract_type_id" name="contract_type_id">
                                <option value=""><?= get_label('select', 'Select') ?></option>
                                @foreach ($contract_types as $type)
                                <option value="{{$type->id}}">{{$type->type}}</option>
                                @endforeach
                            </select>
                            <div class="mt-2">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create_contract_type_modal"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title=" <?= get_label('create_contract_type', 'Create contract type') ?>"><i class="bx bx-plus"></i></button></a>
                                <a href="/contracts/contract-types"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="<?= get_label('manage_contract_types', 'Manage contract types') ?>"><i class="bx bx-list-ul"></i></button></a>
                            </div>
                        </div>
                    </div>
                    <label for="description" class="form-label"><?= get_label('description', 'Description') ?> <span class="asterisk">*</span></label>
                    <textarea class="form-control" name="description" id="update_contract_description" placeholder="Please enter description"></textarea>
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

<script>
    var label_update = '<?= get_label('update', 'Update') ?>';
    var label_delete = '<?= get_label('delete', 'Delete') ?>';
    var label_duplicate = '<?= get_label('duplicate', 'Duplicate') ?>';
    var label_contract_id_prefix = '<?= get_label('contract_id_prefix', 'CTR - ') ?>';
</script>
<script src="{{asset('assets/js/pages/contracts.js')}}"></script>
@endsection