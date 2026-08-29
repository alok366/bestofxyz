<?php

namespace App\Transformers;

use App\Models\User;

class UserTransformer
{
    /**
     * Transform a single user for API response.
     *
     * Whitelists only safe, client-facing fields. The User model's
     * $hidden array is a second line of defence, but the transformer
     * is the authoritative contract between backend and frontend.
     */
    public static function toProfile(User $user): array
    {
        return [
            'id'         => (int) $user->id,
            'username'   => $user->username,
            'email'      => $user->email,
            'role'       => $user->role,
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }

    /**
     * Transform a user for public-facing contexts (comments, submissions)
     * where email and role should not be exposed.
     */
    public static function toPublic(User $user): array
    {
        return [
            'id'       => (int) $user->id,
            'username' => $user->username,
        ];
    }

    /**
     * Transform a collection of users for listing (admin, leaderboard).
     *
     * @param iterable $users
     * @return array
     */
    public static function collectionToList(iterable $users): array
    {
        $list = [];
        foreach ($users as $user) :
            $list[] = self::toProfile($user);
        endforeach;

        return $list;
    }
}
