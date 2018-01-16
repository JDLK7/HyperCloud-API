<?php

namespace App\Services;

use App\User;

class UserService {

    /**
     * Crea un nuevo usuario y su cuenta correspondiente.
     *
     * @param array $data
     * @return \App\User
     */
    public function createUser($data) {

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);
        
        $user->account()->create([
            'userName' => $data['userName'],
        ]);

        $user->account->suscription()->associate($data['suscription']);
        $user->account->save();

        return $user;
    }

}