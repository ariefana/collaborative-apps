<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('document.{id}', function (User $user, $id) {
    // Kembalikan data user agar tahu siapa yang sedang online di dokumen ini
    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
});
