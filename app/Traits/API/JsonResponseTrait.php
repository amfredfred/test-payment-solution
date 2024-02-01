<?php

namespace App\Traits\API;

use App\Http\Resources\UserProfileResource;
use App\Models\User;

trait JsonResponseTrait
 {
    /**
    * Respond with a JSON success message.
    *
    * @param string $message
    * @param array $data
    * @param int $statusCode
    * @return \Illuminate\Http\JsonResponse
    */

    public function respondWithSuccess( $message = 'Success', $data = [], $redirect = null,  $statusCode = 200 ) {
        return response()->json( [
            'status' => 'success',
            'message' => $message,
            'redirect' => $redirect,
            ...( array )$data,
        ], $statusCode );
    }

    /**
    * Respond with a JSON error message.
    *
    * @param string $message
    * @param int $statusCode
    * @return \Illuminate\Http\JsonResponse
    */

    public function respondWithError( $message = 'Error', $devMessage = null, $redirect = null, $statusCode = 500 ) {
        return response()->json( [
            'status' => 'error',
            'devMessage' => $devMessage,
            'redirect' => $redirect,
            'message' => $message,
        ], $statusCode );
    }

    protected function respondUnprocessableEntity( $message = 'Unprocessable Entity' ) {
        return response()->json( [ 'error' => $message ], 422 );
    }

    /**
    * Respond with a JSON not found message ( 404 ).
    *
    * @param string $message
    * @return \Illuminate\Http\JsonResponse
    */

    public function respondNotFound( $message = 'Not Found' ) {
        return $this->respondWithError( message:$message, statusCode:404 );
    }

    /**
    * Respond with a JSON method not allowed message ( 405 ).
    *
    * @param string $message
    * @return \Illuminate\Http\JsonResponse
    */

    public function respondMethodNotAllowed( $message = 'Method Not Allowed', $redirect = null ) {
        return $this->respondWithError( message:$message, statusCode: 405 );
    }

    public function respondWithUserProfile( $user, $statusCode = 200 ) {
        $message = 'User profile retrieved successfully';
        $data = [ 'profile' =>new UserProfileResource( $user ) ];
        return $this->respondWithSuccess( data:$data, message:$message, statusCode:$statusCode );
    }

    public function respondWithUserProfileAndToken( User $user,  $message = null ) {
        $data[ 'token' ] = $user->createToken('AUTH_TOKEN')->plainTextToken;
        $data[ 'profile' ] = new UserProfileResource( $user );
        $message = $message ?? __( 'json-api-auth.success' )  ;
        return $this->respondWithSuccess( data:$data, statusCode:200, message:$message );
    }
}
