<?php

namespace Demofony2\AppBundle\Tests\Front;

use Liip\FunctionalTestBundle\Test\WebTestCase as WebTestCase;

/**
 * Class UserControllerTest.
 *
 * @category Test
 *
 * @author   David Romaní <david@flux.cat>
 * @IgnoreAnnotation("dataProvider")
 */
class UserControllerTest extends WebTestCase
{
    /**
     * Test page is successful.
     *
     * @param array $url
     * @dataProvider provideUrls
     */
    public function testFrontendPagesAreSuccessful($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * Urls provider.
     *
     * @return array
     */
    public function provideUrls()
    {
        return array(
            array('/profile/user1/comments1/'),
            array('/profile/user1/proposals1/'),
        );
    }
}
