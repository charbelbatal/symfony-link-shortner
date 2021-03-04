<?php

namespace App\Service;

use App\Repository\LinkRepository;
use App\Utils\RandomStringGenerator;

class UrlShortnerService
{
    private const MIN_LENGTH = 5;
    private const MAX_LENGTH = 9;

    /**
     * @var LinkRepository
     */
    private $linkRepository;
    /**
     * @var RandomStringGenerator
     */
    private $randomStringGenerator;

    /**
     * UrlShortnerService constructor.
     * @param LinkRepository $linkRepository
     */
    public function __construct(LinkRepository $linkRepository)
    {
        $this->linkRepository = $linkRepository;
        $this->randomStringGenerator = new RandomStringGenerator();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function generateShortCode()
    {
        $length = $this->getLengthToUse();

        if($length) {
            do {
                $shortCode = $this->randomStringGenerator->generate($length);

                //Check if code is already used
                $shortCodeExists = $this->linkRepository->findOneBy([
                    'shortCode' => $shortCode,
                ]);

            } while ($shortCodeExists);

            return $shortCode;
        }

        throw new \Exception('Could not find a Short Code');
    }

    /**
     * Get The Length to Use between the MIN AND MAX Values defined
     * For each length of character between MIN AND MAX we need to check if the number of shorten urls in the db exceeds the total Number allowed for length
     * @return int|null
     */
    public function getLengthToUse(){
        //Get Total Number of Links in the DB
        $totalNumberOfLinks = $this->linkRepository->count([]);
        //Get Total Number of Characters used for string generation
        $totalNumberOfCharacters = $this->randomStringGenerator->getTotalNumberOfCharacters();

        $totalAllowed = 0;
        for($n = self::MIN_LENGTH;$n <= self::MAX_LENGTH; $n++){
            $totalAllowed += pow($totalNumberOfCharacters,$n);

            if($totalNumberOfLinks < $totalAllowed){
                return $n;
            }
        }

        return null;
    }
}