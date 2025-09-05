<?php

declare(strict_types=1);

namespace Tests\Articles;

enum ArticleState
{
    case New;
    case Published;
    case Archived;
}
