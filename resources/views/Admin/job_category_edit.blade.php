@extends('layouts.index')

@section('title', 'Edit Job Category')

@section('content')

    <div class="card p-4">

        <form action="{{ route('admin.job_category_update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            {{-- Category Name --}}
            <div class="form-group">
                <label class="font-weight-bold">Category Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>

                @error('name')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Category Description --}}
            <div class="form-group">
                <label class="font-weight-bold">Category Description</label>
                <input type="text" name="description" class="form-control"
                    value="{{ old('description', $category->description) }}">

                @error('description')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Category Image Upload --}}
            <div class="form-group">
                <label class="font-weight-bold">Category Image</label>

                <input type="file" name="category_image" class="form-control-file" accept="image/*">

                <small class="text-muted d-block mt-1">Upload JPG / PNG (Max 2MB)</small>

                @error('category_image')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Existing Image Preview --}}
            @if ($category->category_image)
                <div class="mb-3">
                    <label class="font-weight-bold d-block">Current Image:</label>
                    <img src="{{ asset('uploads/categories/' . $category->category_image) }}" class="img-thumbnail"
                        style="max-width: 150px;">
                </div>
            @endif

            {{-- Submit Button --}}
            <div class="text-right">
                <button type="submit" class="btn btn-primary px-4">
                    Update
                </button>
            </div>

        </form>

    </div>

@endsection
