<?php

namespace App\Repositories\Negotiation;

use App\Contracts\NegotiationRepositoryInterface;
use App\Models\Negotiation;
use Exception;

class NegotiationRepository implements NegotiationRepositoryInterface
{
    public function createNegotiation($data)
    {
        return Negotiation::create($data);
    }

    /**
     * @throws Exception
     */
    public function getNegotiations()
    {
        // Write repository should not implement read operations
        throw new Exception('Read operation not supported in write repository');
    }

    public function updateNegotiation($id, $data)
    {
        $negotiation = $this->getNegotiation($id);
        $negotiation->update($data);
        return $negotiation;
    }

    public function getNegotiation($id, ?array $with = ['creator'])
    {
        // This is needed for updateNegotiation and deleteNegotiation
        return Negotiation::with($with)->find($id);
    }

    public function deleteNegotiation($id)
    {
        $negotiation = $this->getNegotiation($id);
        $negotiation->delete();
        return $negotiation;
    }
}
