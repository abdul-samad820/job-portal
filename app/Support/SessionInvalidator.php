<?php

namespace App\Support;

use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\DB;

class SessionInvalidator
{
    /**
     * Delete every DB-stored session that's authenticated as the given
     * user on the given guard. Laravel stores the "is this session logged
     * in" flag as a payload key named login_{guard}_{sha1(SessionGuard::class)}
     * rather than a queryable column — this app's guards ('user', 'admin')
     * aren't the default 'web' guard, so the built-in
     * logoutOtherDevices()/AuthenticateSession middleware (which only
     * tracks the default guard) doesn't cover them. We scan session
     * payloads directly instead.
     *
     * Used after a password reset so a stolen/old session can't stay
     * logged in once the real owner has reset their password
     * (Phase5 SEC-23).
     */
    public static function invalidateForUser(int $userId, string $guard = 'user'): int
    {
        if (config('session.driver') !== 'database') {
            return 0;
        }

        $loginKey = 'login_'.$guard.'_'.sha1(SessionGuard::class);
        $deleted = 0;

        DB::table('sessions')->select(['id', 'payload'])
            ->orderBy('id')
            ->chunkById(200, function ($sessions) use ($loginKey, $userId, &$deleted) {
                foreach ($sessions as $session) {
                    $payload = @unserialize(base64_decode($session->payload));

                    if (! is_array($payload) || ! array_key_exists($loginKey, $payload)) {
                        continue;
                    }

                    if ((string) $payload[$loginKey] === (string) $userId) {
                        DB::table('sessions')->where('id', $session->id)->delete();
                        $deleted++;
                    }
                }
            });

        return $deleted;
    }
}
