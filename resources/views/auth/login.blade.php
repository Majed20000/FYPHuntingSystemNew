@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500">
    <div class="absolute inset-0 bg-pattern opacity-20"></div>
    
    <div class="relative z-10 mb-6">
        <div class="flex items-center justify-center">
            <i class="fas fa-graduation-cap text-5xl text-white mr-4"></i>
            <h1 class="text-4xl font-bold text-white">FYP Mentor System</h1>
        </div>
    </div>

    <div class="w-full sm:max-w-md relative z-10">
        <div class="bg-white/90 backdrop-blur-md px-8 py-6 shadow-xl rounded-2xl">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Sign In to Your Account</h2>

            @if (session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        <i class="far fa-envelope mr-2"></i>Email Address
                    </label>
                    <div class="mt-1">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm 
                                   placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm
                                   transition duration-150 ease-in-out">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-lock mr-2"></i>Password
                    </label>
                    <div class="mt-1">
                        <input id="password" type="password" name="password" required
                            class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm 
                                   placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm
                                   transition duration-150 ease-in-out">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-user-tag mr-2"></i>Login As
                    </label>
                    <div class="mt-1">
                        <select id="role" name="role" required
                            class="appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm 
                                   placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm
                                   transition duration-150 ease-in-out">
                            <option value="">Select Role</option>
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                            <option value="lecturer" {{ old('role') == 'lecturer' ? 'selected' : '' }}>Lecturer</option>
                            <option value="coordinator" {{ old('role') == 'coordinator' ? 'selected' : '' }}>Coordinator</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white 
                               bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 
                               transform transition duration-150 hover:scale-[1.02]">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-pattern {
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
</style>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
@endpush
@endsection 