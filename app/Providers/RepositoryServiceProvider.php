<?php

namespace App\Providers;

use App\Contracts\ContactRepositoryInterface;
use App\Contracts\ConversationRepositoryInterface;
use App\Contracts\ConversationUserRepositoryInterface;
use App\Contracts\DemandRepositoryInterface;
use App\Contracts\DocumentRepositoryInterface;
use App\Contracts\HookRepositoryInterface;
use App\Contracts\HostageRepositoryInterface;
use App\Contracts\MessageRepositoryInterface;
use App\Contracts\MoodLogRepositoryInterface;
use App\Contracts\NegotiationRepositoryInterface;
use App\Contracts\NoteRepositoryInterface;
use App\Contracts\ObjectiveRepositoryInterface;
use App\Contracts\TriggerRepositoryInterface;
use App\Contracts\WarningRepositoryInterface;
use App\Contracts\WarrantRepositoryInterface;
use App\Repositories\Contact\ContactRepository;
use App\Repositories\Conversation\ConversationRepository;
use App\Repositories\ConversationUser\ConversationUserRepository;
use App\Repositories\Demand\DemandRepository;
use App\Repositories\Document\DocumentRepository;
use App\Repositories\Hook\HookRepository;
use App\Repositories\Hostage\HostageRepository;
use App\Repositories\Message\MessageRepository;
use App\Repositories\MoodLog\MoodLogRepository;
use App\Repositories\Negotiation\NegotiationRepository;
use App\Repositories\Note\NoteRepository;
use App\Repositories\Objective\ObjectiveRepository;
use App\Repositories\Trigger\TriggerRepository;
use App\Repositories\Warning\WarningRepository;
use App\Repositories\Warrant\WarrantRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Call repository
        $this->app->bind(
            CallRepositoryInterface::class,
            CallRepository::class
        );

        $this->app->bind(
            WarrantRepositoryInterface::class,
            WarrantRepository::class,
        );

        $this->app->bind(
            TriggerRepositoryInterface::class,
            TriggerRepository::class,
        );

        $this->app->bind(
            NegotiationRepositoryInterface::class,
            NegotiationRepository::class
        );

        // Bind Message repository
        $this->app->bind(
            MessageRepositoryInterface::class,
            MessageRepository::class,
        );

        // Bind Conversation repository
        $this->app->bind(
            ConversationRepositoryInterface::class,
            ConversationRepository::class,
        );

        // Bind ConversationUser repository
        $this->app->bind(
            ConversationUserRepositoryInterface::class,
            ConversationUserRepository::class,
        );

        $this->app->bind(
            MessageRepositoryInterface::class,
            MessageRepository::class
        );

        // Bind Hook repository
        $this->app->bind(
            HookRepositoryInterface::class,
            HookRepository::class
        );

        $this->app->bind(
            TriggerRepositoryInterface::class,
            TriggerRepository::class
        );

        // Bind MoodLog repository
        $this->app->bind(
            MoodLogRepositoryInterface::class,
            MoodLogRepository::class
        );

        // Bind Hostage repository
        $this->app->bind(
            HostageRepositoryInterface::class,
            HostageRepository::class
        );

        $this->app->bind(
            ContactRepositoryInterface::class,
            ContactRepository::class
        );

        // Bind Demand repository
        $this->app->bind(
            DemandRepositoryInterface::class,
            DemandRepository::class
        );

        // Bind Warning repository
        $this->app->bind(
            WarningRepositoryInterface::class,
            WarningRepository::class
        );

        // Bind Document repository
        $this->app->bind(
            DocumentRepositoryInterface::class,
            DocumentRepository::class
        );

        // Bind Note repository
        $this->app->bind(
            NoteRepositoryInterface::class,
            NoteRepository::class
        );

        // Bind Objective repository
        $this->app->bind(
            ObjectiveRepositoryInterface::class,
            ObjectiveRepository::class
        );
    }

    public function boot(): void
    {
    }
}
