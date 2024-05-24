<!-- workspaces -->

<div class="card mt-4">
    <div class="table-responsive text-nowrap">
        {{$slot}}
        @if (is_countable($workspaces) && count($workspaces) > 0)

        <div class="row mt-4 mx-2">
            @role('admin')
            <div class="col-md-3">
                <select class="form-select" id="workspace_user_filter" aria-label="Default select example">
                    <option value=""><?= get_label('select_user', 'Select user') ?></option>
                    @foreach ($users as $user)
                    <option value="{{$user->id}}">{{$user->first_name.' '.$user->last_name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <select class="form-select" id="workspace_client_filter" aria-label="Default select example">
                    <option value=""><?= get_label('select_client', 'Select client') ?></option>
                    @foreach ($clients as $client)
                    <option value="{{$client->id}}">{{$client->first_name.' '.$client->last_name}}</option>
                    @endforeach
                </select>
            </div>
            @endrole
        </div>

        <input type="hidden" id="data_type" value="workspaces">
        <div class="mx-2 mb-2">
            <table id="table" data-toggle="table" data-loading-template="loadingTemplate" data-url="/workspaces/list" data-icons-prefix="bx" data-icons="icons" data-show-refresh="true" data-total-field="total" data-trim-on-search="false" data-data-field="rows" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-side-pagination="server" data-show-columns="true" data-pagination="true" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-query-params="queryParams">
                <thead>
                    <tr>
                        <th data-checkbox="true"></th>
                        <th data-sortable="true" data-field="id"><?= get_label('id', 'ID') ?></th>
                        <th data-sortable="true" data-field="title"><?= get_label('title', 'Title') ?></th>
                        <th data-field="users" data-formatter="userFormatter"><?= get_label('users', 'Users') ?></th>
                        <th data-field="clients" data-formatter="clientFormatter"><?= get_label('clients', 'Clients') ?></th>
                        <th data-formatter="actionsFormatter"><?= get_label('actions', 'Actions') ?></th>
                    </tr>
                </thead>
            </table>
        </div>
        @else
        <?php
        $type = 'Workspaces'; ?>
        <x-empty-state-card :type="$type" />
        @endif
    </div>
</div>