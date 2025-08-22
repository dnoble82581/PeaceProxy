<?php

namespace App\Repositories\Subject;

use App\Contracts\SubjectRepositoryInterface;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\App\Models\_IH_Subject_C;

class SubjectRepository implements SubjectRepositoryInterface
{
    public function createSubject($data)
    {
        return Subject::create($data);
    }

    public function getSubjects(): Collection|_IH_Subject_C|array
    {
        return Subject::all();
    }

    public function updateSubject($id, $data)
    {
        $subject = $this->getSubject($id);
        $subject->update($data);
        return $subject;
    }

    public function getSubject($id)
    {
        return Subject::find($id);
    }

    public function deleteSubject($id)
    {
        $subject = $this->getSubject($id);
        $subject->delete();
        return $subject;
    }
}
