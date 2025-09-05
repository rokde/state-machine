<?php

use Rokde\StateMachine\TransitionRegistry;
use Tests\Orders\OrderContext;
use Tests\Orders\OrderEvent;
use Tests\Orders\OrderState;

/**
 * The test cases tries to test the following business process:
 */
it('transition registry holds the business process', function (): void {
    $context = new OrderContext(
        orderId: 123,
        amountCents: 10_00,
        authorizedCents: 10_00
    );

    $registry = new TransitionRegistry;

    $registry->addTransition(
        OrderState::New,
        OrderEvent::Pay,
        OrderState::Paid,
        guard: fn (OrderContext $context) => $context->authorizedCents >= $context->amountCents,
        action: function (OrderContext $context) {
            expect($context->orderId)->toBe(123);
            //            echo 'send receipt for '.$context->orderId.PHP_EOL;
        },
    )->addTransition(
        OrderState::New,
        OrderEvent::Cancel,
        OrderState::Cancelled,
    )->addTransition(
        OrderState::Paid,
        OrderEvent::Ship,
        OrderState::Shipped,
        action: function (OrderContext $context) {
            expect($context->orderId)->toBe(123);
            //            echo 'send shipment for '.$context->orderId.PHP_EOL;
        }
    );

    $sm = new Rokde\StateMachine\StateMachine($registry);

    $canApply = $sm->canApply(OrderState::New, OrderEvent::Pay, $context);
    expect($canApply)->toBeTrue();

    $result = $sm->apply(OrderState::New, OrderEvent::Pay, $context);

    expect($result)->toBe(OrderState::Paid);

    $result = $sm->apply($result, OrderEvent::Ship, $context);

    expect($result)->toBe(OrderState::Shipped);
});

it('blocks payment without authorization with guard', function (): void {
    $context = new OrderContext(
        orderId: 123,
        amountCents: 10_00,
        authorizedCents: 5_00
    );

    $registry = new TransitionRegistry;

    $registry->addTransition(
        OrderState::New,
        OrderEvent::Pay,
        OrderState::Paid,
        guard: fn (OrderContext $context) => $context->authorizedCents >= $context->amountCents,
        action: function (OrderContext $context) {
            echo 'send receipt for '.$context->orderId.PHP_EOL;
        },
    )->addTransition(
        OrderState::New,
        OrderEvent::Cancel,
        OrderState::Cancelled,
    )->addTransition(
        OrderState::Paid,
        OrderEvent::Ship,
        OrderState::Shipped,
        action: function (OrderContext $context) {
            echo 'send shipment for '.$context->orderId.PHP_EOL;
        }
    );

    $sm = new Rokde\StateMachine\StateMachine($registry);

    $canApply = $sm->canApply(OrderState::New, OrderEvent::Pay, $context);
    expect($canApply)->toBeFalse();

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('The guard failed.');

    $sm->apply(OrderState::New, OrderEvent::Pay, $context);
});

it('rejects invalid transition', function (): void {
    $context = new OrderContext(
        orderId: 123,
        amountCents: 10_00,
        authorizedCents: 10_00
    );

    $registry = new TransitionRegistry;

    $registry->addTransition(
        OrderState::New,
        OrderEvent::Pay,
        OrderState::Paid,
        guard: fn (OrderContext $context) => $context->authorizedCents >= $context->amountCents,
        action: function (OrderContext $context) {
            echo 'send receipt for '.$context->orderId.PHP_EOL;
        },
    )->addTransition(
        OrderState::New,
        OrderEvent::Cancel,
        OrderState::Cancelled,
    )->addTransition(
        OrderState::Paid,
        OrderEvent::Ship,
        OrderState::Shipped,
        action: function (OrderContext $context) {
            echo 'send shipment for '.$context->orderId.PHP_EOL;
        }
    );

    $sm = new Rokde\StateMachine\StateMachine($registry);

    $canApply = $sm->canApply(OrderState::Shipped, OrderEvent::Pay, $context);
    expect($canApply)->toBeFalse();

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('There is no transition registered for the given state and event.');
    $sm->apply(OrderState::Shipped, OrderEvent::Pay, $context);
});
