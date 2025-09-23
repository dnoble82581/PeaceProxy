<?php

namespace App\Support\EventNames;

class NegotiationEventNames
{
    public const OBJECTIVE_UPDATED = 'ObjectiveUpdated';
    public const OBJECTIVE_CREATED = 'ObjectiveCreated';
    public const OBJECTIVE_DELETED = 'ObjectiveDeleted';

    public const DEMAND_UPDATED = 'DemandUpdated';
    public const DEMAND_CREATED = 'DemandCreated';
    public const DEMAND_DELETED = 'DemandDeleted';

    public const DELIVERY_PLAN_CREATED = 'DeliveryPlanCreated';
    public const DELIVERY_PLAN_UPDATED = 'DeliveryPlanUpdated';
    public const DELIVERY_PLAN_DELETED = 'DeliveryPlanDestroyed';

    public const RFI_CREATED = 'RFICreated';
}
