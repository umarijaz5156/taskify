@extends('layout')

@section('title')
<?= get_label('update_user_profile', 'Update user profile') ?>
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
                    <li class="breadcrumb-item">
                        <a href="{{url('/users')}}"><?= get_label('users', 'Users') ?></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{url('/users/profile/'.$user->id)}}">{{$user->first_name.' '.$user->last_name}}</a>
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
            <form action="{{url('/users/update_user/' . $user->id)}}" class="form-submit-event" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="redirect_url" value="/users">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="firstName" class="form-label"><?= get_label('first_name', 'First name') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="text" id="first_name" name="first_name" placeholder="Enter first name" value="{{ $user->first_name }}">

                        @error('first_name')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror


                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="lastName" class="form-label"><?= get_label('last_name', 'Last name') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="text" name="last_name" placeholder="Enter last name" id="last_name" value="{{ $user->last_name }}">

                        @error('last_name')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror

                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="role"><?= get_label('role', 'Role') ?> <span class="asterisk">*</span></label>
                        <div class="input-group">
                            <select class="form-select text-capitalize" id="role" name="role">
                                @foreach ($roles as $role)

                                <option value="{{$role->id}}" <?php if ($user->getRoleNames()->first() == $role->name) {
                                                                    echo 'selected';
                                                                }  ?>>{{$role->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        @error('role')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror

                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="email" class="form-label"><?= get_label('email', 'E-mail') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="text" id="email" name="email" placeholder="Enter email" value="{{ $user->email }}">

                        @error('email')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="phone"><?= get_label('phone_number', 'Phone number') ?> <span class="asterisk">*</span></label>
                        <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter phone number" value="{{ $user->phone }}">
                        @error('phone')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <div class="mb-3 col-md-6">
                        <label for="address" class="form-label"><?= get_label('address', 'Address') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="text" id="address" name="address" placeholder="Enter address" value="{{ $user->address }}">
                        @error('address')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror

                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="city" class="form-label"><?= get_label('city', 'City') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="text" id="city" name="city" placeholder="Enter city" value="{{ $user->city }}">

                        @error('city')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="state" class="form-label"><?= get_label('state', 'State') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="text" id="state" name="state" placeholder="Enter state" value="{{ $user->state }}">

                        @error('state')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="country" class="form-label"><?= get_label('country', 'Country') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="text" id="country" name="country" placeholder="Enter country" value="{{ $user->country }}">

                        @error('country')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="zip" class="form-label"><?= get_label('zip_code', 'ZIP code') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="text" id="zip" name="zip" placeholder="Enter ZIP code" value="{{ $user->zip }}">

                        @error('zip')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="dob" class="form-label"><?= get_label('date_of_birth', 'Date of birth') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="text" id="dob" name="dob" value="{{ $user->dob?format_date($user->dob) : ''}}" placeholder="<?= get_label('please_select', 'Please select') ?>" autocomplete="off">

                        @error('dob')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="doj" class="form-label"><?= get_label('date_of_join', 'Date of joining') ?> <span class="asterisk">*</span></label>
                        <input class="form-control" type="text" id="doj" name="doj" value="{{ $user->doj?format_date($user->doj) : ''}}" placeholder="<?= get_label('please_select', 'Please select') ?>" autocomplete="off">

                        @error('doj')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-12">
                        <label for="photo" class="form-label"><?= get_label('profile_picture', 'Profile picture') ?></label>
                        <div class="d-flex align-items-start align-items-sm-center gap-4 my-3">
                            <img src="{{$user->photo ? asset('storage/' . $user->photo) : asset('storage/photos/no-image.jpg')}}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                            <div class="button-wrapper">
                                <div class="input-group d-flex">

                                    <input type="file" class="form-control" id="inputGroupFile02" name="upload">
                                </div>

                                @error('upload')
                                <p class="text-danger text-xs mt-1">{{ $message }}</p>
                                @enderror

                                <p class="text-muted mt-2"><?= get_label('allowed_jpg_png', 'Allowed JPG or PNG.') ?></p>
                            </div>

                        </div>
                    </div>

                    @role('admin')

                    <div class="mb-3 col-md-6">
                        <label class="form-label" for=""><?= get_label('status', 'Status') ?> (<small class="text-muted mt-2">If deactivated, the user won't be able to log in to their account.</small>)</label>
                        <div class="">
                            <div class="btn-group btn-group d-flex justify-content-center" role="group" aria-label="Basic radio toggle button group">

                                <input type="radio" class="btn-check" id="user_active" name="status" value="1" <?= $user->status == 1 ? 'checked' : '' ?>>
                                <label class="btn btn-outline-primary" for="user_active"><?= get_label('active', 'Active') ?></label>

                                <input type="radio" class="btn-check" id="user_deactive" name="status" value="0" <?= $user->status == 0 ? 'checked' : '' ?>>
                                <label class="btn btn-outline-primary" for="user_deactive"><?= get_label('deactive', 'Deactive') ?></label>

                            </div>
                        </div>
                    </div>
                    @endrole

                    <div class="mb-3 col-md-6">
                        <label for="department" class="form-label"><?= get_label('Department', 'Department') ?> <span class="asterisk">*</span></label>
                        {{-- <select class="form-select" id="department" name="department">
                            <option value="">Select Department</option>
                            <option value="Web Development/Designing" {{ $user->department == 'Web Development/Designing' ? 'selected' : '' }}>Web Development/Designing</option>
                            <option value="Fix Bug" {{ $user->department == 'Fix Bug' ? 'selected' : '' }}>Fix Bug</option>
                            <option value="Graphic Designing" {{ $user->department == 'Graphic Designing' ? 'selected' : '' }}>Graphic Designing</option>
                            <option value="SEO" {{ $user->department == 'SEO' ? 'selected' : '' }}>SEO</option>
                            <option value="Content Writing" {{ $user->department == 'Content Writing' ? 'selected' : '' }}>Content Writing</option>
                            <option value="Marketing" {{ $user->department == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                            <option value="Domain/Hosting/cPanel Support" {{ $user->department == 'Domain/Hosting/cPanel Support' ? 'selected' : '' }}>Domain/Hosting/cPanel Support</option>
                            <option value="HR/Customer Support" {{ $user->department == 'HR/Customer Support' ? 'selected' : '' }}>HR/Customer Support</option>
                        </select> --}}
                        <select class="form-select" id="department" name="department" required>
                            <option value="">Select Department</option>
                               <option value="Project Manager" 
                               {{ $user->department == 'Project Manager' ? 'selected' : '' }}>Project Manager</option>
                            <option value="Web Developer/Designer" {{ $user->department == 'Web Developer/Designer' ? 'selected' : '' }}>Web Developer/Designer</option>
                            <option value="Graphic Designer" {{ $user->department == 'Graphic Designer' ? 'selected' : '' }}>Graphic Designer</option>
                            <option value="SEO Expert" {{ $user->department == 'SEO Expert' ? 'selected' : '' }}>SEO Expert</option>
                            <option value="Content Writer" {{ $user->department == 'Content Writer' ? 'selected' : '' }}>Content Writer</option>
                            <option value="Marketing Expert" {{ $user->department == 'Marketing Expert' ? 'selected' : '' }}>Marketing Expert</option>
                            <option value="Agent" {{ $user->department == 'Agent' ? 'selected' : '' }}>Agent</option>
                        </select>
                        
                    
                        @error('department')
                        <p class="text-danger text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" id="submit_btn" class="btn btn-primary me-2"><?= get_label('update', 'Update') ?></button>
                        <button type="reset" class="btn btn-outline-secondary"><?= get_label('cancel', 'Cancel') ?></button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection