<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Job;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Public XML sitemap — static pages + every currently live/visible job.
     *
     * Cached briefly since it can involve scanning every job row; a few
     * minutes of staleness is irrelevant for crawlers and keeps this from
     * running a full query on every bot hit.
     */
    public function index()
    {
        $urls = Cache::remember('sitemap.xml', now()->addMinutes(30), function () {
            $urls = [
                ['loc' => route('user.home'), 'priority' => '1.0', 'changefreq' => 'daily'],
                ['loc' => route('user.jobs'), 'priority' => '0.9', 'changefreq' => 'hourly'],
                ['loc' => route('privacy.policy'), 'priority' => '0.3', 'changefreq' => 'monthly'],
                ['loc' => route('terms.conditions'), 'priority' => '0.3', 'changefreq' => 'monthly'],
            ];

            Job::visible()
                ->orderByDesc('updated_at')
                ->select(['id', 'updated_at'])
                ->chunk(500, function ($jobs) use (&$urls) {
                    foreach ($jobs as $job) {
                        $urls[] = [
                            'loc' => route('user.job_single', $job->id),
                            'lastmod' => $job->updated_at->toAtomString(),
                            'priority' => '0.8',
                            'changefreq' => 'weekly',
                        ];
                    }
                });

            Admin::where('role', 'admin')
                ->where('is_active', true)
                ->whereNotNull('slug')
                ->select(['id', 'slug', 'updated_at'])
                ->chunk(500, function ($companies) use (&$urls) {
                    foreach ($companies as $company) {
                        $urls[] = [
                            'loc' => route('company.show', $company->slug),
                            'lastmod' => $company->updated_at->toAtomString(),
                            'priority' => '0.6',
                            'changefreq' => 'weekly',
                        ];
                    }
                });

            return $urls;
        });

        return response()
            ->view('sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }

    /**
     * robots.txt — keeps authenticated/admin areas out of crawl budget
     * and points crawlers at the sitemap. Dynamic (not a static public
     * file) so the sitemap URL always matches the current app domain.
     */
    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /superadmin',
            'Disallow: /user/dashboard',
            'Disallow: /user/user_account_settings',
            'Disallow: /user/user_profile',
            'Disallow: /user/applied-job',
            'Disallow: /user/saved-jobs',
            'Disallow: /user/invites',
            'Disallow: /user/resumes',
            'Disallow: /my-interviews',
            'Disallow: /reset-password',
            'Disallow: /email/verify',
            'Allow: /',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines), 200)
            ->header('Content-Type', 'text/plain');
    }
}
