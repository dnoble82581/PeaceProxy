<?php

namespace App\Repositories\NegotiationSubject;

use App\Contracts\NegotiationSubjectRepositoryInterface;
use App\Models\NegotiationSubject;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\App\Models\_IH_NegotiationSubject_C;

class NegotiationSubjectRepository implements NegotiationSubjectRepositoryInterface
{
    public function createNegotiationSubject($data)
    {
        return NegotiationSubject::create($data);
    }

    public function getNegotiationSubjects(): Collection|_IH_NegotiationSubject_C|array
    {
        return NegotiationSubject::all();
    }

    public function updateNegotiationSubject($id, $data)
    {
        $negotiationSubject = $this->getNegotiationSubject($id);
        $negotiationSubject->update($data);
        return $negotiationSubject;
    }

    public function getNegotiationSubject($id)
    {
        return NegotiationSubject::find($id);
    }

    public function deleteNegotiationSubject($id)
    {
        $negotiationSubject = $this->getNegotiationSubject($id);
        $negotiationSubject->delete();
        return $negotiationSubject;
    }
}
