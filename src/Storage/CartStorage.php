<?php

namespace App\Storage;

use App\Entity\Room;

class CartStorage
{
    public function __construct(private SessionStorage $storage)
    {
    }

    public function get(): ?array
    {
        return $this->storage->get($this->provideKey());
    }

    public function add(Room $room): void
    {
        $this->storage->set($this->provideKey(), ['_room_id' => $room->getId()]);
    }

    public function init(): void
    {
        $this->storage->remove($this->provideKey());
    }

    private function provideKey(): string
    {
        return '_app_card';
    }
}
