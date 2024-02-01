<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Traits\API\JsonResponseTrait;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller {
    /**
    * Display the registration view.
    */

    use JsonResponseTrait;

    public function create(): Response {
        return Inertia::render( 'Auth/Register' );
    }

    /**
    * Handle an incoming registration request.
    *
    * @throws \Illuminate\Validation\ValidationException
    */

    public function store( Request $request ) {

        $validated = Validator::make( $request->all(),  [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => [ 'required', 'confirmed', Rules\Password::defaults() ],
        ] );

        if ( $validated->fails() ) {
            return   $request->wantsJson()
            ? throw new HttpResponseException( response()->json( [
                'error' => $validated->errors()->first(),
                'errors' => $validated->errors(),
            ], 422 ) )
            :back()->withErrors( $validated->errors() )->withInput();
        }

        $user = User::create( [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make( $request->password ),
        ] );

        event( new Registered( $user ) );
        Auth::login( $user );
        return $request->wantsJson()?
        $this->respondWithUserProfileAndToken( $user )
        :redirect( RouteServiceProvider::HOME );
    }
}
