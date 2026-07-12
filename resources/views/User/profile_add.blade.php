@extends('layouts.User_layout')
@section('title', 'Add Profile')

@section('content')
<div class="container py-4">

    <div class="sa-page-header">
        <div class="d-flex align-items-center">
            <div class="sa-page-icon mr-3"><i class="fas fa-id-card"></i></div>
            <div>
                <h1 class="sa-page-title font-weight-bold text-dark mb-0">Add Your Professional Profile</h1>
                <small class="text-muted">Fill in your summary, skills, experience and education to complete your profile.</small>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success shadow-sm border-0">{{ session('success') }}</div>
    @endif

    {{-- form --}}
    <form action="{{ route('user.update_profile') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- Profile Image --}}
        <div class="card border-0 shadow-sm rounded mb-4">
            <div class="card-header bg-white border-0 py-3">
                <strong><i class="fas fa-camera text-primary mr-2"></i>Profile Photo</strong>
            </div>
            <div class="card-body p-4 d-flex align-items-center flex-wrap">

                @php
                $userImg =
                $profile && $profile->profile_image
                ? Storage::url('user_profile/' . $profile->profile_image)
                : asset('admins/dist/img/default.png');
                @endphp

                {{-- Current Image Preview --}}
                <img id="profile-photo-preview" src="{{ $userImg }}"
                    class="rounded-circle shadow-sm mr-4 u-fit-cover border"
                    style="width: 90px; height: 90px; border-width: 3px !important; border-color: #eef1f6 !important;"
                    width="90" height="90" alt="Profile photo">

                {{-- Upload Field --}}
                <div class="flex-grow-1" style="min-width: 240px;">
                    <input type="file" name="profile_image" id="profile-photo-input"
                        class="form-control @error('profile_image') is-invalid @enderror"
                        accept="image/jpeg,image/png" aria-label="Upload profile photo">
                    @error('profile_image')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="text-muted d-block mt-1">Upload JPG or PNG (Max 2MB)</small>
                </div>

            </div>
        </div>

        {{-- Professional Summary --}}
        <div class="card border-0 shadow-sm rounded mb-4">
            <div class="card-header bg-white border-0 py-3">
                <strong><i class="fas fa-align-left text-primary mr-2"></i>Professional Summary</strong>
            </div>
            <div class="card-body p-4">
                <textarea name="professional_summary" aria-label="Professional Summary" rows="6"
                    class="form-control @error('professional_summary') is-invalid @enderror"
                    placeholder="Briefly describe your professional background, strengths and goals...">{{ old('professional_summary', $profile->professional_summary) }}</textarea>

                @error('professional_summary')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted mt-2">Keep it concise (3–6 lines). Employers will see this first.</small>
            </div>
        </div>

        {{-- Core / Concept Skills --}}
        <div class="card border-0 shadow-sm rounded mb-4">
            <div class="card-header bg-white border-0 py-3">
                <strong><i class="fas fa-tools text-primary mr-2"></i>Core Skills (comma separated)</strong>
            </div>
            <div class="card-body p-4">
                <input type="text" name="core_skills" aria-label="Core Skills" value="{{ old('core_skills', $profile->core_skills) }}"
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

        {{-- Experience --}}
        <div class="card border-0 shadow-sm rounded mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <strong><i class="fas fa-briefcase text-primary mr-2"></i>Experience</strong>
                <button type="button" id="add-experience-btn" class="btn btn-sm btn-primary rounded-pill px-3">
                    <i class="fas fa-plus mr-1"></i> Add Experience
                </button>
            </div>

            <div class="card-body p-4" id="experience-list">

                @php
                $experienceData = $profile->experience ?? [];
                @endphp

                @forelse ($experienceData as $index => $exp)
                <div class="experience-row border rounded p-3 mb-3 bg-light" data-index="{{ $index }}">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="small font-weight-bold">Company</label>
                            <input type="text" name="experience[{{ $index }}][company]"
                                value="{{ $exp['company'] ?? '' }}" class="form-control" placeholder="Company name">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="small font-weight-bold">Role</label>
                            <input type="text" name="experience[{{ $index }}][role]"
                                value="{{ $exp['role'] ?? '' }}" class="form-control" placeholder="e.g. Backend Developer">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="small font-weight-bold">Duration</label>
                            <input type="text" name="experience[{{ $index }}][duration]"
                                value="{{ $exp['duration'] ?? '' }}" class="form-control" placeholder="e.g. Jan 2025 - Present">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="small font-weight-bold">Description</label>
                            <textarea name="experience[{{ $index }}][description]" class="form-control" rows="2"
                                placeholder="What did you build or improve? Use action verbs and numbers where possible.">{{ $exp['description'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="text-right mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-experience-btn">
                            <i class="fas fa-trash-alt mr-1"></i>Remove
                        </button>
                    </div>
                </div>
                @empty
                <div class="experience-row border rounded p-3 mb-3 bg-light" data-index="0">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="small font-weight-bold">Company</label>
                            <input type="text" name="experience[0][company]" class="form-control" placeholder="Company name">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="small font-weight-bold">Role</label>
                            <input type="text" name="experience[0][role]" class="form-control" placeholder="e.g. Backend Developer">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="small font-weight-bold">Duration</label>
                            <input type="text" name="experience[0][duration]" class="form-control" placeholder="e.g. Jan 2025 - Present">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="small font-weight-bold">Description</label>
                            <textarea name="experience[0][description]" class="form-control" rows="2"
                                placeholder="What did you build or improve? Use action verbs and numbers where possible."></textarea>
                        </div>
                    </div>
                    <div class="text-right mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-experience-btn d-none">
                            <i class="fas fa-trash-alt mr-1"></i>Remove
                        </button>
                    </div>
                </div>
                @endforelse

            </div>
        </div>

        {{-- Projects (NEW) --}}
        <div class="card border-0 shadow-sm rounded mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <strong><i class="fas fa-project-diagram text-primary mr-2"></i>Projects</strong>
                <button type="button" id="add-project-btn" class="btn btn-sm btn-primary rounded-pill px-3">
                    <i class="fas fa-plus mr-1"></i> Add Project
                </button>
            </div>

            <div class="card-body p-4" id="project-list">

                @php
                $projectsData = $profile->projects ?? [];
                @endphp

                @forelse ($projectsData as $index => $proj)
                <div class="project-row border rounded p-3 mb-3 bg-light" data-index="{{ $index }}">
                    <div class="row">
                        <div class="col-md-8 mb-2">
                            <label class="small font-weight-bold">Project Title</label>
                            <input type="text" name="projects[{{ $index }}][title]"
                                value="{{ $proj['title'] ?? '' }}" class="form-control" placeholder="e.g. Job Portal Platform">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="small font-weight-bold">Tech Used</label>
                            <input type="text" name="projects[{{ $index }}][tech]"
                                value="{{ $proj['tech'] ?? '' }}" class="form-control" placeholder="e.g. Laravel, MySQL">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="small font-weight-bold">Project Link <span class="text-muted font-weight-normal">(optional)</span></label>
                            <input type="url" name="projects[{{ $index }}][link]"
                                value="{{ $proj['link'] ?? '' }}" class="form-control" placeholder="https://github.com/you/project">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="small font-weight-bold">Description</label>
                            <textarea name="projects[{{ $index }}][description]" class="form-control" rows="2"
                                placeholder="What does the project do, what problem does it solve, what was your role?">{{ $proj['description'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="text-right mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-project-btn">
                            <i class="fas fa-trash-alt mr-1"></i>Remove
                        </button>
                    </div>
                </div>
                @empty
                <p class="text-muted mb-0" id="no-projects-msg">
                    No projects added yet. Add at least one project — it strengthens your Resume/ATS score.
                </p>
                @endforelse

            </div>
        </div>

        {{-- Education --}}
        <div class="card border-0 shadow-sm rounded mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <strong><i class="fas fa-graduation-cap text-primary mr-2"></i>Education</strong>
                <button type="button" id="add-education-btn" class="btn btn-sm btn-primary rounded-pill px-3">
                    <i class="fas fa-plus mr-1"></i> Add Education
                </button>
            </div>

            <div class="card-body p-4" id="education-list">

                @php
                $educationData = $profile->education ?? [];
                @endphp

                @forelse ($educationData as $index => $edu)
                <div class="education-row border rounded p-3 mb-3 bg-light" data-index="{{ $index }}">
                    <div class="row">

                        <div class="col-md-5 mb-2">
                            <label class="small font-weight-bold" for="education_{{ $index }}_degree">Degree / Course</label>
                            <input id="education_{{ $index }}_degree" type="text" class="form-control" name="education[{{ $index }}][degree]"
                                value="{{ $edu['degree'] ?? '' }}" placeholder="e.g. B.Sc / B.Tech / MBA">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="small font-weight-bold" for="education_{{ $index }}_institute">Institute</label>
                            <input id="education_{{ $index }}_institute" type="text" class="form-control" name="education[{{ $index }}][institute]"
                                value="{{ $edu['institute'] ?? '' }}" placeholder="Institute name">
                        </div>

                        <div class="col-md-3 mb-2">
                            <label class="small font-weight-bold" for="education_{{ $index }}_year">Year</label>
                            <input id="education_{{ $index }}_year" type="text" class="form-control" name="education[{{ $index }}][year]"
                                value="{{ $edu['year'] ?? '' }}" placeholder="e.g., 2020">
                        </div>

                    </div>

                    <div class="text-right mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-education-btn">
                            <i class="fas fa-trash-alt mr-1"></i>Remove
                        </button>
                    </div>
                </div>
                @empty
                {{-- First empty row --}}
                <div class="education-row border rounded p-3 mb-3 bg-light" data-index="0">
                    <div class="row">

                        <div class="col-md-5 mb-2">
                            <label class="small font-weight-bold" for="education_0__degree_">Degree / Course</label>
                            <input id="education_0__degree_" type="text" name="education[0][degree]" class="form-control"
                                placeholder="e.g. B.Sc / B.Tech / MBA">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="small font-weight-bold" for="education_0__institute_">Institute</label>
                            <input id="education_0__institute_" type="text" name="education[0][institute]" class="form-control"
                                placeholder="Institute name">
                        </div>

                        <div class="col-md-3 mb-2">
                            <label class="small font-weight-bold" for="education_0__year_">Year</label>
                            <input id="education_0__year_" type="text" name="education[0][year]" class="form-control"
                                placeholder="e.g., 2020">
                        </div>

                    </div>

                    <div class="text-right mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-education-btn d-none">
                            <i class="fas fa-trash-alt mr-1"></i>Remove
                        </button>
                    </div>
                </div>
                @endforelse

            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill px-4 mr-2">Cancel</a>
            <button type="submit" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-save mr-1"></i> Save Profile
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Live preview when a new profile photo is chosen
    const photoInput = document.getElementById('profile-photo-input');
    const photoPreview = document.getElementById('profile-photo-preview');
    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                photoPreview.src = URL.createObjectURL(this.files[0]);
            }
        });
    }

    /**
     * Generic "repeatable rows" helper — used for Experience, Projects
     * and Education. Each instance gets its own add button, list
     * container, row class and an HTML template for a fresh row.
     */
    function initRepeatable({ addBtnId, listId, rowClass, removeBtnClass, startIndex, buildRowHtml, emptyMsgId }) {
        const addBtn = document.getElementById(addBtnId);
        const list = document.getElementById(listId);
        if (!addBtn || !list) return;

        let index = startIndex;

        function updateRemoveButtons() {
            const rows = list.querySelectorAll('.' + rowClass);
            rows.forEach((row) => {
                const btn = row.querySelector('.' + removeBtnClass);
                if (!btn) return;
                btn.classList.remove('d-none');
                btn.onclick = function () {
                    row.remove();
                    checkOnlyOneRow();
                };
            });
            checkOnlyOneRow();
        }

        function checkOnlyOneRow() {
            const rows = list.querySelectorAll('.' + rowClass);
            if (rows.length === 1) {
                const btn = rows[0].querySelector('.' + removeBtnClass);
                if (btn) btn.classList.add('d-none');
            }
        }

        addBtn.addEventListener('click', function () {
            const emptyMsg = emptyMsgId ? document.getElementById(emptyMsgId) : null;
            if (emptyMsg) emptyMsg.remove();

            const wrapper = document.createElement('div');
            wrapper.innerHTML = buildRowHtml(index).trim();
            list.appendChild(wrapper.firstChild);

            index++;
            updateRemoveButtons();
        });

        updateRemoveButtons();
    }

    // ---- Experience ----
    initRepeatable({
        addBtnId: 'add-experience-btn',
        listId: 'experience-list',
        rowClass: 'experience-row',
        removeBtnClass: 'remove-experience-btn',
        startIndex: {{ !empty($experienceData) ? count($experienceData) : 1 }},
        buildRowHtml: (i) => `
            <div class="experience-row border rounded p-3 mb-3 bg-light" data-index="${i}">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="small font-weight-bold">Company</label>
                        <input type="text" name="experience[${i}][company]" class="form-control" placeholder="Company name">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="small font-weight-bold">Role</label>
                        <input type="text" name="experience[${i}][role]" class="form-control" placeholder="e.g. Backend Developer">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="small font-weight-bold">Duration</label>
                        <input type="text" name="experience[${i}][duration]" class="form-control" placeholder="e.g. Jan 2025 - Present">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="small font-weight-bold">Description</label>
                        <textarea name="experience[${i}][description]" class="form-control" rows="2"
                            placeholder="What did you build or improve? Use action verbs and numbers where possible."></textarea>
                    </div>
                </div>
                <div class="text-right mt-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-experience-btn">
                        <i class="fas fa-trash-alt mr-1"></i>Remove
                    </button>
                </div>
            </div>`
    });

    // ---- Projects ----
    initRepeatable({
        addBtnId: 'add-project-btn',
        listId: 'project-list',
        rowClass: 'project-row',
        removeBtnClass: 'remove-project-btn',
        startIndex: {{ !empty($projectsData) ? count($projectsData) : 0 }},
        emptyMsgId: 'no-projects-msg',
        buildRowHtml: (i) => `
            <div class="project-row border rounded p-3 mb-3 bg-light" data-index="${i}">
                <div class="row">
                    <div class="col-md-8 mb-2">
                        <label class="small font-weight-bold">Project Title</label>
                        <input type="text" name="projects[${i}][title]" class="form-control" placeholder="e.g. Job Portal Platform">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">Tech Used</label>
                        <input type="text" name="projects[${i}][tech]" class="form-control" placeholder="e.g. Laravel, MySQL">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="small font-weight-bold">Project Link <span class="text-muted font-weight-normal">(optional)</span></label>
                        <input type="url" name="projects[${i}][link]" class="form-control" placeholder="https://github.com/you/project">
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="small font-weight-bold">Description</label>
                        <textarea name="projects[${i}][description]" class="form-control" rows="2"
                            placeholder="What does the project do, what problem does it solve, what was your role?"></textarea>
                    </div>
                </div>
                <div class="text-right mt-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-project-btn">
                        <i class="fas fa-trash-alt mr-1"></i>Remove
                    </button>
                </div>
            </div>`
    });

    // ---- Education ----
    initRepeatable({
        addBtnId: 'add-education-btn',
        listId: 'education-list',
        rowClass: 'education-row',
        removeBtnClass: 'remove-education-btn',
        startIndex: {{ !empty($educationData) ? count($educationData) : 1 }},
        buildRowHtml: (i) => `
            <div class="education-row border rounded p-3 mb-3 bg-light" data-index="${i}">
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <label class="small font-weight-bold" for="education_${i}_degree">Degree / Course</label>
                        <input id="education_${i}_degree" type="text" class="form-control"
                               name="education[${i}][degree]"
                               placeholder="e.g. B.Sc / B.Tech / MBA">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold" for="education_${i}_institute">Institute</label>
                        <input id="education_${i}_institute" type="text" class="form-control"
                               name="education[${i}][institute]"
                               placeholder="Institute name">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold" for="education_${i}_year">Year</label>
                        <input id="education_${i}_year" type="text" class="form-control"
                               name="education[${i}][year]"
                               placeholder="e.g., 2020">
                    </div>
                </div>
                <div class="text-right mt-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-education-btn">
                        <i class="fas fa-trash-alt mr-1"></i>Remove
                    </button>
                </div>
            </div>`
    });

});
</script>
@endpush