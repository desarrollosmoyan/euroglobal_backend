<?php

namespace Domain\Orders\DataTransferObjects;

use Support\DataTransferObjects\Response;

class OrderSummarizedProductionReportPDFResponse extends Response
{
    /**
     * @return array
     */
    public function getData(): array
    {
        return parent::getData();
    }

}
