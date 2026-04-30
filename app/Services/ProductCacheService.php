<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;

class ProductCacheService
{
    private const TAG = 'products';

    private const LIST_TTL = 300;

    public function rememberList(array $params, Closure $callback): array
    {
        return Cache::tags([self::TAG])->remember(
            $this->listKey($params),
            self::LIST_TTL,
            $callback,
        );
    }

    public function forgetList(): void
    {
        Cache::tags([self::TAG])->flush();
    }

    private function listKey(array $params): string
    {
        ksort($params);

        return 'products:list:'.md5(http_build_query($params));
    }
}
