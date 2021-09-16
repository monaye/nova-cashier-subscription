<?php

namespace Monaye\NovaCashierSubscription;

use Exception;
use Laravel\Cashier\Billable;
use Laravel\Nova\ResourceTool;

class Plan extends ResourceTool
{
    /**
     * Subscription constructor.
     *
     * @param string $subscription
     */
    public function __construct(array $plans, $user)
    {
        if (!in_array(Billable::class, class_uses($user))) {
            throw new Exception("second parameters passed to the Plan constructor should use Laravel\Cashier\Billable trait.", 1);
        }

        parent::__construct();

        $this->locale(config("app.locale"));
        $this->plan_title();

        $this->withMeta([
            'plans' => $plans,
            'card_last_four' => $user ? $user->card_last_four : auth()->user()->card_last_four,
            'subscription_plan' => $user ? $user->subscriptions()->first() : auth()->user()->subscriptions()->first(),
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

    public function locale($locale)
    {
        return $this->withMeta([
            'locale' => $locale
        ]);
    }
}
