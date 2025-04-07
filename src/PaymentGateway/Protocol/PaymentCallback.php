<?php

namespace App\PaymentGateway\Protocol;

readonly class PaymentCallback
{
    public function __construct(
        public string $currency,
        public int $amount,
        public int $externalId,
        public int $statusId,
        public string $customerPurse,
        public string $bankCountry,
        public string $processorMessage,
        public string $signature,
    ) {
    }

    public function toArray(): array
    {
        return [
            'currency' => $this->currency,
            'amount' => $this->amount,
            'external_id' => $this->externalId,
            'status_id' => $this->statusId,
            'customer_purse' => $this->customerPurse,
            'bank_country' => $this->bankCountry,
            'processor_message' => $this->processorMessage,
            'signature' => $this->signature,
        ];
    }

    public static function ofArray(array $data): static
    {
        assert(is_string($data['currency']));
        assert(is_int($data['amount']));
        assert(is_int($data['external_id']));
        assert(is_int($data['status_id']));
        assert(is_string($data['customer_purse']));
        assert(is_string($data['bank_country']));
        assert(is_string($data['processor_message']));
        assert(is_string($data['signature']));

        return new static(
            $data['currency'],
            $data['amount'],
            $data['external_id'],
            $data['status_id'],
            $data['customer_purse'],
            $data['bank_country'],
            $data['processor_message'],
            $data['signature'],
        );
    }
}
