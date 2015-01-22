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

    // Many fortunes provided by: http://www.huffingtonpost.com/2012/11/10/29-fortune-cookies-that-will-surprise-you_n_2109491.html

    private $fortunes = [
        'job' => [
            'It would be best to maintain a low profile for now.',
            '404 Fortune not found. Abort, Retry, Ignore?',
            'You laugh now, wait til you get home.',
            'If your work is not finished, blame it on the computer.',
        ],
        'lunch' => [
            'You will be hungry again in one hour.',
            'Vampires will soon strike you if you do not order again',
            'A nice cake is waiting for you',
            'Warning: Do not eat your fortune',
        ],
        'proverb' => [
            'A conclusion is simply the place where you got tired of thinking.',
            'Cookie said: "You really crack me up"',
            'When you squeeze an orange, orange juice comes out. Because that\'s what\'s inside.',
        ],
        'pets' => [
            'There\'s no such thing as an ordinary cat',
            'That wasn\'t chicken',
        ],
        'love' => [
            'An alien of some sort will be appearing to you shortly!',
            'Are your legs tired? You\'ve been running through someone\'s mind all day long.',
            'run',
        ],
        'lucky_number' => [
            42,
            12,
            '10^2',
            'Jar Jar Binks',
            'Pi',
        ]
    ];
}
