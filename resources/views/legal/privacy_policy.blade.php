@extends('layouts.landing_page')

@section('content')
<section class="py-5" style="margin-top: 90px;">
    <div class="container" style="max-width: 860px;">

        <h1 class="font-weight-bold mb-2">Privacy Policy</h1>
        <p class="text-muted mb-5">Last updated: {{ date('F Y') }}</p>

        <div class="mb-4">
            <h5 class="font-weight-bold mb-2">1. Information We Collect</h5>
            <p class="text-muted">
                When you register on JobHub, we collect information such as your name, email address,
                phone number, resume, work experience, and other profile details you choose to provide.
                Employers who register with us provide company details for the purpose of posting jobs
                and reviewing applications.
            </p>
        </div>

        <div class="mb-4">
            <h5 class="font-weight-bold mb-2">2. How We Use Your Information</h5>
            <p class="text-muted">
                We use the information you provide to match job seekers with relevant opportunities,
                enable employers to review applications, send job alerts and notifications you opt into,
                and improve our platform's features and services.
            </p>
        </div>

        <div class="mb-4">
            <h5 class="font-weight-bold mb-2">3. Sharing of Information</h5>
            <p class="text-muted">
                Your resume and application details are shared only with the employers you apply to or
                who invite you to their opportunities. We do not sell your personal data to third parties.
            </p>
        </div>

        <div class="mb-4">
            <h5 class="font-weight-bold mb-2">4. Data Security</h5>
            <p class="text-muted">
                We take reasonable technical and organizational measures to protect your personal
                information against unauthorized access, alteration, disclosure, or destruction.
            </p>
        </div>

        <div class="mb-4">
            <h5 class="font-weight-bold mb-2">5. Your Rights</h5>
            <p class="text-muted">
                You may update or delete your profile information at any time from your account settings.
                For any privacy-related requests, please reach out to us via the Contact page.
            </p>
        </div>

        <div class="mb-4">
            <h5 class="font-weight-bold mb-2">6. Changes to This Policy</h5>
            <p class="text-muted">
                We may update this Privacy Policy from time to time. Any changes will be posted on this
                page with a revised "last updated" date.
            </p>
        </div>

        <p class="text-muted small mt-5">
            If you have any questions about this Privacy Policy, please
            <a href="{{ url('/#contact') }}">contact us</a>.
        </p>
    </div>
</section>
@endsection
