<?php

namespace App\Enums\Tenant;

enum TenantTypes: string
{
    case LAW_ENFORCEMENT = 'law_enforcement';
    case MENTAL_HEALTH = 'mental_health';
    case CRISIS_NEGOTIATION = 'crisis_negotiation';
    case EMERGENCY_SERVICES = 'emergency_services';
    case CORRECTIONS = 'corrections';
    case MILITARY = 'military';
    case PRIVATE_SECURITY = 'private_security';
    case OTHER = 'other';

    /**
     * Get all tenant types as an array of label-value pairs.
     */
    public static function toArray(): array
    {
        return array_map(
            fn (TenantTypes $type) => [
                'label' => $type->label(),
                'value' => $type->value,
            ],
            self::cases()
        );
    }

    /**
     * Get the human-readable label for the tenant type.
     */
    public function label(): string
    {
        return match ($this) {
            self::LAW_ENFORCEMENT => 'Law Enforcement',
            self::MENTAL_HEALTH => 'Mental Health',
            self::CRISIS_NEGOTIATION => 'Crisis negotiation',
            self::EMERGENCY_SERVICES => 'Emergency Services',
            self::CORRECTIONS => 'Corrections',
            self::MILITARY => 'Military',
            self::PRIVATE_SECURITY => 'Private Security',
            self::OTHER => 'Other',
        };
    }

    /**
     * Create an enum instance from an array representation.
     *
     * @param  array  $array  The array containing the enum value
     *
     * @return static|null The enum instance or null if not found
     */
    public static function fromArray(array $array): ?static
    {
        if (!isset($array['value'])) {
            return null;
        }

        return self::tryFrom($array['value']);
    }

    /**
     * Convert the enum to a JSON representation.
     *
     * @param  int  $options  JSON encoding options
     *
     * @return string JSON representation of the enum
     */
    public function toJson($options = 0): string
    {
        return json_encode([
            'label' => $this->label(),
            'value' => $this->value,
        ], $options);
    }
}
