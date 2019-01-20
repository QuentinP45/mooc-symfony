<?php

namespace OC\PlatformBundle\Beta;

use Symfony\Component\HttpFoundation\Response;

class BetaListener{
    protected $betaHtml;
    protected $enDate;

    public function __construct(BetaHtmlAdder $betaHtml, $endDate)
    {
        $this->betaHtml = $betaHTML;
        $this->endDate = new \Datetime($endDate);
    }

    public function processBeta()
    {
        $remainingDays = $this->enDate->diff(new \Datetime())->days;

        if ($remainingDays <= 0) {
            return;
        } 

        // method $this->betaHtml->addBeta()
    }
}