@extends('layouts.dashboard')

@section('title', 'Change Password')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-4">Change Password</h2>

                <!-- Display success or error message to change the password -->
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span> <!-- Display the success message -->
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Form to change the password -->
                <form method="POST" action="{{ route('password.change') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="current_password" class="block text-lg font-medium text-gray-700">Current Password</label>
                        <input type="password" name="current_password" id="current_password" required
                               class="mt-1 block w-3/4 rounded-lg border-gray-300 shadow-lg focus:border-blue-500 focus:ring-blue-500 text-lg">
                        @error('current_password') <!-- Display the error message checking the current password -->
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password" class="block text-lg font-medium text-gray-700">New Password</label>
                        <input type="password" name="new_password" id="new_password" required
                               class="mt-1 block w-3/4 rounded-lg border-gray-300 shadow-lg focus:border-blue-500 focus:ring-blue-500 text-lg">
                        @error('new_password') <!-- Display the error message checking the new password -->
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-lg font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                               class="mt-1 block w-3/4 rounded-lg border-gray-300 shadow-lg focus:border-blue-500 focus:ring-blue-500 text-lg">
                        @error('new_password_confirmation') <!-- Display the error message checking the confirm new password -->
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 border border-transparent rounded-full font-semibold text-sm text-white uppercase tracking-widest hover:from-blue-600 hover:to-blue-800 active:from-blue-700 active:to-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg">
                            Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
