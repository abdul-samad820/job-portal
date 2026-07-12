<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Job;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuperAdminExportController extends Controller
{
    /**
     * Streamed CSV so large admin/job lists don't have to be built in
     * memory as one giant string before sending.
     */
    public function admins(Request $request): StreamedResponse
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $admins = Admin::where('role', 'admin')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($status === 'suspended', fn ($q) => $q->where('is_active', false))
            ->withCount('jobs')
            ->latest()
            ->get();

        return $this->streamCsv('admins-export-'.now()->format('Y-m-d').'.csv',
            ['ID', 'Company Name', 'Email', 'Contact Number', 'Location', 'Status', 'Jobs Posted', 'Joined On'],
            $admins,
            fn ($a) => [
                $a->id, $a->company_name, $a->email, $a->contact_number, $a->location,
                $a->is_active ? 'Active' : 'Suspended', $a->jobs_count, $a->created_at->format('Y-m-d'),
            ]
        );
    }

    public function jobs(Request $request): StreamedResponse
    {
        $search = trim((string) $request->query('search'));
        $status = $request->query('status');

        $jobs = Job::with('admin', 'category')
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($status === 'hidden', fn ($q) => $q->where('is_hidden', true))
            ->latest()
            ->get();

        return $this->streamCsv('jobs-export-'.now()->format('Y-m-d').'.csv',
            ['ID', 'Title', 'Company', 'Category', 'Location', 'Type', 'Status', 'Last Date', 'Posted On'],
            $jobs,
            fn ($j) => [
                $j->id, $j->title, $j->admin->company_name ?? '—', $j->category->name ?? '—',
                $j->location, $j->type, $j->is_hidden ? 'Hidden' : 'Live',
                optional($j->last_date)->format('Y-m-d'), $j->created_at->format('Y-m-d'),
            ]
        );
    }

    private function streamCsv(string $filename, array $header, $rows, callable $rowMapper): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows, $rowMapper) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header);

            foreach ($rows as $row) {
                fputcsv($out, array_map([$this, 'sanitizeCsvCell'], $rowMapper($row)));
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function sanitizeCsvCell($value)
    {
        if (is_string($value) && preg_match('/^[=+\-@]/', $value)) {
            return "'".$value;
        }

        return $value;
    }
}
