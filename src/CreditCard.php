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
    public function __construct($client_secret, $stripe_key = NULL)
    {
        parent::__construct();

        $this->locale(config("app.locale"));

        $this->withMeta([
            'client_secret' => $client_secret,
            'stripe_key' => $stripe_key ?? config("cashier.key"),
            'card_last_four' => auth()->user()->card_last_four,
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
