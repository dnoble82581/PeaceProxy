<?php

namespace App\Contracts;

interface DeliveryPlannablesRepositoryInterface
{
    public function createDeliveryPlannable($data);

    public function getDeliveryPlannable($id);

    public function getDeliveryPlannables();

    public function getDeliveryPlannablesByDeliveryPlanId($deliveryPlanId);

    public function updateDeliveryPlannable($id, $data);

    public function deleteDeliveryPlannable($id);
}
