<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' && $user->hasVerifiedEmail();
    }

    public function view(User $user, SupportTicket $ticket): bool
    {
        return $this->viewAny($user) || $ticket->user_id === $user->id;
    }

    public function update(User $user, SupportTicket $ticket): bool
    {
        return $this->viewAny($user);
    }

    public function reply(User $user, SupportTicket $ticket): bool
    {
        return $this->view($user, $ticket) && ! in_array($ticket->status, ['resolved', 'closed'], true);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, SupportTicket $ticket): bool
    {
        return false;
    }
}
