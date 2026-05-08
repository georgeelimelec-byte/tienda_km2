<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Inventory
use Modules\Inventory\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Inventory\Repositories\Eloquent\ProductRepository;

/**
 * Registra los bindings de Repository Interfaces → Implementaciones Eloquent.
 *
 * Si en el futuro se migra de MySQL a otro motor de BD,
 * solo se cambian las implementaciones aquí sin tocar Services ni Controllers.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        ProductRepositoryInterface::class => ProductRepository::class,
    ];
}
