@extends('layouts.member')

@section('title', 'Edit Profile')

@section('member-content')
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl shadow px-6 py-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Edit Profile</h2>
            <a href="{{ route('member.profile') }}" class="text-sm text-blue-600 hover:underline">
                ← Back
            </a>
        </div>

        {{-- FORM CARD --}}
        <div class="bg-white rounded-3xl shadow overflow-hidden">

            {{-- TOP --}}
            <div class="bg-gradient-to-r from-blue-500 to-blue-100 p-6 text-white flex items-center gap-4">
                {{-- Profile Image --}}
                <img id="previewImage" src="{{ auth()->user()->profileImage() }}"
                    class="w-20 h-20 rounded-full border-4 border-white object-cover">

                <div class="flex flex-col gap-2">
                    <p class="font-semibold">Profile Photo</p>

                    {{-- Change & Delete Horizontal --}}
                    <div class="flex gap-4 items-center">
                        {{-- Change Photo --}}
                        <label class="text-sm cursor-pointer underline">
                            Change photo
                            <input type="file" name="image" accept="image/*" class="hidden" form="updateProfileForm"
                                onchange="previewFile(event)">
                        </label>

                        {{-- Delete Photo --}}
                        @if (auth()->user()->image)
                            <form action="{{ route('member.profile.delete_image') }}" method="POST" class="inline-flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-500 hover:underline"
                                    onclick="return confirm('Are you sure you want to delete your profile photo?')">
                                    Delete photo
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- FORM UPDATE --}}
            <form id="updateProfileForm" action="{{ route('member.profile.update') }}" method="POST"
                enctype="multipart/form-data" class="p-6 space-y-6 bg-white">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-sm text-gray-600">Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                        class="w-full mt-1 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-white"
                        required>
                </div>

                <div>
                    <label class="text-sm text-gray-600">Email</label>
                    <input type="email" value="{{ auth()->user()->email }}"
                        class="w-full mt-1 rounded-xl border-gray-200 bg-gray-100" disabled>
                </div>

                <div>
                    <label class="text-sm text-gray-600">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                        class="w-full mt-1 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>

                {{-- ACTION --}}
                <div class="flex gap-3 pt-4">
                    <button type="submit"
                        class="px-6 py-2 rounded-xl bg-blue-500 text-white font-semibold hover:bg-blue-600 transition">
                        Save Changes
                    </button>

                    <a href="{{ route('member.profile') }}"
                        class="px-6 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition">
                        Cancel
                    </a>
                </div>
            </form>

        </div>
    </div>

    {{-- PREVIEW IMAGE --}}
    <script>
        function previewFile(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('previewImage').src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
