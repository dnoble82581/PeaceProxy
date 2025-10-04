<?php

namespace App\Support;

use Illuminate\Support\Str;

class NameParser
{
    /** Parse a human name into parts. */
    public static function parse(string $full): array
    {
        $name = trim(Str::of($full)->squish());

        // If "Last, First Middle" format, flip it
        if (str_contains($name, ',')) {
            [$last, $rest] = array_map('trim', explode(',', $name, 2));
            $name = $rest.' '.$last;
        }

        // Remove leading titles
        $titles = ['mr', 'mrs', 'ms', 'miss', 'dr', 'prof', 'officer', 'deputy', 'sgt', 'sergeant', 'lt', 'capt', 'chief'];
        $name = preg_replace('/^(?:'.implode('|', $titles).')\.?\s+/i', '', $name);

        // Pull off trailing suffixes
        $suffixes = ['jr', 'sr', 'ii', 'iii', 'iv', 'v', 'esq', 'md', 'phd', 'dds', 'do'];
        $suffix = null;
        $tmp = preg_split('/\s+/', $name);
        if ($tmp && in_array(strtolower(end($tmp)), $suffixes, true)) {
            $suffix = array_pop($tmp);
            $name = implode(' ', $tmp);
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        if (count($parts) === 0) {
            return ['first' => '', 'middle' => null, 'last' => '', 'suffix' => $suffix];
        }
        if (count($parts) === 1) {
            return ['first' => $parts[0], 'middle' => null, 'last' => '', 'suffix' => $suffix];
        }

        // Multiword last-name particles (lowercase by convention)
        $particles = [
            'al', 'bin', 'bint', 'da', 'de', 'del', 'della', 'der', 'di', 'du', 'la', 'le', 'van', 'von', 'st', 'st.',
            'van de', 'van der', 'de la', 'de los', 'de las',
        ];

        // Build the last name from the end, attaching particles
        $last = array_pop($parts);
        while ($parts) {
            $peek = strtolower(end($parts));
            if (in_array($peek, $particles, true)) {
                $last = array_pop($parts).' '.$last;
            } else {
                break;
            }
        }

        $first = array_shift($parts);
        $middle = $parts ? implode(' ', $parts) : null;

        return ['first' => $first, 'middle' => $middle, 'last' => $last, 'suffix' => $suffix];
    }
}
