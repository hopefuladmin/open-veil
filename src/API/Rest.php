<?php

declare(strict_types=1);

namespace OpenVeil\API;

/**
 * REST API
 * 
 * Manages REST API endpoints for Protocol and Trial post types.
 * Acts as a factory for versioned API implementations.
 * 
 * @package OpenVeil\API
 */
class Rest
{
    /**
     * Sets up versioned API implementations.
     */
    public function __construct()
    {
        new V1\Api();
    }
}
