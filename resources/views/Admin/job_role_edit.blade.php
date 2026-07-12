@extends('layouts.Admin_layout')
@section('title', 'Edit Job Role')
@section('content')
<div class="card p-4">
    <form action="{{ route('admin.job_role_update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Role Name --}}
        <fieldset class="mb-3">
            <label class="font-weight-bold" for="name">Role Name <span class="text-danger">*</span></label>
            <input id="name" type="text" name="name" class="form-control" placeholder="Enter role name"
                value="{{ old('name', $role->name) }}" required>
            @error('name')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </fieldset>

        {{-- Category Select --}}
        <fieldset class="mb-3">
            <label class="font-weight-bold" for="category_id">Select Category <span class="text-danger">*</span></label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">-- Select Category --</option>

                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $role->category_id) == $category->id ?
                    'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>

            @error('category_id')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </fieldset>

        {{-- Role Description --}}
        <fieldset class="mb-3">
            <label class="font-weight-bold" for="description">Role Description</label>
            <input id="description" type="text" name="description" class="form-control" placeholder="Enter role description"
                value="{{ old('description', $role->description) }}">
            @error('description')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </fieldset>

        {{-- Submit Button --}}
        <div class="text-right">
            <button class="btn btn-primary">
                <i class="fa fa-save mr-1"></i> Update
            </button>
        </div>

    </form>
</div>
@endsection