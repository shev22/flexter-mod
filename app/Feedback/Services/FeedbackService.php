<?php

namespace App\Feedback\Services;

use App\Feedback\Models\Feedback;
use App\Models\User;

class FeedbackService
{
    /**
     * @param  array{name: string, email: string, subject: string, message: string}  $data
     */
    public function store(array $data, ?User $user = null): Feedback
    {
        return Feedback::create([
            'user_id' => $user?->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
        ]);
    }
}
