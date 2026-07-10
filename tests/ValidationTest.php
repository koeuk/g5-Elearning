<?php

namespace Tests;

use App\Core\Validation;
use PHPUnit\Framework\TestCase;

/**
 * The app intentionally does not enforce a strong-password policy: any
 * non-empty password is accepted (e.g. "12345678"). These tests pin that
 * behaviour so a future change doesn't silently reintroduce strictness.
 */
final class ValidationTest extends TestCase
{
    /**
     * @dataProvider anyPasswords
     */
    public function testAnyNonEmptyPasswordIsAccepted(string $password): void
    {
        $this->assertSame('', Validation::strongPassword($password)['password'], "\"$password\" should be accepted");
    }

    /** @return array<int, array{0: string}> */
    public static function anyPasswords(): array
    {
        return [
            ['12345678'],
            ['1234'],
            ['password'],
            ['abc'],
            ['a'],
            ['Abcd@123'],
        ];
    }

    public function testRegistrationDoesNotFlagASimplePassword(): void
    {
        // A plain numeric password must not be rejected for "strength". We leave
        // the phone empty so validation stops before the email-uniqueness check
        // (which would need the database); that isolates the password rule.
        $errors = Validation::registration('Sok', 'sok@example.com', '12345678', '', '12345678');

        $this->assertSame('', $errors['password']);
        $this->assertSame('', $errors['password_comfirm']);
    }

    public function testRegistrationStillRequiresAPassword(): void
    {
        $errors = Validation::registration('Sok', 'sok@example.com', '', '012345678', '');

        $this->assertNotSame('', $errors['password']);
    }

    public function testRegistrationStillRequiresMatchingConfirmation(): void
    {
        $errors = Validation::registration('Sok', 'sok@example.com', '12345678', '012345678', '87654321');

        $this->assertNotSame('', $errors['password_comfirm']);
    }
}
