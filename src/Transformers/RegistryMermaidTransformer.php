<?php

declare(strict_types=1);

namespace Rokde\StateMachine\Transformers;

use BackedEnum;
use Rokde\StateMachine\Contracts\RegistryTransformer;
use Rokde\StateMachine\TransitionRegistry;
use UnitEnum;

final readonly class RegistryMermaidTransformer implements RegistryTransformer
{
    public function __construct(
        private MermaidDirection $direction = MermaidDirection::TOP,
    ) {}

    public function transform(TransitionRegistry $registry): string
    {
        $lines = ['stateDiagram-v2'];
        $this->buildDirection($lines);

        foreach ($registry->transitions() as $from => $events) {
            foreach ($events as $event => $transition) {
                $to = $this->resolveKey($transition->to);
                $lines[] = "  {$from} --> {$to} : {$event}";
            }
        }

        return implode("\n", $lines)."\n";
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function buildDirection(array &$lines): void
    {
        if ($this->direction === MermaidDirection::TOP) {
            return;
        }

        $lines[] = "  direction {$this->direction->value}";
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
