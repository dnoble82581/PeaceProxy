<?php

namespace App\Http\Controllers\Api;

use App\Enums\General\Genders;
use App\Enums\User\UserNegotiationRole;
use App\Enums\Warrant\BondType;
use App\Enums\Warrant\WarrantStatus;
use App\Enums\Warrant\WarrantType;

class EnumController
{
    public function userNegotiationRoles()
    {
        $roles = array_map(function (UserNegotiationRole $role) {
            return [
                'value' => $role->value,
                'label' => $role->label(),
                'description' => $role->description(),
            ];
        }, UserNegotiationRole::cases());

        return response()->json($roles);
    }

    public function subjectNegotiationBondTypes()
    {
        $bondTypes = array_map(function (BondType $type) {
            return [
                'value' => $type->value,
                'label' => $type->label(),
            ];
        }, BondType::cases());

        return response()->json($bondTypes);
    }

    public function warrantStatus()
    {
        $warrantStatuses = array_map(function (WarrantStatus $status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
            ];
        }, WarrantStatus::cases());

        return response()->json($warrantStatuses);
    }

    public function warrantTypes()
    {
        $warrantTypes = array_map(function (WarrantType $type) {
            return [
                'value' => $type->value,
                'label' => $type->label(),
            ];
        }, WarrantType::cases());

        return response()->json($warrantTypes);
    }

    public function genders()
    {
        $genders = array_map(function (Genders $gender) {
            return [
                'value' => $gender->value,
                'label' => $gender->label(),
            ];
        }, Genders::cases());

        return response()->json($genders);
    }

}
