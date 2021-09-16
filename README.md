# Nova Cashier Subscription Fields
Nova Cashier Subscription package provide two fields, credit card and plans user can subscribe which can be use similar to Forge/Envoyer like subscription management for user. 


Features:
 - save credit card information (Stripe)
 - change subscribe plan (Stripe)
 - display message if user is on active plan
 - display message if user is on trial
 - display message if user canceled and if user is still have trial period left
 - display message if user canceled and is user is still on grace period
  



## Table of Contents

 - [Screenshots](#screenshots)
 - [Installation](#installation)
 - [Usage](#usage)
 - [License](#license)

## Screenshots

![credit card and payment plan](https://user-images.githubusercontent.com/1147313/118728386-eeb3f980-b7e8-11eb-8895-1988e9c539be.png)
## Installation

This package can be installed via command:

```bash
composer require monaye/nova-cashier-subscription
```

## Usage

```php
Plan::make($planList),

CreditCard::make($intentClientSecret, $stripeKey),
```

### Plan List Format
```
return [
            [
                'label' => 'Free Plan',
                'value' => self::FREE_PLAN,
            ],
            [
                'label' => 'Standard Plan',
                'value' => config('stripe.standard_plan')
            ],
            [
                'label' => 'Premium Plan',
                'value' => config('stripe.premium_plan'),
            ],
        ];
```

### Intent Client Secret

For Intent Client Secret you can simply get with :  
```$user->createSetupIntent()->client_secret```  
[Please check Laravel docs for detail](https://laravel.com/docs/8.x/billing#payment-methods-for-subscriptions)

### Handle request 

Since every application handle little bit differently, the package doesn't come with a controller that will handle the request when user submit the creditcard update and plan update request. 

You can defined the FQCN with method name in the config file like below: 

```php
[
    'handleCreditUpdate' => '\App\Http\Controllers\SubscriptionController@changeSubscription',
    'handlePlanUpdate' => '\App\Http\Controllers\SubscriptionController@updateCreditCard'
]
```

To publish the config file:  
`php artisan vendor:publish --provider="Monaye\NovaCashierSubscription\ToolServiceProvider"`




## License

#### The License is free to use for non-military use and if you are: 
1. Personal use and make less than 1 million US dollar  per year
2. Corporate/Organization make less than 1 million revenue per year

#### License fees : 
If you make more than 1 million US dollar, license fees \$100/year. 
If you corporate/organization make more than 1 million revenue per year, license fees $150/year.

#### Use of license fee
All the revenue generate from this software will be donate back to following: 
1. To open source community (50%)
2. To organization that support education for children (25%)
3. To organization that fight for the Global Warming (25%)


## More Screenshots


![grace-period-message](https://user-images.githubusercontent.com/1147313/133651929-3be7fe4a-9b00-41c3-823d-02f11a36136b.png)
![registred credit card](https://user-images.githubusercontent.com/1147313/133651937-c82b1c1f-f3b0-479a-895c-b030aac5c116.png)
![active message](https://user-images.githubusercontent.com/1147313/133651940-ac7203a4-79ab-4101-9a45-379b0c267280.png)
