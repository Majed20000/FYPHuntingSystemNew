<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - FYP Hunting System</title>

    <!-- Alpine.js (Add this before other scripts) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    @stack('styles')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800">
                            FYP System
                        </a>
                    </div>

                    <!-- Primary Navigation -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        @if(Auth::user()->isStudent())
                            <a href="{{ route('student.dashboard', ['user_id' => Auth::id()]) }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('student.dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('student.view-slots', ['user_id' => Auth::id()]) }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('student.view-slots') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Appointments
                            </a>
                            <a href="{{ route('student.appointments.view', ['user_id' => Auth::id()]) }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('student.appointments.view') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                My Appointments
                            </a>
                            <a href="{{ route('student.browse-proposals', ['user_id' => Auth::id()]) }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('student.browse-proposals') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Browse Proposals
                            </a>
                            <a href="{{ route('student.my-applications', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('student.my-applications') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                My Applications
                            </a>
                            <a href="{{ route('password.change.form', ['user_id' => Auth::id()]) }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('password.change.form') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Change Password
                            </a>
                        @elseif(Auth::user()->isLecturer())
                            <a href="{{ route('lecturer.dashboard', ['user_id' => Auth::id()]) }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('lecturer.dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('lecturer.calendar', ['user_id' => Auth::id()]) }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('lecturer.calendar') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Calendar
                            </a>
                            <a href="{{ route('lecturer.proposals.manage', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('lecturer.proposals.manage') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Manage Proposals
                            </a>
                            <a href="{{ route('lecturer.applications.manage', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('lecturer.applications.manage') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Manage Applications
                            </a>
                            <a href="{{ route('password.change.form', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                       {{ request()->routeIs('password.change.form') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                 Change Password
                             </a>
                        @elseif(Auth::user()->isCoordinator())
                            <a href="{{ route('coordinator.dashboard') }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('coordinator.dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('coordinator.userRegister') }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('coordinator.userRegister') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Manage Users
                            </a>
                            <a href="{{ route('coordinator.timeframes.index') }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('coordinator.timeframes.*') && !request()->routeIs('coordinator.timeframes.quotas.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Timeframes
                            </a>
                            @if($activeTimeframe = \App\Models\Timeframe::where('is_active', true)->first())
                            <a href="{{ route('coordinator.timeframes.quotas.manage', ['timeframe' => $activeTimeframe->id]) }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                      {{ request()->routeIs('coordinator.timeframes.quotas.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Manage Quotas
                            </a>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Secondary Navigation -->
                {{-- @if(Auth::user()->isCoordinator())
                <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
                    <a href="{{ route('coordinator.timeframes.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                              {{ request()->routeIs('coordinator.timeframes.*') && !request()->routeIs('coordinator.timeframes.quotas.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Timeframes
                    </a>

                    @if($activeTimeframe = \App\Models\Timeframe::where('is_active', true)->first())
                        <a href="{{ route('coordinator.timeframes.quotas.manage', ['timeframe' => $activeTimeframe->id]) }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                  {{ request()->routeIs('coordinator.timeframes.quotas.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Manage Quotas
                        </a>
                    @endif
                </div>
                @endif --}}

                <!-- Right side -->
                <div class="flex items-center">
                    @include('components.nav-notification')

                    <form method="POST" action="{{ route('logout') }}" class="ml-3">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto py-6 px-4">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
