<div class="modal fade sa-modal" id="createAdminModal" tabindex="-1" role="dialog" aria-labelledby="createAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title font-weight-bold" id="createAdminModalLabel">Admin Registration</h5>
                    <small class="text-muted">Fill in the details to create a new admin account</small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('superadmin.create') }}">
                    @csrf

                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="modal_company_name">Company Name</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                </div>
                                <input type="text" id="modal_company_name" name="company_name" class="form-control" placeholder="Company Name" value="{{ old('company_name') }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="modal_contact_number">Contact Number</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input type="text" id="modal_contact_number" name="contact_number" class="form-control" placeholder="Contact Number" value="{{ old('contact_number') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modal_location">Location</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            </div>
                            <input type="text" id="modal_location" name="location" class="form-control" placeholder="Location" value="{{ old('location') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modal_description">Description</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                            </div>
                            <textarea id="modal_description" name="description" rows="2" class="form-control" placeholder="Short description">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="modal_email">Email</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="email" id="modal_email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6 mb-3">
                            <label for="modal_password">Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" id="modal_password" name="password" class="form-control" placeholder="Password">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="modal_password_confirmation">Confirm Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" id="modal_password_confirmation" name="password_confirmation" class="form-control" placeholder="Retype Password">
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary btn-block">Register</button>

                </form>

            </div>

        </div>
    </div>
</div>

@if ($errors->any())
    <script>
        // Validation failed on submit — the redirect brought us back here, so re-open the modal.
        document.addEventListener('DOMContentLoaded', function () {
            $('#createAdminModal').modal('show');
        });
    </script>
@endif
