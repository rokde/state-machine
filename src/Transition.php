<?php

declare(strict_types=1);

namespace Rokde\StateMachine;

use BackedEnum;
use Closure;
use UnitEnum;

final readonly class Transition
{
    public Closure $guard;

    public Closure $action;

    public function __construct(
        public BackedEnum|UnitEnum|string $to,
        ?Closure $guard = null,
        ?Closure $action = null,
    ) {
        $this->guard = $guard ?? fn (): bool => true;
        $this->action = $action ?? function (): void {};
    }
}
