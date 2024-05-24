<!-- Navbar -->

<?php

use App\Models\Language;
use App\Models\Notification;
$user_id =   getAuthenticatedUser()->id;
$notifications = Notification::where('to_id', $user_id)
    ->orderBy('created_at', 'desc') // Sort by created_at column in descending order
    ->get();
$newNotificationCount = $notifications->where('is_read', 0)->count();


$current_language = Language::where('code', app()->getLocale())->get(['name', 'code']);
$default_language = getAuthenticatedUser()->lang;
?>
@authBoth
<div id="section-not-to-print">
    <nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">

        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

            <div class="nav-item d-flex align-items-center">

                <form action="/search" method="get" class="d-flex align-items-center m-0" id="search-form">
                    <button type="submit" class="btn btn-default p-0"><i class="bx bx-search fs-4 lh-0"></i></button>
                    <input type="text" name="query" value="<?php if (isset($query)) {
                                                                print_r($query);
                                                            } ?>" class="form-control border-0 shadow-none" id="search-input" placeholder="<?= get_label('search', 'Search') ?>...">
                </form>
            </div>


            <ul class="navbar-nav flex-row align-items-center ms-auto">
                @if (config('constants.ALLOW_MODIFICATION') === 0)
                <li><span class="badge bg-danger demo-mode">Demo mode</span><span class="demo-mode-icon-only">
                        <i class='bx bx-error-alt text-danger'></i>
                    </span></li>
                @endif
                <style>
                    .dropdown-menu_noti {
                        max-height: 500px; /* Limit the maximum height */
    overflow-y: auto; /* Enable vertical scrolling */
    width: 330px; /* Set a fixed width */
}

.dropdown-item {
    /* Allow long messages to wrap into multiple lines */
    white-space: normal;
    word-wrap: break-word;
    width:250px;
}
.fa-stack[data-count]:after{
  position:absolute;
  right:0%;
  top:1%;
  content: attr(data-count);
  font-size:30%;
  padding:.6em;
  border-radius:999px;
  line-height:.75em;
  color: white;
  background:rgba(255,0,0,.85);
  text-align:center;
  min-width:2em;
  font-weight:bold;
}
.dropstart .dropdown-toggle::before, .dropend .dropdown-toggle::after {
display: none;
}
/* Width */
.dropdown-menu_noti::-webkit-scrollbar {
    width: 8px;
}

/* Track */
.dropdown-menu_noti::-webkit-scrollbar-track {
    background: #f1f1f1; /* Or any color you prefer */
}

/* Handle */
.dropdown-menu_noti::-webkit-scrollbar-thumb {
    background: #888; /* Color of the scrollbar handle */
}

/* Handle on hover */
.dropdown-menu_noti::-webkit-scrollbar-thumb:hover {
    background: #555; /* Darker color when hovering over the scrollbar handle */
}




                    </style>
                                
                                <li class="nav-item navbar-dropdown dropdown-user dropdown mx-2">
                                    <div class="btn-group dropend px-2 position-relative">
                                        <button type="button" class="btn p-0 dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="menu-icon tf-icons bx bx-bell text-primary"></i>
                                            @if($newNotificationCount > 0)
                                                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $newNotificationCount }}</span>
                                            @endif
                                        </button>
                                        
                                        <ul class="dropdown-menu dropdown-menu_noti language-dropdown dropdown-menu-start">
                                            @if($notifications->isEmpty())
                                                <li class="dropdown-item">
                                                    <a href="#">No notifications</a>
                                                </li>
                                            @else
                                            @foreach($notifications as $notification)
                                            <li class="dropdown-item">
                                                <a href="#" id="notification_{{ $notification->id }}" class="notification-link {{ $notification->is_read ? 'text-dark' : '' }}" data-notification-id="{{ $notification->id }}" data-action-id="{{ $notification->action_id }}">
                                                    {{ $notification->message }}
                                                </a>
                                            </li>
                                        @endforeach
                                        
                                                <div class="dropdown-divider"></div>
                                               
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        // Get all notification links
                                        var notificationLinks = document.querySelectorAll('.notification-link');
                                
                                        // Attach click event listener to each notification link
                                        notificationLinks.forEach(function (link) {
                                            link.addEventListener('click', function (event) {
                                                event.preventDefault(); // Prevent default link behavior
                                
                                                // Get notification id and action id from data attributes
                                                var notificationId = this.getAttribute('data-notification-id');
                                                var actionId = this.getAttribute('data-action-id');
                                
                                                // Construct URL for AJAX request
                                                var url = '/tasks/information/' + actionId;
                                                var ajxUrl = '/tasks/notification/read/' + notificationId;
                                                
                                                 $.ajax({
                                                     url: ajxUrl,
                                                     type: 'GET',
                                                     success: function(response) {
                                                        window.location.href = '/tasks/information/' + actionId;
                                                     },
                                                     error: function(xhr, status, error) {
                                                        window.location.href = '/tasks/information/' + actionId;
                                                     }
                                                 });
                                
                                                // For demonstration purposes, logging the notification id and action id
                                                console.log('Notification ID:', notificationId);
                                                console.log('Action ID:', actionId);
                                                console.log('URL:', url);
                                            });
                                        });
                                    });
                                </script>
                                
            
                
                
                <li class="nav-item navbar-dropdown dropdown-user dropdown mx-2">
                    <div class="btn-group dropend px-2">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="icon-only"><i class='bx bx-globe'></i></span> <span class="language-name"><?= $current_language[0]['name'] ?? '' ?></span>
                        </button>
                        <ul class="dropdown-menu language-dropdown">
                            @foreach ($languages as $language)
                            <?php $checked = $language->code == app()->getLocale() ? "<i class='menu-icon tf-icons bx bx-check-square text-primary'></i>" : "<i class='menu-icon tf-icons bx bx-square text-solid'></i>" ?>
                            <li class="dropdown-item">
                                <a href="{{ url('/settings/languages/switch/' . $language->code) }}">
                                    <?= $checked ?>

                                    {{ $language->name }}

                                </a>
                            </li>
                            @endforeach

                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            @if ($current_language[0]['code'] == $default_language)
                            <li><span class="badge bg-primary mx-5 mb-1 mt-1" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('current_language_is_your_primary_language', 'Current language is your primary language') ?>"><?= get_label('primary', 'Primary') ?></span></li>
                            @else
                            <a href="javascript:void(0);"><span class="badge bg-secondary mx-5 mb-1 mt-1" id="set-as-default" data-lang="{{app()->getLocale()}}" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-original-title="<?= get_label('set_current_language_as_your_primary_language', 'Set current language as your primary language') ?>"><?= get_label('set_as_primary', 'Set as primary') ?></span></a>
                            @endif

                        </ul>
                    </div>
                    </button>
                </li>
             
                
                <li class="nav-item navbar-dropdown dropdown-user dropdown mt-3 mx-2">
                    <p class="nav-item" id="hi-greeting">
                        <span class="hide-mobile"><?= getAuthenticatedUser()->first_name ?></span>
                        <span class="show-mobile"><?= get_label('hi', 'Hi') ?>👋</span>
                        <span class="show-mobile">{{getAuthenticatedUser()->first_name}}</span>
                    </p>

                </li>
                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="{{getAuthenticatedUser()->photo ? asset('storage/' . getAuthenticatedUser()->photo) : asset('storage/photos/no-image.jpg')}}" alt class="w-px-40 h-auto rounded-circle" />
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="{{getAuthenticatedUser()->photo ? asset('storage/' . getAuthenticatedUser()->photo) : asset('storage/photos/no-image.jpg')}}" alt class="w-px-40 h-auto rounded-circle" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block">{{getAuthenticatedUser()->first_name}} {{getAuthenticatedUser()->last_name}}</span>
                                        <small class="text-muted text-capitalize">
                                            {{getAuthenticatedUser()->getRoleNames()->first()}}
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/account/{{ getAuthenticatedUser()->id }}">
                                <i class="bx bx-user me-2"></i>
                                <span class="align-middle"><?= get_label('my_profile', 'My Profile') ?></span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <form action="/logout" method="POST" class="dropdown-item">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bx bx-log-out-circle"></i> <?= get_label('logout', 'Logout') ?></button>

                            </form>
                        </li>
                    </ul>
                </li>

                <!--/ User -->
            </ul>
        </div>
    </nav>
</div>
@else
@endauth

<!-- / Navbar -->