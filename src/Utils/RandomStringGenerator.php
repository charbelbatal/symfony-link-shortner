<?php

namespace App\Utils;


class RandomStringGenerator
{
    private const CHARACTERS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    /**
     * @var int
     */
    private $totalNumberOfCharacters = 0;

    /**
     * RandomStringGenerator constructor.
     */
    public function __construct()
    {
        $this->totalNumberOfCharacters = strlen(self::CHARACTERS);
    }

    /**
     * @return int
     */
    public function getTotalNumberOfCharacters(): int
    {
        return $this->totalNumberOfCharacters;
    }

    public function generate(int $length)
    {
        $totalNumberOfCharacters = $this->getTotalNumberOfCharacters();

        $string = '';
        for ($i = 0; $i < $length; $i++) {
            //shuffle Characters to make it more random
            $shuffledCharacters = str_shuffle(self::CHARACTERS);

            //Get A Random Key to add more random to the character pic
            $randomKey = rand(0, $totalNumberOfCharacters-1);

            $string .= $shuffledCharacters[$randomKey];
        }

        return $string;
    }
}