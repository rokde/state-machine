<?php

declare(strict_types=1);

use Rokde\StateMachine\Transformers\MermaidDirection;
use Rokde\StateMachine\Transformers\RegistryMermaidTransformer;
use Rokde\StateMachine\TransitionRegistry;
use Tests\Orders\OrderContext;
use Tests\Orders\OrderEvent;
use Tests\Orders\OrderState;

it('can generate mermaid diagrams', function () {
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

    $transformer = new Rokde\StateMachine\Transformers\RegistryMermaidTransformer;

    $mermaid = $transformer->transform($registry);

    expect($mermaid)->toBe(<<<'MERMAID'
stateDiagram-v2
  new --> paid : pay
  new --> cancelled : cancel
  paid --> shipped : ship

MERMAID
    );
});

it('can generate mermaid diagrams with display direction configured', function () {
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

    $transformer = new Rokde\StateMachine\Transformers\RegistryMermaidTransformer(MermaidDirection::LEFT);

    $mermaid = $transformer->transform($registry);

    expect($mermaid)->toBe(<<<'MERMAID'
stateDiagram-v2
  direction LR
  new --> paid : pay
  new --> cancelled : cancel
  paid --> shipped : ship

MERMAID
    );
});

it('can generate the README example article lifecycle', function () {
    enum ArticleState
    {
        case Draft;
        case InReview;
        case Scheduled;
        case Published;
        case Archived;
    }
    // ArticleEvent should be an Enum/BackedEnum or string constants
    enum ArticleEvent
    {
        case Submit;
        case Approve;
        case Publish;
        case Update;
        case Archive;
    }

    $articleRegistry = new \Rokde\StateMachine\TransitionRegistry;
    $articleRegistry->addTransition(
        ArticleState::Draft, ArticleEvent::Submit, ArticleState::InReview,
    )->addTransition(
        ArticleState::InReview, ArticleEvent::Approve, ArticleState::Scheduled,
    )->addTransition(
        ArticleState::Scheduled, ArticleEvent::Publish, ArticleState::Published,
    )->addTransition(
        ArticleState::Draft, ArticleEvent::Update, ArticleState::Draft,
    )->addTransition(
        ArticleState::InReview, ArticleEvent::Update, ArticleState::Draft,
    )->addTransition(
        ArticleState::Published, ArticleEvent::Archive, ArticleState::Archived,
    );

    $transformer = new RegistryMermaidTransformer;
    $mermaid = $transformer->transform($articleRegistry);

    expect($mermaid)->toBe(<<<'MERMAID'
stateDiagram-v2
  Draft --> InReview : Submit
  Draft --> Draft : Update
  InReview --> Scheduled : Approve
  InReview --> Draft : Update
  Scheduled --> Published : Publish
  Published --> Archived : Archive

MERMAID
    );
});
