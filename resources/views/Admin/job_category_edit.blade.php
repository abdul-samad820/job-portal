@extends('layouts.Admin_layout')
@section('title', 'Edit Job Category')

@section('content')

<div class="card p-4">

    <form action="{{ route('admin.job_category_update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        {{-- Category Name --}}
        <div class="form-group">
            <label class="font-weight-bold" for="name">Category Name <span class="text-danger">*</span></label>
            <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $category->name) }}" required>

            @error('name')
            <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Category Description --}}
        <div class="form-group">
            <label class="font-weight-bold" for="description">Category Description</label>
            <input id="description" type="text" name="description" class="form-control"
                value="{{ old('description', $category->description) }}">

            @error('description')
            <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Category Image Upload --}}
        <div class="form-group">
            <label class="font-weight-bold" for="category_image">Category Image</label>

            <input id="category_image" type="file" name="category_image" class="form-control-file" accept="image/*">

            <small class="text-muted d-block mt-1">Upload JPG / PNG (Max 2MB)</small>

            @error('category_image')
            <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Existing Image Preview --}}
        @if ($category->category_image)
        <div class="mb-3">
            <label class="font-weight-bold d-block">Current Image:</label>
            <img src="{{ Storage::url($category->category_image) }}" class="img-thumbnail" style="max-width: 150px;" alt="{{ $category->name ?? 'Category' }}">
        </div>
        @endif

        {{-- Submit Button --}}
        <div class="text-right">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fa fa-save mr-1"></i> Update
            </button>
        </div>
    </form>
</div>

@endsection