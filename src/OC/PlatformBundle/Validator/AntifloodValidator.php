<?php 

namespace OC\PlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use SYmfony\Component\HttpFoundation\RequestStack;

class AntifloodValidator extends ConstraintValidator
{
    private $requestStack;
    private $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        $request = $this->requestStack->getCurrentRequest();

        $ip = $request->getClientIp();

        $currentDate = new \DateTime();
        
        $advert = $this->em
        ->getRepository('OCPlatformBundle:Advert')
        ->findBy(
            ['ip' => $ip],
            ['date' => 'desc'],
            1   
        );

        if ($advert === null) {
            return;
        }
            
        //  date and time of creation of the last advert with the same ip as current ip
        $dateSave = $advert[0]->getDate();

        //  date and time to accept new advert from same ip
        $dateMin = $dateSave->add(new \DateInterval('PT1M'));
            
        if ($currentDate <= $dateMin) {
            $this->context->addViolation($constraint->message);
        }
    }
}
