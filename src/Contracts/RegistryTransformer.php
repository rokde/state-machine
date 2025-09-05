<?php

declare(strict_types=1);

namespace Rokde\StateMachine\Contracts;

use Rokde\StateMachine\TransitionRegistry;

interface RegistryTransformer
{
    public function transform(TransitionRegistry $registry): mixed;
}
