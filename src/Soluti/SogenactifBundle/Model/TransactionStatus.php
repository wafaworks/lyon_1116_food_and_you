<?php

namespace Soluti\SogenactifBundle\Model;

class TransactionStatus
{
    const STATUS_ACCEPTED = '00';
    const STATUS_REQUEST_PHONE = '02';
    const STATUS_MERCHANT_INVALID = '03';
    const STATUS_REFUSED = '05';
    const STATUS_INVALID_TRANSACTION = '12';
    const STATUS_CANCELLED = '17';
    const STATUS_FORMAT_ERROR = '30';
    const STATUS_SUSPICION_FRAUD = '34';
    const STATUS_RETRY_LIMIT = '75';
    const STATUS_UNAVAILABLE = '90';
}
