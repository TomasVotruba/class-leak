<?php

namespace ClassLeak202411\Illuminate\Contracts\Broadcasting;

interface HasBroadcastChannel
{
    /**
     * Get the broadcast channel route definition that is associated with the given entity.
     *
     * @return string
     */
    public function broadcastChannelRoute();
    /**
     * Get the broadcast channel name that is associated with the given entity.
     *
     * @return string
     */
    public function broadcastChannel();
}
