<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\API\JsonResponseTrait;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Http\Request;
use Inertia;
use Illuminate\Support;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class CustomerConrtoller extends Controller {

    use JsonResponseTrait;

    public $currencies = [
        'NGN' => [ 'slug' => 'NGN', 'name' => 'Nigerian Naira' ],
        'USD' => [ 'slug' => 'USD', 'name' => 'United States Dollar' ],
        'EUR' => [ 'slug' => 'EUR', 'name' => 'Euro' ],
        'JPY' => [ 'slug' => 'JPY', 'name' => 'Japanese Yen' ],
        'GBP' => [ 'slug' => 'GBP', 'name' => 'British Pound Sterling' ],
        'AUD' => [ 'slug' => 'AUD', 'name' => 'Australian Dollar' ],
        'CAD' => [ 'slug' => 'CAD', 'name' => 'Canadian Dollar' ],
        'CHF' => [ 'slug' => 'CHF', 'name' => 'Swiss Franc' ],
        'CNY' => [ 'slug' => 'CNY', 'name' => 'Chinese Yuan' ],
        'SEK' => [ 'slug' => 'SEK', 'name' => 'Swedish Krona' ],
        'NZD' => [ 'slug' => 'NZD', 'name' => 'New Zealand Dollar' ],
        'NOK' => [ 'slug' => 'NOK', 'name' => 'Norwegian Krone' ],
        'INR' => [ 'slug' => 'INR', 'name' => 'Indian Rupee' ],
        'BRL' => [ 'slug' => 'BRL', 'name' => 'Brazilian Real' ],
        'ZAR' => [ 'slug' => 'ZAR', 'name' => 'South African Rand' ],
        'RUB' => [ 'slug' => 'RUB', 'name' => 'Russian Ruble' ],
        'SGD' => [ 'slug' => 'SGD', 'name' => 'Singapore Dollar' ],
        'HKD' => [ 'slug' => 'HKD', 'name' => 'Hong Kong Dollar' ],
        'MXN' => [ 'slug' => 'MXN', 'name' => 'Mexican Peso' ],
        'TRY' => [ 'slug' => 'TRY', 'name' => 'Turkish Lira' ],
        'KRW' => [ 'slug' => 'KRW', 'name' => 'South Korean Won' ],
    ];

    private function wallets( $wallet_slug = null ) {

        $wallets = [];

        foreach ( auth()->user()->wallets as $key => $wallet ) {
            $wallets[ $wallet->slug ] = [
                'name' => $wallet->name,
                'slug' => $wallet->slug,
                'balance' => $wallet->balance,
                'transaction_count' => $wallet->transactions->count()
            ];
        }

        return $wallets;
    }


    public function displayDashboard(Request $request) {
        $user = User::find($request->user()->id);
        $payLink = route('post-transfer-funds', ['recipient_id' => $user->id]);
        $data = [
            'wallets' => $this->wallets(),
            'pay_link' => $payLink,
            'user' => $user,
        ];
        return $request->wantsJson() ? $this->respondWithSuccess($data): Inertia\Inertia::render('Dashboard', $data);
    }

    public function displayTransferPage(Request $request)  {
        $recipientId = $request->query('recipient_id');
        $users = User::where('id', '!=', $request->user()->id)->get();

        if (!$users->find($recipientId) && $recipientId) {
            return $request->wantsJson()
                ? $this->respondWithError("SORRY USER WITH ID: $recipientId NOT FOUND")
                : abort(404);
        }

        $data = [
            'users' => $users->pluck('name', 'id'),
            'wallets' => collect($this->wallets())->where('balance', '>', 0)->all(),
            'recipient_id' => $recipientId,
        ];

        return $request->wantsJson()  ? $this->respondWithSuccess($data) : Inertia\Inertia::render('Payment/TransferHome', $data);
    }

    public function makeFundTransfer(Request $request) {
        $user = $request->user();

        $validated = $request->validate([
            'amount' => ['required', 'min:1', "max:$user->balance", 'numeric'],
            'recipient_id' => ['required'],
            'wallet_slug' => ['required', 'in:' . implode(',', array_keys($this->currencies))],
        ]);

        $recipient = User::findOrFail($validated['recipient_id']);

        if (!$recipient) {
            return back()->with(session()->flash('error', 'User not found'));
        }

        try {
            $meta = [
                'remark' => "You sent $validated[amount] to $recipient->name",
                'reference' => support\Str::uuid(),
            ];

            $recipientWallet = $recipient->hasWallet($validated['wallet_slug']);

            if (!$recipientWallet) {
                $recipient = $recipient->createWallet($this->currencies[$validated['wallet_slug']]);
            }

            $user->transfer($recipient, $validated['amount'], $meta);
        } catch (\Throwable $th) {
            Log::error("CustomerController->makeFundTransfer: $th->getMessage()");
            return $request->wantsJson()
                ? $this->respondWithError("Something happened: $th->getMessage()")
                : back()->withErrors(['default' => "Something happened: $th->getMessage()"]);
        }

        return $request->wantsJson() 
        ? $this->respondWithSuccess('Transaction Successful.') 
        : redirect(route('dashboard'))->with(['message' => 'Transaction Successful.']);
    }

    public function displayCreateWalletPage( Request $request ) {
        $userWallets = collect( $this->wallets() )->pluck( 'slug'  );
        $currencies = collect($this->currencies);
        $currencies = $currencies ->filter(function($currency) use($userWallets) {
            return !in_array($currency['slug'], array_values($userWallets->toArray()));
        });

        $data = [];
        $data['currencies'] = $currencies;

        return $request->wantsJson()
           ? $this->respondWithSuccess(  data:$data  )
           : Inertia\Inertia::render( 'Payment/CreateWalletHome', $data  );
    }

    public function createNewWallet( Request $request ) {
        $user = User::find( $request->user()->id );

        $validated = $request->validate( [
            'initial_balance' => [ 'numeric' ],
            'slug' => [ 'required', 'in:'.implode( ',', array_keys( $this->currencies ) ) ]
        ] );

        $selectedCurrency = optional( $this->currencies )[ $validated[ 'slug' ] ];

        try {
            $userHasWallet = $user->hasWallet( $selectedCurrency[ 'slug' ] );

            if ( $userHasWallet ) {
               return   $request->wantsJson()  
                ? $this->respondWithError('You Already have this wallet' )
                : redirect()->back()->withErrors( [ 'default' =>'You Already have this wallet' ] )->withInput();
            }
            $wallet = $user->createWallet( $selectedCurrency );
            $wallet->deposit( abs( $validated[ 'initial_balance' ] ), [ 'remark' => 'initial balance', 'reference' => Support\Str::uuid() ] );
        } catch ( \Throwable $th ) {
            Log::error( 'CustomerConrtoller->createWallet: '.$th->getMessage() );
            return $request->wantsJson() 
              ? $this->respondWithError('something went wrong: '.$th->getMessage())
              : redirect()->back()->withErrors( [ 'default' =>'something went wrong: '.$th->getMessage() ] )->withInput();
        }


        return $request->wantsJson() 
          ? $this->respondWithSuccess('Wallet created successfuly')
          :  redirect( route( 'dashboard' ) )->with( [ 'message' =>'Wallet created successfuly' ] );
    }

}
