@extends('layouts.index')

@section('title', 'Add Job Role')

@section('content')
    <div class="card p-4">
        <form action="{{ route('admin.job_role_create') }}" method="POST">
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

            {{-- Role Name --}}
            <fieldset class="mb-3">
                <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="name" placeholder="Enter role name"
                    value="{{ old('name') }}" required>
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </fieldset>

            {{-- Category Select --}}
          <fieldset class="mb-3">
    <label class="font-weight-bold">Select Category <span class="text-danger">*</span></label>

    <select name="category_id" class="form-control" required>
        <option value="">-- Select Category --</option>

        @foreach ($categories as $category)
            <option value="{{ $category->id }}"
                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    @error('category_id')
        <small class="text-danger d-block mt-1">
            <i class="fa fa-exclamation-circle mr-1"></i> {{ $message }}
        </small>
    @enderror
</fieldset>


            {{-- Role Description --}}
            <fieldset class="mb-3">
                <label class="form-label fw-semibold">Role Description</label>
                <textarea class="form-control" name="description" placeholder="Enter role description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </fieldset>

            {{-- Submit Button --}}
            <div class="text-end">
                <button class="btn btn-primary" type="submit">Save Role</button>
            </div>
        </form>
    </div>
@endsection
