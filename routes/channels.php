<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('project.{projectId}', function ($user, $projectId) {
    // Check if the user is a member of any project that has the given projectId.
    // This ensures the user is part of the project before allowing them to listen.
    return $user->projects->contains($projectId);
}); 