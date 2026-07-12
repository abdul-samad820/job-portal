@extends('layouts.User_layout')
@section('title', 'Salary Insights')

@section('content')

<div class="container py-4">

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fas fa-chart-line"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Salary Insights</h1>
                <small class="text-muted">Real pay ranges pulled from currently live job postings on this platform — pick a role to see what
            it's paying right now.</small>
            </div>
        </div>
    </div>

    @if (session('error'))
    <div class="alert alert-danger shadow-sm border-0">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded mb-4">
        <div class="card-body p-4">
            <form action="{{ route('user.salary_insights') }}" method="GET" class="form-row align-items-end">
                <div class="col-md-5 mb-2">
                    <label class="font-weight-semibold" for="role_id">Job Role</label>
                    <select name="role_id" id="role_id" class="form-control" required>
                        <option value="">-- Select a role --</option>
                        @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ $selectedRoleId == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="font-weight-semibold" for="location">Location (optional)</label>
                    <input type="text" name="location" id="location" class="form-control"
                        placeholder="e.g. Bangalore" value="{{ $selectedLocation }}">
                </div>
                <div class="col-md-3 mb-2">
                    <button type="submit" class="btn btn-primary btn-block rounded-pill">
                        <i class="fas fa-search mr-1"></i> Check Salary
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($selectedRoleId)
        @if ($stats)
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm rounded text-center h-100">
                    <div class="card-body p-4">
                        <h4 class="font-weight-bold text-primary mb-0">{{ $stats['job_count'] }}</h4>
                        <small class="text-muted">Live jobs analyzed</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm rounded text-center h-100">
                    <div class="card-body p-4">
                        <h4 class="font-weight-bold text-success mb-0">₹{{ number_format($stats['avg_min']) }}</h4>
                        <small class="text-muted">Avg. minimum pay (LPA)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm rounded text-center h-100">
                    <div class="card-body p-4">
                        <h4 class="font-weight-bold text-success mb-0">₹{{ number_format($stats['avg_max']) }}</h4>
                        <small class="text-muted">Avg. maximum pay (LPA)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm rounded text-center h-100">
                    <div class="card-body p-4">
                        <h6 class="font-weight-bold text-dark mb-0">
                            ₹{{ number_format($stats['overall_min']) }} – ₹{{ number_format($stats['overall_max']) }}
                        </h6>
                        <small class="text-muted">Overall range seen</small>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-info shadow-sm border-0">
            Not enough live job postings with salary data for this role{{ $selectedLocation ? ' in ' . $selectedLocation : '' }} yet.
        </div>
        @endif
    @endif

</div>

@endsection
