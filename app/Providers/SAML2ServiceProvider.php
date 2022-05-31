<?php

namespace App\Providers;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class SAML2ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        // Event::listen('Aacotroneo\Saml2\Events\Saml2LoginEvent', function (Saml2LoginEvent $event) {
        //     $messageId = $event->getSaml2Auth()->getLastMessageId();
        //     // Add your own code preventing reuse of a $messageId to stop replay attacks

        //     $user = $event->getSaml2User();
        //     $userData = [
        //         'id' => $user->getUserId(),
        //         'attributes' => $user->getAttributes(),
        //         'assertion' => $user->getRawSamlAssertion()
        //     ];

        //     $inputs = [
        //         'sso_user_id'  => $user->getUserId(),
        //         'username'     => self::getValue($user->getAttribute('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name')),
        //         'email'        => self::getValue($user->getAttribute('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name')),
        //         'first_name'   => self::getValue($user->getAttribute('http://schemas.microsoft.com/identity/claims/displayname')),
        //         'last_name'    => self::getValue($user->getAttribute('http://schemas.microsoft.com/identity/claims/displayname')),
        //         'password'     => Hash::make('anything'),
        //      ];

        //     $user = User::where('sso_user_id', $inputs['sso_user_id'])->where('email', $inputs['email'])->first();
        //     if(!$user){
        //         $res = PortalUser::store($inputs);
        //         if($res['status'] == 'success'){
        //             $user  = $res['data'];
        //             Auth::guard('web')->login($user);
        //         }else{
        //             Log::info('SAML USER Error '.$res['messages']);
        //         }
        //     }else{
        //         Auth::guard('web')->login($user);
        //     }

        // });

        Event::listen('Aacotroneo\Saml2\Events\Saml2LoginEvent', function (Saml2LoginEvent $event) {
            $messageId = $event->getSaml2Auth()->getLastMessageId();
            // Add your own code preventing reuse of a $messageId to stop replay attacks
        
            $user = $event->getSaml2User();
            $userData = [
                'id' => $user->getUserId(),
                'attributes' => $user->getAttributes(),
                'assertion' => $user->getRawSamlAssertion()
            ];
            
            // dd($userData);

            $laravelUser = User::where('ad_id', $userData['id'])->first(); //find user by ID or attribute

            // dd($laravelUser);

            if($laravelUser != null){

                dd('entrou no != null');
                Auth::login($laravelUser);
                
            } else {
                
                dd('entrou no else');
                $user = User::create([
                    'name' => $userData['attributes']['http://schemas.microsoft.com/identity/claims/displayname'][0],
                    'email' => $userData['attributes']['http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name'][0],
                    'ad_id' => $userData['id'],
                    'password' => $userData['assertion'],
                ]);

                dd($user);

                Auth::login($user);
            }
            // if it does not exist create it and go on  or show an error message
            
            dd(Auth::id());
        });

        Event::listen('Aacotroneo\Saml2\Events\Saml2LogoutEvent', function ($event) {
            Auth::logout();
            Session::save();
        });
    }
}
