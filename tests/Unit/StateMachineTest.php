<?php

use Rokde\StateMachine\StateMachine;
use Rokde\StateMachine\TransitionRegistry;

it('can create its own registry', function () {
    $sm = new StateMachine;

    expect($sm->registry)->toBeInstanceOf(TransitionRegistry::class);
});

it('can use a given registry', function () {
    $registry = new TransitionRegistry;

    $sm = new StateMachine($registry);
    expect($sm->registry)->toBe($registry);
});
