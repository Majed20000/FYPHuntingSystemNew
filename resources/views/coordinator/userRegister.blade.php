@extends('layouts.dashboard') <!-- Extend the dashboard layout -->

@section('title', 'User Management') <!-- The title of the page -->

@section('content') <!-- Start of the content of the page -->
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manage Users</h1>
        <button onclick="openRegisterModal()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Register New User
        </button>
    </div>

        <!-- Search and Filter Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form action="{{ route('coordinator.userRegister') }}" method="GET" class="flex gap-4">
                <!-- Search and filter the users -->
                <div class="flex-1"> <!-- The input field to search by name, email, or matric number -->
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by name, email, or matric number"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="w-48"> <!-- The dropdown menu to select by roles -->
                    <select name="role"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Roles</option> <!-- Select by roles -->
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="lecturer" {{ request('role') === 'lecturer' ? 'selected' : '' }}>Lecturer</option>
                        <option value="coordinator" {{ request('role') === 'coordinator' ? 'selected' : '' }}>Coordinator
                        </option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Search
                </button>
            </form>
        </div>

        <!-- Register User Modal -->
        <div id="registerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-600 bg-opacity-60 backdrop-blur-sm hidden">
            <div class="relative w-full max-w-md mx-auto bg-white rounded-2xl shadow-2xl border border-gray-200 animate-fadeIn">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 bg-blue-600 rounded-t-2xl">
                    <div class="flex items-center gap-2">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.104.896-2 2-2s2 .896 2 2-.896 2-2 2-2-.896-2-2zm0 0V7m0 4v4m0 0h4m-4 0H8" />
                        </svg>
                        <h3 class="text-lg font-semibold text-white">Register New User</h3>
                    </div>
                    <button type="button" onclick="closeRegisterModal()" aria-label="Close modal" class="text-white hover:text-blue-200 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-6">
                    <!-- Success Message -->
                    <div id="successMessage" class="hidden mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative transition-all duration-300 ease-in-out">
                        <span class="block sm:inline"></span>
                        <button onclick="this.parentElement.classList.add('hidden')" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <!-- Error Message -->
                    <div id="errorMessage" class="hidden mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded relative transition-all duration-300 ease-in-out">
                        <span class="block sm:inline"></span>
                        <button onclick="this.parentElement.classList.add('hidden')" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form id="registerForm" action="{{ route('coordinator.register.single') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.797.657 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </span>
                                <input type="text" name="name" id="name" required autocomplete="off"
                                    class="pl-10 pr-3 py-2 w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition outline-none">
                            </div>
                            <div id="nameError" class="hidden text-red-500 text-sm mt-1"></div>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 0a4 4 0 11-8 0 4 4 0 018 0zm0 0v1a4 4 0 01-8 0v-1" />
                                    </svg>
                                </span>
                                <input type="email" name="email" id="email" required autocomplete="off"
                                    class="pl-10 pr-3 py-2 w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition outline-none">
                            </div>
                            <div id="emailError" class="hidden text-red-500 text-sm mt-1"></div>
                        </div>
                        <div>
                            <label for="matric_number" class="block text-sm font-medium text-gray-700 mb-1">ID Number</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </span>
                                <input type="text" name="matric_number" id="matric_number" required autocomplete="off"
                                    class="pl-10 pr-3 py-2 w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition outline-none">
                            </div>
                            <div id="matricNumberError" class="hidden text-red-500 text-sm mt-1"></div>
                        </div>
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 118 0v2m-4 4v-4" />
                                    </svg>
                                </span>
                                <select name="role" id="role" required
                                    class="pl-10 pr-3 py-2 w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition outline-none">
                                    <option value="">Select Role</option>
                                    <option value="student">Student</option>
                                    <option value="lecturer">Lecturer</option>
                                    <option value="coordinator">Coordinator</option>
                                </select>
                            </div>
                            <div id="roleError" class="hidden text-red-500 text-sm mt-1"></div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" onclick="closeRegisterModal()"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                                Cancel
                            </button>
                            <button type="submit" id="submitButton"
                                class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-md shadow hover:from-blue-600 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400 flex items-center gap-2 transition">
                                <span id="submitBtnText">Register</span>
                                <svg id="submitSpinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- CSV Upload Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Upload Users</h2>
                <button onclick="window.location.href='{{ asset('templates/user_registration_template.csv') }}'"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Download CSV Template
                </button> <!-- Button to download the CSV template fetched from the assets folder -->
            </div>
            <form action="{{ route('coordinator.register.store') }}" method="POST" enctype="multipart/form-data">
                <!-- Form to upload the CSV file -->
                @csrf
                <div class="flex gap-4">
                    <div class="flex-1">
                        <input type="file" name="csv_file" accept=".csv"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <!-- Box that show name of the file -->
                    </div>

                    <!-- Button to upload the CSV file -->
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Upload CSV
                    </button>
                </div>
            </form>
        </div>

        <!-- Messages -->
        @if (session()->has('success'))
            <!-- Display the success message of uploading the CSV file -->
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <!-- Display the error message of uploading the CSV file -->
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('failures'))
            <!-- Display the error message of registering the users -->
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
                {{ session('failures') }}
                @if (session()->has('errors'))
                    <ul class="list-disc list-inside mt-2">
                        @foreach (session('errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- User Count and Range Information -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                    </div>
                    <div class="text-sm text-gray-600">
                        Total Users: {{ $users->total() }}
                    </div>
                </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supervisor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($users as $user)
                        <!-- Display the users, fetched from the table user -->
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-sm rounded-full capitalize
                                    @if($user->role === 'student')
                                        bg-blue-100 text-blue-800
                                    @elseif($user->role === 'lecturer')
                                        bg-green-100 text-green-800
                                    @elseif($user->role === 'coordinator')
                                        bg-purple-100 text-purple-800
                                    @endif">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->matric_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->role === 'student')
                                    @php
                                        $proposal = \App\Models\ProjectProposal::where('student_id', $user->id)
                                            ->where('status', \App\Models\ProjectProposal::STATUS_ACCEPTED)
                                            ->first();
                                    @endphp
                                    {{ $proposal ? $proposal->lecturer->name : 'Not Assigned' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="deleteUser({{ $user->id }})" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button> <!-- Button to delete the user -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <!-- Pagination Links -->
        <div class="mt-4">
            @if ($users->hasPages())
                <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center">
                    {{-- Previous Page Link --}}
                    @if ($users->onFirstPage())
                        <span
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-l-md">
                            Previous
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" rel="prev"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50">
                            Previous
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    <div class="hidden md:inline-flex">
                        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            @if ($page == $users->currentPage())
                                <span
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-gray-300">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    </div>

                    {{-- Next Page Link --}}
                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" rel="next"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50">
                            Next
                        </a>
                    @else
                        <span
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-r-md">
                            Next
                        </span>
                    @endif
                </nav>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Function to delete the user
            function deleteUser(userId) {
                if (confirm('Are you sure you want to delete this user?')) {
                    fetch(`/coordinator/register/${userId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error deleting user');
                        });
                }
            }

            // Modal functions
            function openRegisterModal() {
                document.getElementById('registerModal').classList.remove('hidden');
                resetForm();
            }

            function closeRegisterModal() {
                document.getElementById('registerModal').classList.add('hidden');
                resetForm();
            }

            function resetForm() {
                document.getElementById('registerForm').reset();
                document.getElementById('successMessage').classList.add('hidden');
                document.getElementById('errorMessage').classList.add('hidden');
                // Hide all error messages
                ['name', 'email', 'matric_number', 'role'].forEach(field => {
                    const errorDiv = document.getElementById(`${field}Error`);
                    if (errorDiv) {
                        errorDiv.classList.add('hidden');
                        errorDiv.textContent = '';
                    }
                });
            }

            function showSuccess(message) {
                const successDiv = document.getElementById('successMessage');
                successDiv.querySelector('span').textContent = message;
                successDiv.classList.remove('hidden');
            }

            function showError(message) {
                const errorDiv = document.getElementById('errorMessage');
                errorDiv.querySelector('span').textContent = message;
                errorDiv.classList.remove('hidden');
            }

            function showFieldError(field, message) {
                const errorDiv = document.getElementById(`${field}Error`);
                if (errorDiv) {
                    errorDiv.textContent = message;
                    errorDiv.classList.remove('hidden');
                }
            }

            // Handle form submission
            document.getElementById('registerForm').addEventListener('submit', function(e) {
                e.preventDefault();

                // Reset all error messages
                ['name', 'email', 'matric_number', 'role'].forEach(field => {
                    const errorDiv = document.getElementById(`${field}Error`);
                    if (errorDiv) {
                        errorDiv.classList.add('hidden');
                        errorDiv.textContent = '';
                    }
                });

                const submitButton = document.getElementById('submitButton');
                submitButton.disabled = true;
                submitButton.innerHTML = 'Registering...';

                const formData = new FormData(this);

                // Log form data for debugging
                console.log('Form data:', Object.fromEntries(formData));

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json().then(data => {
                            if (!response.ok) {
                                throw data;
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        console.log('Success data:', data);
                        if (data.success) {
                            showSuccess(data.message || 'User registered successfully!');
                            // Reset form
                            this.reset();
                            // Reload the page after 2 seconds
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            if (data.errors) {
                                // Handle validation errors
                                Object.keys(data.errors).forEach(field => {
                                    showFieldError(field, data.errors[field][0]);
                                });
                            } else {
                                showError(data.message || 'Error registering user');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error details:', error);
                        if (error.errors) {
                            // Handle validation errors
                            Object.keys(error.errors).forEach(field => {
                                showFieldError(field, error.errors[field][0]);
                            });
                        } else {
                            showError(error.message || 'An unexpected error occurred. Please try again.');
                        }
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = 'Register';
                    });
            });
        </script>
    @endpush
@endsection
