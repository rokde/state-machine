<?php

declare(strict_types=1);

namespace Tests\Orders;

use DateTimeImmutable;

final readonly class OrderContext
{
    public function __construct(
        public int $orderId,
        public int $amountCents,
        public int $authorizedCents,
        public DateTimeImmutable $now = new DateTimeImmutable,
    ) {}
}
