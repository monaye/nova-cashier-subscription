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

        $this->plan_title();

        $this->withMeta([
            'plans' => $plans,
            'card_last_four' => auth()->user()->card_last_four,
            'subscription_plan' =>  auth()->user()->subscriptions()->first(),
        ]);
        // $this->withMeta(compact('plans'));
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
