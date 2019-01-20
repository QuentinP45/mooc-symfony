<?php

namespace OC\PlatformBundle\Beta;

use Symfony\Component\HttpFoundation\Response;


class BetaListener{
    protected $betaHtml;
    protected $endDate;

    public function __construct(BetaHtmlAdder $betaHtml, $endDate)
    {
        $this->betaHtml = $betaHtml;
        $this->endDate = new \Datetime($endDate);
    }

    public function processBeta()
    {
        $remainingDays = $this->endDate->diff(new \Datetime())->days;

        if ($remainingDays <= 0) {
            return;
        } 

        // method $this->betaHtml->addBeta()
    }
}