@extends('layouts.landing_page')

@section('content')
<section class="py-5" style="margin-top: 90px;">
    <div class="container" style="max-width: 860px;">

        <h1 class="font-weight-bold mb-2">Terms & Conditions</h1>
        <p class="text-muted mb-5">Last updated: {{ date('F Y') }}</p>

        <div class="mb-4">
            <h5 class="font-weight-bold mb-2">1. Acceptance of Terms</h5>
            <p class="text-muted">
                By creating an account or using JobHub, you agree to be bound by these Terms &
                Conditions. If you do not agree with any part of these terms, please do not use the
                platform.
            </p>
        </div>

        <div class="mb-4">
            <h5 class="font-weight-bold mb-2">2. Account Responsibilities</h5>
            <p class="text-muted">
                You are responsible for maintaining the confidentiality of your account credentials and
                for all activity that occurs under your account. Please notify us immediately of any
                unauthorized use.
            </p>
        </div>

        <div class="mb-4">
            <h5 class="font-weight-bold mb-2">3. Accuracy of Information</h5>
            <p class="text-muted">
                Job seekers and employers agree to provide accurate and truthful information on their
                profiles, resumes, and job postings. JobHub reserves the right to suspend or remove
                accounts found to contain misleading or fraudulent content.
            </p>
        </div>

        <div class="mb-4">
            <h5 class="font-weight-bold mb-2">4. Job Postings & Applications</h5>
            <p class="text-muted">
                Employers are solely responsible for the content of their job listings. JobHub does not
                guarantee employment outcomes and is not a party to any agreement between a job seeker
                and an employer.
            </p>
        </div>

        <div class="mb-4">
            <h5 class="font-weight-bold mb-2">5. Prohibited Conduct</h5>
            <p class="text-muted">
                Users may not post spam, discriminatory content, or misuse the platform's reporting,
                messaging, or contact features. Violations may result in suspension or termination of
                access.
            </p>
        </div>

        <div class="mb-4">
            <h5 class="font-weight-bold mb-2">6. Limitation of Liability</h5>
            <p class="text-muted">
                JobHub is provided on an "as is" basis. We are not liable for any indirect, incidental,
                or consequential damages arising from your use of the platform.
            </p>
        </div>

        <div class="mb-4">
            <h5 class="font-weight-bold mb-2">7. Changes to These Terms</h5>
            <p class="text-muted">
                We may revise these Terms & Conditions periodically. Continued use of JobHub after
                changes are posted constitutes acceptance of the updated terms.
            </p>
        </div>

        <p class="text-muted small mt-5">
            Questions about these terms? <a href="{{ url('/#contact') }}">Get in touch with us</a>.
        </p>
    </div>
</section>
@endsection
