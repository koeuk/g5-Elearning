<?php

namespace App\Core;

use App\Models\User;

/**
 * Form-validation + credential checks, reimplemented from the assorted
 * apply/require validation functions in the old models. Sign-in and password
 * change now use App\Core\Auth for real verification instead of the old
 * re-hash trick.
 *
 * Each method returns an array of field => message; an all-empty array means
 * "valid" (matching the original convention the views check against).
 */
final class Validation
{
    /** A letter, a digit, min 5 chars (the app's original rule). */
    public const PASSWORD_PATTERN = '/^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9@$!%*?&]{5,}$/i';

    /** Shared message for a password that fails PASSWORD_PATTERN. */
    private const WEAK_PASSWORD =
        'Password must contain at least one letter and one digit, and be at least 5 characters long.';

    /** Validate a student/trainer sign-in by email. */
    public static function signin(string $email, string $password, int $role = User::ROLE_STUDENT): array
    {
        $errors = ['email' => '', 'password' => ''];

        if ($email === '') {
            $errors['email'] = 'Input email !';
        }
        if ($password === '') {
            $errors['password'] = 'Input password !';
        }

        if ($email !== '' && $password !== '') {
            $user = User::findByEmail($email, $role);
            if (count($user) > 0) {
                if (!Auth::verify($password, (string) $user['password'])) {
                    $errors['password'] = 'Password incorrect !';
                }
            } else {
                $errors['email'] = 'No account found for this email';
            }
        }

        return $errors;
    }

    /** Validate an admin sign-in by (display) name. */
    public static function adminSignin(string $name, string $password): array
    {
        $errors = ['name' => '', 'password' => ''];

        if ($name === '') {
            $errors['name'] = 'Input name !';
        }
        if ($password === '') {
            $errors['password'] = 'Input password !';
        }

        if ($name !== '' && $password !== '') {
            $user = User::findAdminByName($name);
            if (count($user) > 0) {
                if (!Auth::verify($password, (string) $user['password'])) {
                    $errors['password'] = 'Password incorrect !';
                }
            } else {
                $errors['name'] = 'No account found for this name';
            }
        }

        return $errors;
    }

    /** Validate a student registration form. */
    public static function registration(
        string $name,
        string $email,
        string $password,
        string $phone,
        string $confirmPassword
    ): array {
        $info = ['name' => '', 'email' => '', 'password' => '', 'password_comfirm' => '', 'phone' => ''];

        if ($name === '') {
            $info['name'] = 'Name is required';
        }
        if ($email === '') {
            $info['email'] = 'Email is required';
        }
        if ($password === '') {
            $info['password'] = 'Password is required';
        }
        if ($phone === '') {
            $info['phone'] = 'Phone is required';
        } elseif (!preg_match('/^(0|\+855)(\d{9})$/', $phone)) {
            $info['phone'] = 'Invalid phone number format.';
        }
        if ($confirmPassword === '') {
            $info['password_comfirm'] = 'Confirm your password';
        }
        if ($password !== '' && $confirmPassword !== '' && $password !== $confirmPassword) {
            $info['password_comfirm'] = 'Your passwords do not match!';
        }

        $strong = $password !== '' && preg_match(self::PASSWORD_PATTERN, $password);

        if (
            $name !== '' && $email !== '' && $password !== '' && $phone !== '' && $confirmPassword !== ''
            && $strong && $password === $confirmPassword
        ) {
            // All fields valid — the only remaining reason to reject is a taken email.
            if (count(User::findByEmail($email)) > 0) {
                $info['email'] = 'This email already exists!';
            }
        } elseif (!$strong && $password !== '') {
            $info['password'] = self::WEAK_PASSWORD;
        }

        return $info;
    }

    /** Validate a password change. Verifies the current password via Auth. */
    public static function passwordChange(
        string $stored,
        string $current,
        string $new,
        string $confirm
    ): array {
        $info = ['currentPassword' => '', 'newPassword' => '', 'confirmPassword' => ''];

        if (!Auth::verify($current, $stored)) {
            $info['currentPassword'] = 'Password incorrect!';
            return $info;
        }

        if ($new !== $confirm) {
            $info['confirmPassword'] = 'Passwords do not match!';
            return $info;
        }

        if (!preg_match(self::PASSWORD_PATTERN, $new)) {
            $info['newPassword'] = self::WEAK_PASSWORD;
        }

        return $info;
    }

    public static function strongPassword(string $password): array
    {
        $result = ['password' => ''];
        if ($password !== '' && !preg_match(self::PASSWORD_PATTERN, $password)) {
            $result['password'] = self::WEAK_PASSWORD;
        }
        return $result;
    }

    /** True when the errors array has no non-empty messages. */
    public static function passes(array $errors): bool
    {
        foreach ($errors as $message) {
            if ($message !== '') {
                return false;
            }
        }
        return true;
    }
}
