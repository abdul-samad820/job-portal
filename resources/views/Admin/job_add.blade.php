@extends('layouts.index')

@section('title', 'Add Job')

@section('content')
    <div class="card p-4">
        <form action="{{ route('admin.job_create') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Show Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- Job Title --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" placeholder="Enter job title"
                    value="{{ old('title') }}">
                @error('title')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>


            {{-- Job Description --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Description <span class="text-danger">*</span></label>
                <textarea class="form-control" name="description" rows="3" placeholder="Enter job description">{{ old('description') }}</textarea>
                @error('description')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Job overview --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Overview <span class="text-danger">*</span></label>
                <textarea class="form-control" name="overview" rows="3" placeholder="Enter job overview">{{ old('overview') }}</textarea>
                @error('overview')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            {{-- Job Responsibilities --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Responsibilities <span class="text-danger">*</span></label>
                <textarea class="form-control" name="responsibilities" rows="3" placeholder="Enter job responsibilities">{{ old('responsibilities') }}</textarea>
                @error('responsibilities')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            {{-- Job required_skills --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Required_Skills <span class="text-danger">*</span></label>
                <textarea class="form-control" name="required_skills" rows="3" placeholder="Enter job required_skills">{{ old('required_skills') }}</textarea>
                @error('required_skills')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>


            {{-- Location --}}
            <div class="form-group">
                <label class="font-weight-bold">Location <span class="text-danger">*</span></label>
                <input type="text" name="location" class="form-control" placeholder="Enter job location"
                    value="{{ old('location') }}">
                @error('location')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>


            {{-- Salary --}}
            <div class="form-group">
                <label class="font-weight-bold">Salary (Optional)</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">₹</span>
                    </div>
                    <input type="text" name="salary" class="form-control" placeholder="Enter salary"
                        value="{{ old('salary', $job->salary ?? '') }}">
                </div>
                @error('salary')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>


            {{-- Job Type --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Type <span class="text-danger">*</span></label>
                <select name="type" class="custom-select">
                    <option value="">-- Select Type --</option>
                    <option value="Full-time" {{ old('type') == 'Full-time' ? 'selected' : '' }}>Full-time</option>
                    <option value="Part-time" {{ old('type') == 'Part-time' ? 'selected' : '' }}>Part-time</option>
                    <option value="Internship" {{ old('type') == 'Internship' ? 'selected' : '' }}>Internship</option>
                    <option value="Contract" {{ old('type') == 'Contract' ? 'selected' : '' }}>Contract</option>
                </select>
                @error('type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- job Experience --}}
            <div class="form-group">
                <label class="font-weight-bold">Job Experience <span class="text-danger">*</span></label>
                <select name="experience" class="custom-select">
                    <option value="">-- Select experience --</option>
                    <option value="Fresher" {{ old('experience') == 'Fresher' ? 'selected' : '' }}>Fresher</option>
                    <option value="1 Year" {{ old('experience') == '1 Year' ? 'selected' : '' }}>1 Year</option>
                    <option value="2 Years" {{ old('experience') == '2 Years' ? 'selected' : '' }}>2 Years</option>
                    <option value="3 Years" {{ old('experience') == '3 Years' ? 'selected' : '' }}>3 Years</option>
                    <option value="3+ Years" {{ old('experience') == '3+ Years' ? 'selected' : '' }}>3+ Years</option>
                </select>

                @error('experience')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>



            {{-- Category --}}
            <div class="form-group">
                <label class="font-weight-bold">Select Category <span class="text-danger">*</span></label>
                <select name="category_id" class="custom-select">
                    <option value="">-- Select Category --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>


            {{-- Role --}}
            <div class="form-group">
                <label class="font-weight-bold">Select Role <span class="text-danger">*</span></label>
                <select name="role_id" class="custom-select">
                    <option value="">-- Select Role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>


            {{-- Last Date --}}
            <div class="form-group">
                <label class="font-weight-bold">Application Last Date</label>
                <input type="date" name="last_date" class="form-control" value="{{ old('last_date') }}">
                @error('last_date')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- job Image --}}
            <fieldset class="form-group">
                <label class="font-weight-bold">Job Image</label>
                <input type="file" name="job_image" class="form-control-file" accept="image/*">
                <small class="text-muted d-block mt-1">
                    Upload JPG / PNG (Max size: 2MB)
                </small>

                @error('job_image')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </fieldset>

            {{-- Submit --}}
            <div class="text-right">
                <button class="btn btn-primary">Save Job</button>
            </div>

        </form>
    </div>
@endsection
