<x-mail::message>
# New Job Matches for You

Hello **{{ $user->name }}**,  

We found **{{ count($jobs) }} new job {{ count($jobs) > 1 ? 'opportunities' : 'opportunity' }}** matching your preferences for:  
**"{{ $keywords }}"**

---

@foreach($jobs as $job)
<x-mail::panel>
### {{ $job->title }}

**Company:** {{ $job->admin->company_name ?? 'N/A' }}  
**Location:** {{ $job->location }}  
**Job Type:** {{ ucfirst($job->type) }}  
**Experience Required:** {{ $job->experience ?? 'Not specified' }}

@if($job->min_salary && $job->max_salary)
**Salary Range:** ₹{{ number_format($job->min_salary) }} – ₹{{ number_format($job->max_salary) }}
@endif

@if($job->last_date)
**Application Deadline:** {{ \Carbon\Carbon::parse($job->last_date)->format('d M Y') }}
@endif
</x-mail::panel>

<x-mail::button :url="url()->to(route('apply_form_job_application', $job->id))" color="primary">
Apply Now
</x-mail::button>

---
@endforeach

If you no longer wish to receive these alerts, you can update your preferences in your account settings.

Thanks,  
**{{ config('app.name') }} Team**
</x-mail::message>