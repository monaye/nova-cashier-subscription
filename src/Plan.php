<?php

namespace Monaye\NovaCashierSubscription;

use Laravel\Nova\ResourceTool;

class Plan extends ResourceTool
{
    /**
    * Subscription constructor.
    *
    * @param string $subscription
    */
    public function __construct(array $plans)
    {
        parent::__construct();

        $this->withMeta([
            'plans' => $plans,
            'card_last_four' => \Auth::user()->owner->card_last_four,
            'subscription_plan' =>  \Auth::user()->owner->subscriptions()->first(),
        ]);
        // $this->withMeta(compact('plans'));
    }

    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Nova Cashier Plan';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'nova-cashier-plan';
    }
}
