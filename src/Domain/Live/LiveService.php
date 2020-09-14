<?php

namespace App\Domain\Live;

use App\Core\OptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class LiveService
{
    const OPTION_KEY = 'live_id';
    private OptionInterface $option;
    private SerializerInterface $serializer;

    public function __construct(
        OptionInterface $option,
        SerializerInterface $serializer
    ) {
        $this->option = $option;
        $this->serializer = $serializer;
    }

    public function getCurrentLive(): ?Live
    {
        $option = $this->option->get(self::OPTION_KEY);

        return null === $option ? null : $this->serializer->deserialize($option, Live::class, 'json');
    }
}
