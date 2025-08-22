<?php

namespace App\Enums\User;

enum UserNegotiationStatuses: string
{
    case active = 'active';
    case inactive = 'inactive';
    case left = 'left';
    case rejected = 'rejected';
}
