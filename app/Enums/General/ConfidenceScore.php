<?php

namespace App\Enums\General;

enum ConfidenceScore: string
{
    case VeryLow = '0.0';
    case Low = '0.25';
    case Medium = '0.5';
    case High = '0.75';
    case VeryHigh = '1.0';

    /**
     * Create a ConfidenceScore enum from various input formats
     */
    public static function fromMixed(string|int|float|null $value): ?self
    {
        if ($value === null) {
            return null;
        }

        // Handle integer 0 or float 0.0 or string "0"
        if ($value === 0 || $value === 0.0 || $value === '0') {
            return self::VeryLow;
        }

        // Handle specific string values directly
        if (is_string($value)) {
            if ($value === '0.25') {
                return self::Low;
            } elseif ($value === '0.5') {
                return self::Medium;
            } elseif ($value === '0.75') {
                return self::High;
            } elseif ($value === '1.0' || $value === '1') {
                return self::VeryHigh;
            }
        }

        // Handle specific float values directly
        if (is_float($value) || is_int($value)) {
            if ($value == 0.25) {
                return self::Low;
            } elseif ($value == 0.5) {
                return self::Medium;
            } elseif ($value == 0.75) {
                return self::High;
            } elseif ($value == 1.0 || $value == 1) {
                return self::VeryHigh;
            }
        }

        // Convert to string with proper format
        if (is_numeric($value)) {
            $value = number_format((float) $value, 1, '.', '');
        }

        // Try to create from the formatted string
        try {
            return self::from($value);
        } catch (\ValueError $e) {
            // If not found, return null or a default value
            return null;
        }
    }

    public function label(): string
    {
        return match ($this) {
            self::VeryLow => 'Very Low',
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
            self::VeryHigh => 'Very High',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::VeryLow => 'Extremely uncertain, minimal confidence in the assessment.',
            self::Low => 'Limited confidence, significant uncertainty remains.',
            self::Medium => 'Moderate confidence, balanced certainty and uncertainty.',
            self::High => 'Strong confidence, minimal uncertainty remains.',
            self::VeryHigh => 'Extremely confident, virtually certain assessment.',
        };
    }

    public function value(): float
    {
        return (float) $this->value;
    }
}
