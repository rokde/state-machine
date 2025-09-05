<?php

declare(strict_types=1);

namespace Rokde\StateMachine;

use BackedEnum;
use RuntimeException;
use UnitEnum;

class TransitionRegistry
{
    protected array $transitions = [];

    public function __construct() {}

    /**
     * Adds a transition to the registry
     *
     * @param  \UnitEnum|string  $currentState  The current state the transition should be processed on (e.g. new - article is new)
     * @param  \UnitEnum|string  $event  The event the transition should execute for (e.g. publish - article should be published)
     * @param  \UnitEnum|string  $toState  The resulting state the transition should be processed to (e.g. published - article is now published)
     * @param  callable|null  $guard  The guard checks if the context item can be transitioned (e.g. title exists - article without title can not be published)
     * @param  callable|null  $action  The action that should be triggered after the transition has been made successfully (e.g. post message to slack)
     */
    public function addTransition(
        BackedEnum|UnitEnum|string $currentState,
        BackedEnum|UnitEnum|string $event,
        BackedEnum|UnitEnum|string $toState,
        ?callable $guard = null,
        ?callable $action = null,
    ): self {
        $currentStateKey = $this->resolveKey($currentState);
        $eventKey = $this->resolveKey($event);

        $this->transitions[$currentStateKey][$eventKey] = new Transition($toState, $guard, $action);

        return $this;
    }

    public function transitions(BackedEnum|UnitEnum|string|null $state = null): array
    {
        if ($state !== null) {
            $stateKey = $this->resolveKey($state);

            return $this->transitions[$stateKey] ?? [];
        }

        return $this->transitions;
    }

    public function transition(BackedEnum|UnitEnum|string $state, BackedEnum|UnitEnum|string $event): Transition
    {
        $stateKey = $this->resolveKey($state);
        $eventKey = $this->resolveKey($event);

        return $this->transitions[$stateKey][$eventKey] ?? throw new RuntimeException('There is no transition registered for the given state and event.');
    }

    private function resolveKey(BackedEnum|UnitEnum|string $currentState): string|int
    {
        return $currentState instanceof BackedEnum
            ? $currentState->value
            : ($currentState instanceof UnitEnum
                ? $currentState->name
                : $currentState
            );
    }
}
