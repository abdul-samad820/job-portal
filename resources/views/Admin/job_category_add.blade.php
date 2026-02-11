@extends('layouts.index')

@section('title', 'Add Job Category')

@section('content')

    <div class="card p-4">

        <form action="{{ route('admin.job_category_create') }}" method="POST" enctype="multipart/form-data">

            @csrf

            {{-- Category Name --}}
            <fieldset class="form-group">
                <label class="font-weight-bold">Category Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Enter category name"
                    value="{{ old('name') }}" required>
                @error('name')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </fieldset>

            {{-- Category Description --}}
            <fieldset class="form-group">
                <label class="font-weight-bold">Category Description</label>
                <input type="text" name="description" class="form-control" placeholder="Enter category description"
                    value="{{ old('description') }}">
                @error('description')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </fieldset>

            {{-- Category Image --}}
            <fieldset class="form-group">
                <label class="font-weight-bold">Category Image</label>
                <input type="file" name="category_image" class="form-control-file" accept="image/*">
                <small class="text-muted d-block mt-1">
                    Upload JPG / PNG (Max size: 2MB)
                </small>

                @error('category_image')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
            </fieldset>

            {{-- Submit Button --}}
            <div class="text-right">
                <button type="submit" class="btn btn-primary px-4">
                    Save
                </button>
            </div>

        </form>

    </div>

@endsection
