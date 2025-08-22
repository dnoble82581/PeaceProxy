<?php

namespace App\Repositories\NegotiationUser;

use App\Contracts\NegotiationUserRepositoryInterface;
use App\Models\NegotiationUser;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\App\Models\_IH_NegotiationUser_C;

class NegotiationUserRepository implements NegotiationUserRepositoryInterface
{
    public function createNegotiationUser($data)
    {
        return NegotiationUser::create($data);
    }

    public function getNegotiationUsers(): Collection|_IH_NegotiationUser_C|array
    {
        return NegotiationUser::all();
    }

    public function updateNegotiationUser($id, $data)
    {
        $negotiationUser = $this->getNegotiationUser($id);
        $negotiationUser->update($data);
        return $negotiationUser;
    }

    public function getNegotiationUser($id)
    {
        return NegotiationUser::find($id);
    }

    public function deleteNegotiationUser($id)
    {
        $negotiationUser = $this->getNegotiationUser($id);
        $negotiationUser->delete();
        return $negotiationUser;
    }
}
