<?php

namespace OC\PlatformBundle\Beta;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener{
    protected $betaHtml;
    protected $endDate;

    public function __construct(BetaHtmlAdder $betaHtml, $endDate)
    {
        $this->betaHtml = $betaHtml;
        $this->endDate = new \Datetime($endDate);
    }

    public function processBeta(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $remainingDays = $this->endDate->diff(new \Datetime('today'))->days;

        if ($remainingDays <= 0) {
            return;
        } 

        $response = $this->betaHtml->addBeta($event->getresponse(), $remainingDays);

        $event->setResponse($response);
    }
}