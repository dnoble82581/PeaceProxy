<?php

namespace App\DTOs\NegotiationUser;

use App\Enums\User\UserNegotiationRole;
use Carbon\Carbon;

class NegotiationUserDTO
{
    public function __construct(
        public int $negotiation_id,
        public int $user_id,
        public UserNegotiationRole $role,
        public string $status,
        public ?Carbon $joined_at,
        public ?Carbon $left_at,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
    ) {
    }

    public static function fromArray(array $data): NegotiationUserDTO
    {
        return new self(
            $data['negotiation_id'],
            $data['user_id'],
            $data['role'],
            $data['status'],
            $data['joined_at'],
            $data['left_at'],
            $data['created_at'],
            $data['updated_at'],
        );
    }

    public function toArray()
    {
        return [
            'negotiation_id' => $this->negotiation_id,
            'user_id' => $this->user_id,
            'role' => $this->role,
            'status' => $this->status,
            'joined_at' => $this->joined_at,
            'left_at' => $this->left_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}
