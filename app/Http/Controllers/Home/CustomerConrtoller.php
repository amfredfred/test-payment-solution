<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\User;
use Bavix\Wallet\Models\Transaction;
use Illuminate\Http\Request;
use Inertia;
use Illuminate\Support;
use Illuminate\Support\Facades\Log;

class CustomerConrtoller extends Controller {

    public $currencies = [
        [ 'code' => 'NGN', 'name' => 'Nigerian Naira' ],
        [ 'code' => 'USD', 'name' => 'United STates Dollar' ],
    ];

    public function displayDashboard( Request $request ) {

       $user = User::find($request->user()->id);
       $wallets = [];

       foreach($user->wallets as $key => $wallet){
         $wallets[$wallet->slug] = [
            'name' => $wallet->name,
            'slug' => $wallet->slug,
            'balance' => $wallet->balance,
            'transaction_count' => $wallet->transactions->count()
         ];
       }

        return Inertia\Inertia::render( 'Dashboard', [
            'user' => $user,
            'wallets' => $wallets
        ] );
    }

    public function displayTransferPage( Request $request ) {
        return Inertia\Inertia::render( 'Payment/TransferHome', [
            'users' => User::where( 'id', '!=', $request->user()->id )->get(),
            'balance' => $request->user()->wallet->balance
        ] );
    }

    public function makeFundTransfer( Request $request ) {
        $user = $request->user();

        $validated = $request->validate( [
            'amount' => [ 'required', 'min:1',  "max:$user->balance", 'numeric' ],
            'recipient_id' => [ 'required' ],
            // 'status' => [ 'boolean' ]
        ] );

        $recipient = User::find( $validated[ 'recipient_id' ] );

        if ( !$recipient ) {
            return back()->with( session()->flash( 'error', 'User not found' ) );
        }

        try {

            $meta = [
                'remark' => 'You sent '. $validated[ 'amount' ] .' to '.User::where( 'id', $validated[ 'recipient_id' ] )->first()->name,
                'reference' => Support\Str::uuid()
            ];

            $refereance = Support\Str::uuid();
            $transaction = new Transaction();
            $transaction->amount =  $validated[ 'amount' ];
            $transaction->meta = json_encode( $meta );
            $transaction->uuid = $refereance;
            $transaction->type = 'transfer';
            $transaction->payable_id = $validated[ 'recipient_id' ];
            $transaction->confirmed = false;
            $transaction->save();

            $user->transfer( $recipient,  $validated[ 'amount' ],  json_encode( $meta ) );

        } catch ( \Throwable $th ) {
            return back()->with( session()->flash( 'error', 'Something happened: '. $th->getMessage() ) );
        }

        return back()->with( session()->flash( 'message', 'Transation Successful.' ) );
    }

    public function displayCreateWalletPage( Request $request ) {
        $currencies = $this->currencies;
        return Inertia\Inertia::render( 'Payment/CreateWalletHome', [
            'currencies' =>$currencies,
        ] );
    }

    public function createNewWallet( Request $request ) {
        $user = User::find( $request->user()->id );


        $validated = $request->validate( [
            'initial_balance' => [ 'numeric' ],
            'slug' => [ 'required' ]
        ] );

        $selectedCurrency = array_filter($this->currencies, function ($currency) {
           return $currency['code'] === 'NGN';
        });

        $selectedCurrency =  ['slug' => $selectedCurrency[0]['code'], 'name' => $selectedCurrency[0]['name']] ;
 
        try {
            $userHasWallet = $user->hasWallet($selectedCurrency['slug']); 
            if($userHasWallet){
               return redirect()->back()->withErrors( ['default' =>'You Already have this wallet'])->withInput();  
            }
            $wallet = $user->createWallet( $selectedCurrency);
            $wallet->deposit( abs( $validated[ 'initial_balance' ] ), [ 'remark' => 'initial balance', 'reference' => Support\Str::uuid() ] );
        } catch ( \Throwable $th ) {
            Log::error( 'CustomerConrtoller->createWallet: '.$th->getMessage() );
            return  redirect()->back()->withErrors( ['default' =>'something went wrong: '.$th->getMessage() ])->withInput();
        }
        return back()->with([ 'message' =>'Wallet created successfuly'] );
    }

}
