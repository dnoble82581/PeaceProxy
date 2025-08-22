<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any documents.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Users can view documents from their tenant
        return true;
    }

    /**
     * Determine if the user can view the document.
     *
     * @param User $user
     * @param Document $document
     * @return bool
     */
    public function view(User $user, Document $document): bool
    {
        // Users can view documents from their tenant
        if ($user->tenant_id !== $document->tenant_id) {
            return false;
        }

        // If the document is private, only the uploader or admin can view it
        if ($document->is_private) {
            return $user->id === $document->uploaded_by_id || $user->isAdmin();
        }

        return true;
    }

    /**
     * Determine if the user can create documents.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // All authenticated users can create documents
        return true;
    }

    /**
     * Determine if the user can update the document.
     *
     * @param User $user
     * @param Document $document
     * @return bool
     */
    public function update(User $user, Document $document): bool
    {
        // Users can only update documents from their tenant
        if ($user->tenant_id !== $document->tenant_id) {
            return false;
        }

        // Only the uploader or admin can update the document
        return $user->id === $document->uploaded_by_id || $user->isAdmin();
    }

    /**
     * Determine if the user can delete the document.
     *
     * @param User $user
     * @param Document $document
     * @return bool
     */
    public function delete(User $user, Document $document): bool
    {
        // Users can only delete documents from their tenant
        if ($user->tenant_id !== $document->tenant_id) {
            return false;
        }

        // Only the uploader or admin can delete the document
        return $user->id === $document->uploaded_by_id || $user->isAdmin();
    }

    /**
     * Determine if the user can restore the document.
     *
     * @param User $user
     * @param Document $document
     * @return bool
     */
    public function restore(User $user, Document $document): bool
    {
        // Only admins can restore documents
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the document.
     *
     * @param User $user
     * @param Document $document
     * @return bool
     */
    public function forceDelete(User $user, Document $document): bool
    {
        // Only admins can force delete documents
        return $user->isAdmin();
    }
}
