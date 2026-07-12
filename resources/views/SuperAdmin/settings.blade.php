@extends('layouts.superadmin')
@section('title', 'Site Settings')

@section('content')

    <div class="container-fluid mt-3">

        @include('partials.superadmin-page-header', [
            'icon' => 'fa-cogs',
            'title' => 'Site Settings',
            'subtitle' => 'Public contact info and social links shown on the homepage.',
        ])

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
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

                <form action="{{ route('superadmin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h5 class="font-weight-bold mb-3">Contact Info</h5>
                    <p class="text-muted small mb-3">
                        This is shown publicly on the homepage "Get In Touch" section for all visitors.
                    </p>

                    <div class="form-group">
                        <label>Contact Email</label>
                        <input type="email" name="contact_email" class="form-control"
                               value="{{ old('contact_email', $settings['contact_email']) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="contact_location" class="form-control"
                               value="{{ old('contact_location', $settings['contact_location']) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Working Hours</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            </div>
                            <input type="text" id="workingHoursInput" name="working_hours" class="form-control"
                                   placeholder="e.g. Mon – Sat, 9 AM – 6 PM"
                                   value="{{ old('working_hours', $settings['working_hours']) }}" required maxlength="100">
                        </div>

                        <div class="mt-2 d-flex flex-wrap" style="gap:8px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary wh-preset" data-value="Mon – Fri, 9 AM – 6 PM">Mon–Fri, 9–6</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary wh-preset" data-value="Mon – Sat, 9 AM – 6 PM">Mon–Sat, 9–6</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary wh-preset" data-value="24/7 Support">24/7 Support</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary wh-preset" data-value="By Appointment Only">By Appointment</button>
                        </div>

                        <div class="mt-2 p-2 pl-3 rounded" style="background:#f8f9fc; border:1px solid #eef0f5;">
                            <span class="text-muted small text-uppercase font-weight-bold" style="letter-spacing:.5px;">Homepage preview</span>
                            <div class="mt-1 d-flex align-items-center">
                                <i class="fas fa-clock text-primary mr-2"></i>
                                <span id="workingHoursPreview" class="font-weight-500">{{ $settings['working_hours'] }}</span>
                            </div>
                        </div>
                    </div>

                    <script>
                        (function () {
                            var input = document.getElementById('workingHoursInput');
                            var preview = document.getElementById('workingHoursPreview');
                            if (!input || !preview) return;

                            input.addEventListener('input', function () {
                                preview.textContent = input.value.trim() || 'Mon – Sat, 9 AM – 6 PM';
                            });

                            document.querySelectorAll('.wh-preset').forEach(function (btn) {
                                btn.addEventListener('click', function () {
                                    input.value = btn.getAttribute('data-value');
                                    input.dispatchEvent(new Event('input'));
                                    input.focus();
                                });
                            });
                        })();
                    </script>

                    <hr class="my-4">

                    <h5 class="font-weight-bold mb-3">Social Links</h5>
                    <p class="text-muted small mb-3">
                        Leave blank to hide an icon on the homepage instead of showing a dead link.
                    </p>

                    <div class="form-group">
                        <label>LinkedIn URL</label>
                        <input type="url" name="linkedin_url" class="form-control"
                               placeholder="https://linkedin.com/company/yourcompany"
                               value="{{ old('linkedin_url', $settings['linkedin_url']) }}">
                    </div>

                    <div class="form-group">
                        <label>Twitter / X URL</label>
                        <input type="url" name="twitter_url" class="form-control"
                               placeholder="https://twitter.com/yourhandle"
                               value="{{ old('twitter_url', $settings['twitter_url']) }}">
                    </div>

                    <div class="form-group">
                        <label>GitHub URL</label>
                        <input type="url" name="github_url" class="form-control"
                               placeholder="https://github.com/yourorg/job-portal"
                               value="{{ old('github_url', $settings['github_url']) }}">
                    </div>

                    <button type="submit" class="btn btn-primary mt-2">
                        <i class="fas fa-save mr-1"></i> Save Settings
                    </button>

                </form>

            </div>
        </div>

    </div>

@endsection