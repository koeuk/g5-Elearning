<?php

namespace Tests;

use App\Core\Auth;
use PHPUnit\Framework\TestCase;

/**
 * Guards the password-handling fix: passwords are hashed on write and verified
 * properly on read, while legacy plaintext rows still authenticate (once).
 */
final class AuthTest extends TestCase
{
    public function test_hash_is_not_plaintext_and_verifies(): void
    {
        $hash = Auth::hash('secret1');

        $this->assertNotSame('secret1', $hash);
        $this->assertTrue(Auth::isHashed($hash));
        $this->assertTrue(Auth::verify('secret1', $hash));
        $this->assertFalse(Auth::verify('wrong', $hash));
    }

    public function test_legacy_plaintext_password_still_verifies(): void
    {
        // A row that predates the fix stores the password in plaintext.
        $stored = 'plainpass1';

        $this->assertFalse(Auth::isHashed($stored));
        $this->assertTrue(Auth::verify('plainpass1', $stored));
        $this->assertFalse(Auth::verify('nope', $stored));
        $this->assertTrue(Auth::needsRehash($stored));
    }

    public function test_proper_hash_does_not_need_rehash(): void
    {
        $this->assertFalse(Auth::needsRehash(Auth::hash('secret1')));
    }
}
