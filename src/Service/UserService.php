<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;

class UserService
{

    // user infor holen
    public function inforUser(?User $user): ?array
    {
        $userRole       = $user->getRoles();

        // suchen nach aktuelle Role --> um die aktuelle Recht zu finden
        // default Role ist die, die höher Recht hat (kleiner role ID)
        // Holen Sie sich die Schlüssel des Arrays
        $keys           = array_keys($userRole);
        // Finden Sie den kleinsten Schlüssel
        $smallestKey    = min($keys);
        $defaultRole    = $userRole[$smallestKey];

        return [
            'user'                      => $user,
            'userRole'                  => $userRole,
            'defaultRole'               => $defaultRole,
        ];
    }


}