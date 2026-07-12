@extends('layouts.superadmin')
@section('title', 'Broadcast Announcement')

@section('content')

<div class="container-fluid py-4">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-bullhorn',
        'title' => 'Broadcast Announcement',
        'subtitle' => 'Send a platform-wide notification to admins, users, or both.',
    ])

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('superadmin.broadcast.send') }}" method="POST" onsubmit="return confirm('Send this announcement now? This cannot be undone.');">
                @csrf

                <div class="form-group">
                    <label class="font-weight-bold">Title</label>
                    <input type="text" name="title" class="form-control" required maxlength="150"
                           placeholder="e.g. Scheduled maintenance on Sunday" value="{{ old('title') }}">
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Message</label>
                    <textarea name="message" rows="5" class="form-control" required maxlength="2000"
                              placeholder="Write the announcement details here...">{{ old('message') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Send To</label>
                    <select name="audience" class="form-control" required>
                        <option value="admins" {{ old('audience') == 'admins' ? 'selected' : '' }}>All Companies (Admins) only</option>
                        <option value="users" {{ old('audience') == 'users' ? 'selected' : '' }}>All Job Seekers (Users) only</option>
                        <option value="both" {{ old('audience') == 'both' ? 'selected' : '' }}>Everyone (Admins + Users)</option>
                    </select>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" name="send_email" value="1" class="form-check-input" id="sendEmail">
                    <label class="form-check-label" for="sendEmail">
                        Also send as email (not just in-app bell notification)
                    </label>
                </div>

                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa fa-paper-plane mr-1"></i> Send Announcement
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
