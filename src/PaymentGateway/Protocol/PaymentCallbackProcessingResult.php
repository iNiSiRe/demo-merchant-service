<?php

namespace App\PaymentGateway\Protocol;

readonly class PaymentCallbackProcessingResult
{
    public function __construct(
        public int $code,
    ) {
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
        ];
    }

    public static function ofArray(array $data): static
    {
        assert(is_int($data['code']));

        return new static($data['code']);
    }
}
