<?php

namespace App\Contracts;

interface DeliveryPlanRepositoryInterface
{
    public function createDeliveryPlan($data);

    public function getDeliveryPlan($id);

    public function getDeliveryPlans();

    public function updateDeliveryPlan($id, $data);

    public function deleteDeliveryPlan($id);
}
