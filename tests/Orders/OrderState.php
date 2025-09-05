<?php

namespace Tests\Orders;

enum OrderState: string
{
    case New = 'new';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';
}
