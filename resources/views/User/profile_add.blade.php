@extends('layouts.user_index') {{-- change layout if needed --}}
@section('title', 'Add Profile')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="card shadow-sm mb-4">
                    <div class="card-body d-flex gap-3 align-items-center">

                        <div>
                            <h4 class="mb-1">Add Your Professional Profile</h4>
                            <p class="mb-0 text-muted small">Fill in your summary, skills and education to complete your
                                profile.</p>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- form --}}
                <form action="{{ route('user.update_profile') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    {{-- Professional Summary --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Professional Summary</strong>
                        </div>
                        <div class="card-body">
                            <textarea name="professional_summary" rows="6"
                                class="form-control @error('professional_summary') is-invalid @enderror"
                                placeholder="Briefly describe your professional background, strengths and goals..."> {{ old('professional_summary', $profile->professional_summary) }}</textarea>


                            @error('professional_summary')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted mt-2">Keep it concise (3–6 lines). Employer will see this
                                first.</small>
                        </div>
                    </div>

                    {{-- Profile Image --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Profile Photo</strong>
                        </div>

                        <div class="card-body d-flex align-items-center gap-3">

                            {{-- Current Image Preview --}}
                            @php
                                $userImg =
                                    $profile && $profile->profile_image
                                        ? asset('uploads/user_profile/' . $profile->profile_image)
                                        : asset('admins/dist/img/default.png');
                            @endphp

                            <img src="{{ $userImg }}" class="rounded-circle shadow-sm" width="80" height="80"
                                style="object-fit:cover;">

                            {{-- Upload Field --}}
                            <div class="flex-grow-1">
                                <input type="file" name="profile_image" class="form-control">

                                <small class="text-muted">Upload JPG or PNG (Max 2MB)</small>
                            </div>

                        </div>
                    </div>


                    {{-- Core / Concept Skills --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Core Skills (comma separated)</strong>
                        </div>
                        <div class="card-body">
                            <input type="text" name="core_skills" value="{{ old('core_skills', $profile->core_skills) }}"
                                class="form-control @error('core_skills') is-invalid @enderror"
                                placeholder="e.g. PHP, Laravel, SQL, REST APIs">
                            @error('core_skills')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted mt-2">
                                Enter main skills separated by commas. Example: <code>PHP, Laravel, MySQL</code>
                            </small>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Experience</strong>
                        </div>

                        <div class="card-body">

                            <label class="form-label small mb-2">
                                Write your experience line by line:
                            </label>

                            <textarea name="experience" rows="5" class="form-control @error('experience') is-invalid @enderror"
                                placeholder="Example:
2 Years Experience
Worked at XYZ Company as a Laravel Developer
Handled Backend APIs
Managed Admin Dashboard">{{ old('experience', $profile->experience) }}</textarea>

                            @error('experience')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <small class="text-muted mt-2 d-block">
                                Line 1 → Total experience
                                Line 2+ → Your work details (companies, roles, duties)
                            </small>

                        </div>
                    </div>


                    <div class="card mb-3 rounded shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 font-weight-bold">Education</h6>
        <button type="button" id="add-education-btn" class="btn btn-sm btn-primary">
            + Add Education
        </button>
    </div>

    <div class="card-body" id="education-list">

        @php
            $educationData = $profile->education ? json_decode($profile->education, true) : [];
        @endphp

        @forelse ($educationData as $index => $edu)
            <div class="education-row border rounded p-3 mb-3 bg-light" data-index="{{ $index }}">
                <div class="row">

                    <div class="col-md-5 mb-2">
                        <label class="small font-weight-bold">Degree / Course</label>
                        <input type="text" class="form-control"
                               name="education[{{ $index }}][degree]"
                               value="{{ $edu['degree'] ?? '' }}"
                               placeholder="e.g. B.Sc / B.Tech / MBA">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">Institute</label>
                        <input type="text" class="form-control"
                               name="education[{{ $index }}][institute]"
                               value="{{ $edu['institute'] ?? '' }}"
                               placeholder="Institute name">
                    </div>

                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold">Year</label>
                        <input type="text" class="form-control"
                               name="education[{{ $index }}][year]"
                               value="{{ $edu['year'] ?? '' }}"
                               placeholder="e.g., 2020">
                    </div>

                </div>

                <div class="text-right mt-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-education-btn">
                        Remove
                    </button>
                </div>
            </div>
        @empty
            {{-- First empty row --}}
            <div class="education-row border rounded p-3 mb-3 bg-light" data-index="0">
                <div class="row">

                    <div class="col-md-5 mb-2">
                        <label class="small font-weight-bold">Degree / Course</label>
                        <input type="text" name="education[0][degree]" class="form-control"
                               placeholder="e.g. B.Sc / B.Tech / MBA">
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">Institute</label>
                        <input type="text" name="education[0][institute]" class="form-control"
                               placeholder="Institute name">
                    </div>

                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold">Year</label>
                        <input type="text" name="education[0][year]" class="form-control"
                               placeholder="e.g., 2020">
                    </div>

                </div>

                <div class="text-right mt-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-education-btn d-none">
                        Remove
                    </button>
                </div>
            </div>
        @endforelse

    </div>
</div>


                    {{-- Submit --}}
                    <div class="d-flex justify-content-end gap-4">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Profile</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

   @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    let eduIndex = {{ !empty($educationData) ? count($educationData) : 1 }};

    const addBtn = document.getElementById('add-education-btn');
    const list = document.getElementById('education-list');

    addBtn.addEventListener('click', function() {

        const row = document.createElement('div');
        row.className = 'education-row border rounded p-3 mb-3 bg-light';
        row.dataset.index = eduIndex;

        row.innerHTML = `
            <div class="row">
                <div class="col-md-5 mb-2">
                    <label class="small font-weight-bold">Degree / Course</label>
                    <input type="text" class="form-control" 
                           name="education[${eduIndex}][degree]" 
                           placeholder="e.g. B.Sc / B.Tech / MBA">
                </div>

                <div class="col-md-4 mb-2">
                    <label class="small font-weight-bold">Institute</label>
                    <input type="text" class="form-control"
                           name="education[${eduIndex}][institute]"
                           placeholder="Institute name">
                </div>

                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold">Year</label>
                    <input type="text" class="form-control"
                           name="education[${eduIndex}][year]"
                           placeholder="e.g., 2020">
                </div>
            </div>

            <div class="text-right mt-2">
                <button type="button" class="btn btn-sm btn-outline-danger remove-education-btn">
                    Remove
                </button>
            </div>
        `;

        list.appendChild(row);
        eduIndex++;
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        let removeButtons = document.querySelectorAll('.remove-education-btn');
        removeButtons.forEach(btn => {
            btn.classList.remove('d-none');
            btn.onclick = function() {
                this.closest('.education-row').remove();
                checkOnlyOneRow();
            };
        });

        checkOnlyOneRow();
    }

    function checkOnlyOneRow() {
        let rows = document.querySelectorAll('.education-row');
        if (rows.length === 1) {
            rows[0].querySelector('.remove-education-btn').classList.add('d-none');
        }
    }

    updateRemoveButtons();
});
</script>
@endpush


@endsection
