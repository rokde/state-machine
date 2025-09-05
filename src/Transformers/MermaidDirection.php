<?php

declare(strict_types=1);

namespace Rokde\StateMachine\Transformers;

enum MermaidDirection: string
{
    case LEFT = 'LR';
    case TOP = 'TB';
    case BOTTOM = 'BT';
    case RIGHT = 'RL';
}
