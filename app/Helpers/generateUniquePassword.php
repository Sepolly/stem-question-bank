<?php

/**
 * Generate a cryptographically-strong password.
 *
 * - Uses CSPRNG (random_int) for unbiased selection.
 * - Ensures at least one lowercase, uppercase, digit, and symbol (by default).
 * - Avoids visually ambiguous chars by default (can be toggled).
 *
 * @param  int  $length  Desired length (min 4). Default 8.
 * @param  bool  $useSymbols  Include symbols. Default true.
 * @param  bool  $allowAmbiguous  Include visually ambiguous chars (O0oIl1|). Default false.
 *
 * @throws InvalidArgumentException
 */
function generateUniquePassword(int $length = 8, bool $useSymbols = true, bool $allowAmbiguous = false): string
{
    if ($length < 4) {
        throw new InvalidArgumentException('Password length must be at least 4.');
    }

    // Character classes
    $lower = 'abcdefghjkmnpqrstuvwxyz'; // no i, l, o by default
    $upper = 'ABCDEFGHJKMNPQRSTUVWXYZ'; // no I, L, O by default
    $digits = '23456789';               // no 0,1 by default
    $symbols = '!@#$%^&*()-_=+[]{};:,.?/';

    // If ambiguous characters are allowed, add them back
    if ($allowAmbiguous) {
        $lower .= 'ilo';
        $upper .= 'ILO';
        $digits .= '01';
        // symbols are generally fine; no change needed
    }

    // Build pools
    $classes = [
        str_split($lower),
        str_split($upper),
        str_split($digits),
    ];
    if ($useSymbols) {
        $classes[] = str_split($symbols);
    }

    // Combined pool
    $pool = array_merge(...$classes);

    // Helper: secure pick from array
    $pick = static fn (array $arr) => $arr[random_int(0, count($arr) - 1)];

    // 1) Ensure at least one character from each required class
    $passwordChars = [];
    foreach ($classes as $class) {
        $passwordChars[] = $pick($class);
    }

    // 2) Fill the rest from the full pool
    for ($i = count($passwordChars); $i < $length; $i++) {
        $passwordChars[] = $pick($pool);
    }

    // 3) Secure Fisher–Yates shuffle
    for ($i = count($passwordChars) - 1; $i > 0; $i--) {
        $j = random_int(0, $i);
        if ($i !== $j) {
            $tmp = $passwordChars[$i];
            $passwordChars[$i] = $passwordChars[$j];
            $passwordChars[$j] = $tmp;
        }
    }

    return implode('', $passwordChars);
}
