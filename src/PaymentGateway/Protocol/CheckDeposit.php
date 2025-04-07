<?php

namespace App\PaymentGateway\Protocol;

readonly class CheckDeposit
{
    public function __construct(
        public string $name,
        public int $siteLogin,
        public int $amount,
        public string $currency,
        public ?int $paymentGroupId = null,
        public ?int $displayPaymentGroupId = null,
        public ?int $paymentTypeId = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'site_login' => $this->siteLogin,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'payment_group_id' => $this->paymentGroupId,
            'display_payment_group_id' => $this->displayPaymentGroupId,
            'payment_type_id' => $this->paymentTypeId,
        ];
    }

    public static function ofArray(array $data): static
    {
        assert(is_string($data['name']));
        assert(is_int($data['site_login']));
        assert(is_int($data['amount']));
        assert(is_string($data['currency']));
        assert(!isset($data['payment_group_id']) || is_int($data['payment_group_id']));
        assert(!isset($data['display_payment_group_id']) || is_int($data['display_payment_group_id']));
        assert(!isset($data['payment_type_id']) || is_int($data['payment_type_id']));

        return new static(
            $data['name'],
            $data['site_login'],
            $data['amount'],
            $data['currency'],
            $data['payment_group_id'] ?? null,
            $data['display_payment_group_id'] ?? null,
            $data['payment_type_id'] ?? null,
        );
    }
}