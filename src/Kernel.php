<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Classe Kernel principale de l'application Symfony.
 *
 * Elle utilise le trait `MicroKernelTrait` pour gérer une configuration simplifiée et
 * fournit le point d'entrée principal pour l'exécution de l'application.
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
