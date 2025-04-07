<?php

namespace App\PaymentGateway\Protocol;

readonly class PaymentPayloadRequest
{
    public function __construct(
        public int $siteId,
        public string $siteLogin,
        public string $currency,
        public int $forceAction,
    ) {
    }

    public function toArray(): array
    {
        return [
            'site_id' => $this->siteId,
            'site_login' => $this->siteLogin,
            'currency' => $this->currency,
            'force_action' => $this->forceAction,
        ];
    }

    public static function ofArray(array $data): static
    {
        assert(is_int($data['site_id']));
        assert(is_string($data['site_login']));
        assert(is_string($data['currency']));
        assert(is_int($data['force_action']));

        return new static(
            $data['site_id'],
            $data['site_login'],
            $data['currency'],
            $data['force_action'],
        );
    }
}
