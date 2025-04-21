@extends('user.layout.layout')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg rounded-4 border-0 overflow-hidden">
                    <!-- Header with gradient background -->
                    <div class="card-header bg-gradient text-white text-center fs-4 fw-bold"
                        style="background: linear-gradient(135deg, #6a11cb, #2575fc);">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="bi bi-pencil-square me-2" style="font-size: 1.5rem;"></i> Edit Profile
                        </div>
                    </div>
                    <div class="card-body p-5 bg-light">
                        <!-- Form -->
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <!-- Profile Picture -->
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('assets/img/icon/user-profile.png') }}"
                                        alt="Profile Picture"
                                        class="rounded-circle shadow-lg border border-3 border-primary"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                    <label for="profile-picture"
                                        class="position-absolute bottom-0 end-0 bg-success text-white rounded-circle p-2 shadow"
                                        style="font-size: 0.8rem; cursor: pointer;">
                                        <i class="bi bi-camera-fill"></i>
                                    </label>
                                    <input type="file" id="profile-picture" name="profile_picture" class="d-none">
                                </div>
                            </div>
                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Name</label>
                                <input type="text" class="form-control rounded-pill shadow-sm" id="name"
                                    name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control rounded-pill shadow-sm" id="email"
                                    name="email" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <!-- Phone -->
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-bold">Phone</label>
                                <input type="text" class="form-control rounded-pill shadow-sm" id="phone"
                                    name="phone" value="{{ old('phone', $user->phone) }}">
                            </div>
                            <!-- Address -->
                            <div class="mb-3">
                                <label for="address" class="form-label fw-bold">Address</label>
                                <textarea class="form-control rounded-4 shadow-sm" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                            </div>
                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                                    <i class="bi bi-save me-2"></i>Save Changes
                                </button>
                                <a href="{{ route('profile.show') }}"
                                    class="btn btn-secondary rounded-pill px-4 py-2 shadow-sm">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
