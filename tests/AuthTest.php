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
    public function testHashIsNotPlaintextAndVerifies(): void
    {
        $hash = Auth::hash('secret1');

        $this->assertNotSame('secret1', $hash);
        $this->assertTrue(Auth::isHashed($hash));
        $this->assertTrue(Auth::verify('secret1', $hash));
        $this->assertFalse(Auth::verify('wrong', $hash));
    }

    public function testLegacyPlaintextPasswordStillVerifies(): void
    {
        // A row that predates the fix stores the password in plaintext.
        $stored = 'plainpass1';

        $this->assertFalse(Auth::isHashed($stored));
        $this->assertTrue(Auth::verify('plainpass1', $stored));
        $this->assertFalse(Auth::verify('nope', $stored));
        $this->assertTrue(Auth::needsRehash($stored));
    }

    public function testProperHashDoesNotNeedRehash(): void
    {
        $this->assertFalse(Auth::needsRehash(Auth::hash('secret1')));
    }
}
