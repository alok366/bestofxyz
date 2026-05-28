<?php

use App\Events\UserCreated;


global $events;

$events->listen(UserCreated::class, [GenerateUserKeys::class, 'handle']);

return $events;
