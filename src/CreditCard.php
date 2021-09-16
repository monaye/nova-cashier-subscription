<?php

namespace Monaye\NovaCashierSubscription;

use Exception;
use Laravel\Cashier\Billable;
use Laravel\Nova\ResourceTool;

class CreditCard extends ResourceTool
{

    /**
     * Subscription constructor.
     *
     * @param string $subscription
     */
    public function __construct($client_secret, $stripe_key = NULL, $user = NULL)
    {

        if (!in_array(Billable::class, class_uses($user))) {
            throw new Exception("third parameters passed to the Plan constructor should use Laravel\Cashier\Billable trait.", 1);
        }

        parent::__construct();

        $this->locale(config("app.locale"));

        $this->withMeta([
            'client_secret' => $client_secret,
            'stripe_key' => $stripe_key ?? config("cashier.key"),
            'card_last_four' => $user ? $user->card_last_four : auth()->user()->card_last_four,
        ]);
    }

    /**
     * Get the displayable title of the field
     *
     * @return string
     */
    public function title($title)
    {
        return $this->withMeta([
            'title' => $title,
        ]);
    }

    public function locale($locale)
    {
        return $this->withMeta([
            'locale' => $locale
        ]);
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
