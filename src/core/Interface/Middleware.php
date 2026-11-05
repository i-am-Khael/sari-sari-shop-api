<?php

declare(strict_types=1);

namespace Core\Interface;

interface Middleware
{

  public function handle(): bool;

}
