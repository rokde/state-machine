<?php

declare(strict_types=1);

namespace Rokde\StateMachine;

use UnitEnum;

final readonly class StateMachine
{
    public TransitionRegistry $registry;

    public function __construct(?TransitionRegistry $registry = null)
    {
        $this->registry = $registry ?? new TransitionRegistry;
    }

    public function apply(UnitEnum|string $currentState, UnitEnum|string $event, mixed $context): UnitEnum|string
    {
        try {
            $transition = $this->registry->transition($currentState, $event);
        } catch (\Throwable $e) {
            throw new \RuntimeException('There is no transition registered for the given state and event.', previous: $e);
        }

        if (! ($transition->guard)($context)) {
            throw new \RuntimeException('The guard failed.');
        }

        $transition->action->call($this, $context);

        return $transition->to;
    }
}
