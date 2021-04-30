<?php

namespace Monaye\NovaCashierSubscription;

use Laravel\Nova\ResourceTool;

class CreditCard extends ResourceTool
{

    /**
    * Subscription constructor.
    *
    * @param string $subscription
    */
    public function __construct($client_secret, $stripe_key)
    {
        parent::__construct();

        
        $this->withMeta([
            'client_secret' => $client_secret,
            'stripe_key' => $stripe_key,
            'card_last_four' => \Auth::user()->owner->card_last_four,
        ]);
    }

    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Nova Cashier Credit Card';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'nova-cashier-credit-card';
    }
}
