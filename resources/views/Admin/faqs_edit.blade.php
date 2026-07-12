@extends('layouts.Admin_layout')
@section('title', 'Edit FAQ')
@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="p-4 rounded shadow-sm mb-4 bg-light border-left border-primary u-bw-4px">

        <div class="d-md-flex justify-content-between align-items-center">

            <div>
                <h4 class="font-weight-bold text-dark mb-1">
                    <i class="fa fa-edit text-primary mr-2"></i>
                    Edit FAQ
                </h4>
                <small class="text-muted">
                    Update the selected FAQ details.
                </small>
            </div>

            <nav>
                <ol class="breadcrumb mb-0 bg-white shadow-sm px-3 py-2 rounded">
                    <li class="breadcrumb-item">
                        <a href="{{route('admin.dashboard')}}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active font-weight-bold">
                        Edit
                    </li>
                </ol>
            </nav>

        </div>

    </div>

    {{-- FORM --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="{{ route('faqs_update', $faq->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Question --}}
                <div class="form-group">
                    <label class="font-weight-bold" for="question">
                        Question <span class="text-danger">*</span>
                    </label>

                    <input id="question" type="text" name="question" class="form-control"
                        value="{{ old('question', $faq->question) }}" required>

                    @error('question')
                    <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Answer --}}
                <div class="form-group">
                    <label class="font-weight-bold" for="answer">
                        Answer <span class="text-danger">*</span>
                    </label>

                    <textarea id="answer" name="answer" rows="5" class="form-control"
                        required>{{ old('answer', $faq->answer) }}</textarea>

                    @error('answer')
                    <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status (optional but recommended) --}}
                <div class="form-group">
                    <label class="font-weight-bold" for="status">Status</label>

                    <select id="status" name="status" class="form-control">
                        <option value="1" {{ $faq->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $faq->status == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Submit --}}
                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa fa-save mr-1"></i> Update FAQ
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection