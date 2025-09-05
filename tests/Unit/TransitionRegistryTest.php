<?php

use Rokde\StateMachine\Transition;
use Rokde\StateMachine\TransitionRegistry;
use Tests\Articles\ArticleEvent;
use Tests\Articles\ArticleState;
use Tests\Orders\OrderEvent;
use Tests\Orders\OrderState;

it('can set a transition by BackedEnum', function () {
    $registry = new TransitionRegistry;
    $registry->addTransition(OrderState::New, OrderEvent::Pay, OrderState::Paid);

    expect($registry->transition(OrderState::New, OrderEvent::Pay))->toBeInstanceOf(Transition::class);

    expect($registry->transitions())->toBeArray();
});

it('can set a transition by Enum', function () {
    $registry = new TransitionRegistry;
    $registry->addTransition(ArticleState::New, ArticleEvent::Publish, ArticleState::Published);

    expect($registry->transition(ArticleState::New, ArticleEvent::Publish))->toBeInstanceOf(Transition::class);

    expect($registry->transitions())->toBeArray();
});

it('can set a transition by string', function () {
    $registry = new TransitionRegistry;
    $registry->addTransition(OrderState::New->value, OrderEvent::Pay->value, OrderState::Paid->value);

    expect($registry->transition(OrderState::New->value, OrderEvent::Pay->value))->toBeInstanceOf(Transition::class);

    expect($registry->transitions())->toBeArray();
});
