<?php

namespace App\Support\Channels;

final class Negotiation
{
    public const NEGOTIATION_PATTERN = 'negotiation.{negotiationId}';

    public const NEGOTIATION_OBJECTIVE_PATTERN = 'negotiation.{negotiationId}.objective';

    public const NEGOTIATION_DEMAND_PATTERN = 'negotiation.{negotiationId}.demand';

    public const NEGOTIATION_DELIVERY_PLAN_PATTERN = 'negotiation.{negotiationId}.delivery-plan';

    public const NEGOTIATION_RFI_PATTERN = 'negotiation.{negotiationId}.rfi';

    public static function negotiation(int $id)
    {
        return "negotiation.$id";
    }

    public static function negotiationObjective(int $id)
    {
        return "negotiation.$id.objective";
    }

    public static function negotiationDemand(int $id)
    {
        return "negotiation.$id.demand";
    }

    public static function negotiationDeliveryPlan(int $id)
    {
        return "negotiation.$id.delivery-plan";
    }

    public static function negotiationRfi(int $id)
    {
        return "negotiation.$id.rfi";
    }
}
