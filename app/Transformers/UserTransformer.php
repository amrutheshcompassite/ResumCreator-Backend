<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;
use League\Fractal\ParamBag;


class UserTransformer extends TransformerAbstract
{


    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => (int)$user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_no' => $user->phone_no
        ];
    }
}
