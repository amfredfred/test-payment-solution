<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource {
    /**
    * Transform the resource into an array.
    *
    * @return array<string, mixed>
    */

    public function toArray( Request $request ): array {

        $profile = [];
        $profile[ 'name' ] = $this->name;
        $profile[ 'email' ] = $this->email;
        $profile[ 'phone' ] = $this->phone;
        $profile[ 'address' ] = $this->address;
        $profile[ 'bio' ] = $this->bio;
        $profile[ 'interests' ] = $this->interests;
        $profile[ 'username' ] = $this->username;
        $profile[ 'profilePics' ] = $this->profile_pics;
        $profile[ 'coverPics' ] = $this->cover_pics;
        $profile[ 'age' ] = $this->age;
        $profile[ 'gender' ] = $this->gender;
        $profile[ 'role' ] = $this->role;
        $profile[ 'emailVerifiedAt' ] = $this->email_verified_at;

        return $profile;
    }
}
