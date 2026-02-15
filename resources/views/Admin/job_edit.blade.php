@extends('layouts.index')

@section('title', 'Edit Job')

@section('content')
    <div class="card p-4">
        <form action="{{ route('admin.job_update', $job->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Job Title --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Title <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="title" value="{{ $job->title }}"
                    placeholder="Enter job title" required>
                @error('title')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            {{-- Job Description --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Description</label>
                <textarea class="form-control" name="description" rows="3" placeholder="Enter job description">{{ old('description', $job->description) }}</textarea>
                @error('description')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>
            {{-- Job overview --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Overview</label>
                <textarea class="form-control" name="overview" rows="3" placeholder="Enter job overview">{{ old('overview', $job->overview) }}</textarea>
                @error('overview')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>
            {{-- Job responsibilities --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Responsibilities</label>
                <textarea class="form-control" name="responsibilities" rows="3" placeholder="Enter job responsibilities">{{ old('responsibilities', $job->responsibilities) }}</textarea>
                @error('responsibilities')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>
            {{-- Job required_skills --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Required_Skills</label>
                <textarea class="form-control" name="required_skills" rows="3" placeholder="Enter job required_skills">{{ old('required_skills', $job->required_skills) }}</textarea>
                @error('required_skills')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            {{-- Job Location --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Location</label>
                <input class="form-control" type="text" name="location" value="{{ old('location', $job->location) }}"
                    placeholder="Enter job location">
                @error('location')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            {{-- Salary --}}
            <div class="form-group">
                <label class="font-weight-bold">Salary</label>
                <input class="form-control" type="text" name="salary" value="{{ old('salary', $job->salary) }}"
                    placeholder="Enter salary">
                @error('salary')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            {{-- Job Type --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Type <span class="text-danger">*</span></label>
                <select name="type" class="custom-select" required>
                    <option value="">-- Select Job Type --</option>

                    <option value="Full-time" {{ old('type', $job->type) == 'Full-time' ? 'selected' : '' }}>
                        Full-time
                    </option>

                    <option value="Part-time" {{ old('type', $job->type) == 'Part-time' ? 'selected' : '' }}>
                        Part-time
                    </option>

                    <option value="Internship" {{ old('type', $job->type) == 'Internship' ? 'selected' : '' }}>
                        Internship
                    </option>

                    <option value="Contract" {{ old('type', $job->type) == 'Contract' ? 'selected' : '' }}>
                        Contract
                    </option>

                </select>

                @error('type')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            {{-- job experience  --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Experience <span class="text-danger">*</span></label>
                <select name="experience" class="custom-select" required>
                    <option value="">-- Select Experience --</option>

                    <option value="Fresher" {{ old('experience', $job->experience) == 'Fresher' ? 'selected' : '' }}>
                        Fresher
                    </option>

                    <option value="1 Year" {{ old('experience', $job->experience) == '1 Year' ? 'selected' : '' }}>
                        1 Year
                    </option>

                    <option value="2 Years" {{ old('experience', $job->experience) == '2 Years' ? 'selected' : '' }}>
                        2 Years
                    </option>

                    <option value="3 Years" {{ old('experience', $job->experience) == '3 Years' ? 'selected' : '' }}>
                        3 Years
                    </option>

                    <option value="3+ Years" {{ old('experience', $job->experience) == '3+ Years' ? 'selected' : '' }}>
                        3+ Years
                    </option>
                </select>

                @error('experience')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            {{-- Category Select --}}
            <div class="form-group">
                <label class="font-weight-bold">Select Category <span class="text-danger">*</span></label>
                <select name="category_id" class="custom-select" required>
                    <option value="">-- Select Category --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('category_id', $job->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            {{-- Role Select --}}
            <div class="form-group">
                <label class="font-weight-bold">Select Role <span class="text-danger">*</span></label>
                <select name="role_id" class="custom-select" required>
                    <option value="">-- Select Role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ old('role_id', $job->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            {{-- Last Date --}}
            <div class="form-group">
                <label class="font-weight-bold">Last Date to Apply</label>
                <input class="form-control" type="date" name="last_date"
                    value="{{ old('last_date', $job->last_date) }}">
                @error('last_date')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            {{-- job Image Upload --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Image</label>

                <input type="file" name="job_image" class="form-control-file" accept="image/*">

                <small class="text-muted d-block mt-1">Upload JPG / PNG (Max 2MB)</small>

                @error('job_image')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Existing Image Preview --}}
            @if ($job->job_image)
                <div class="mb-3">
                    <label class="font-weight-bold d-block">Current Image:</label>
                    <img src="{{ asset('uploads/job/' . $job->job_image) }}" class="img-thumbnail"
                        style="max-width: 150px;">
                </div>
            @endif

            {{-- Submit Button --}}
            <div class="text-right">
                <button class="btn btn-primary" type="submit">
                    <i class="fa fa-save mr-1"></i> Update Job
                </button>
            </div>
        </form>
    </div>
@endsection
