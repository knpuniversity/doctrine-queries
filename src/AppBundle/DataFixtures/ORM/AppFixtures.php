<?php

namespace AppBundle\DataFixtures\ORM;

use Hautelook\AliceBundle\Alice\DataFixtureLoader;
use Nelmio\Alice\Fixtures;

class AppFixtures extends DataFixtureLoader
{
    /**
     * {@inheritDoc}
     */
    protected function getFixtures()
    {
        return  array(
            __DIR__ . '/fixtures.yml',
        );
    }

    public function randomFortune($category)
    {
        $possibilities = $this->fortunes[$category];
        $key = array_rand($possibilities);

        return $possibilities[$key];
    }

    private $fortunes = [
        'job' => [
            'FOO'
        ],
        'lunch' => [
            'BAR'
        ],
        'proverb' => [
            'BOB'
        ],
        'pets' => [
            'PET'
        ],
        'love' => [
            'LOVE'
        ],
        'lucky_number' => [
            42
        ]
    ];
}
