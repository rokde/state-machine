<?php

namespace Tests\Orders;

enum OrderEvent: string
{
    case Pay = 'pay';
    case Ship = 'ship';
    case Cancel = 'cancel';
}
