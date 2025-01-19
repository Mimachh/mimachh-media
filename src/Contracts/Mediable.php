<?php

declare(strict_types=1);

namespace Mimachh\Media\Contracts;

interface Mediable
{
    // il me faut ici une fonction que le model qui a le trait devra avoir
    // dans cette fonction il y aura une liste de nom -> valeur 
    // les noms correspondront à un nom de conversion de media 
    // et la valeur sera la width (la height sera en auto ?)

    /**
     * Get media conversions.
     *
     * @return array An associative array where the key is the conversion name and the value is the width.
     */
    public function getMediaConversions(): array;
}
