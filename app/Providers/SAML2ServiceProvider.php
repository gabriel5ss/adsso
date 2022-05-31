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
        
            // $user vai receber os dados vindos do AD;
            $user = $event->getSaml2User();

            // $userData vai receber os dados vindos do $user, modifique da forma que for necessaria;
            $userData = [
                'id' => $user->getUserId(),
                'username' => $user->getAttribute("http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name"),
                'email' => $user->getAttribute("http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"),
                'first_name' => $user->getAttribute("http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname"),
                'last_name' => $user->getAttribute("http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname"),
                'assertion' => $user->getRawSamlAssertion()
            ];

            // dd($userData);
            
            // dd($userData['attributes']['http://schemas.microsoft.com/identity/claims/tenantid'][0]);

            //pesquisar se o id está salvo no banco
            $laravelUser = User::where('ad_id', $userData['id'])->first(); //find user by ID or attribute
            // if it does not exist create it and go on  or show an error message

            // dd($laravelUser);
            $name = $userData['name'][0];
            $email = $userData['email'][0];

            if($laravelUser != null){

                //se existir cria o login;
                // dd('entrou no != null');
                Auth::login($laravelUser);
                
            } else {
                // se não, cria o usuario local
                // dd('entrou no else');
                //criação do usuario, verificar as informações que serão necessarias
                //para a utilização do sistema
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'ad_id' => $userData['id'],
                    'password' => Hash::make('Batata123@'),
                ]);

                // dd($user);

                Auth::login($user);
            }
            
            // dd(Auth::id());
        });

        Event::listen('Aacotroneo\Saml2\Events\Saml2LogoutEvent', function ($event) {
            Auth::logout();
            Session::save();
        });
    }
}
