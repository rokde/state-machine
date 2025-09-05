<?php

declare(strict_types=1);

namespace Tests\Articles;

enum ArticleEvent
{
    case Publish;
    case Archive;
}
