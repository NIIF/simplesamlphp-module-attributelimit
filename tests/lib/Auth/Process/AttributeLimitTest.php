<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\attributelimit\Auth\Process;

use Exception;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\attributelimit\Auth\Process\AttributeLimit;

class AttributeLimitTest extends TestCase
{

    /**
     * Helper function to run the filter with a given configuration.
     *
     * @param  array $config The filter configuration.
     * @param  array $request The request state.
     * @return array  The state array after processing.
     */
    private static function processFilter(array $config, array $request): array
    {
        $filter = new AttributeLimit($config, null);
        $filter->process($request);
        return $request;
    }

    /**
     * Test releasing attribute
     */
    public function testBilateralSPs(): void
    {

        $expectedData = array(
            'Attributes' => array(
                'mail' => array('bob@institutionalmail.org'),
                'notification-mail' => array('bob@gmail.com')
            ),        );

        $request = array(
            'Attributes' => array(
                'mail' => array('bob@institutionalmail.org'),
                'notification-mail' => array('bob@gmail.com')
            ),
            'Destination' => array(
                'entityid' => 'https://tesztsp.hu/shibboleth',
                'attributes' => array('mail')
            ),
            'Source' => array()
        );

        $config = array(
            'bilateralSPs' => array(
                'https://tesztsp.hu/shibboleth' => array(
                    'notification-mail'
                )
            )
        );

        $result = self::processFilter($config, $request);
        $this->assertEquals($result['Attributes'], $expectedData['Attributes'], "OK");
    }

    public function testBilateralAttributes(): void
    {

        $expectedData = array(
            'Attributes' => array(
                'mail' => array('bob@institutionalmail.org'),
                'notification-mail' => array('bob@gmail.com')
            ),        );

        $request = array(
            'Attributes' => array(
                'mail' => array('bob@institutionalmail.org'),
                'notification-mail' => array('bob@gmail.com')
            ),
            'Destination' => array(
                'entityid' => 'https://tesztsp.hu/shibboleth',
                'attributes' => array('mail')
            ),
            'Source' => array()
        );

        $config = array(
            'bilateralAttributes' => array(
                'notification-mail' => array(
                    'https://tesztsp.hu/shibboleth'
                )
            )
        );

        $result = self::processFilter($config, $request);
        $this->assertEquals($result['Attributes'], $expectedData['Attributes'], "OK");
    }

    public function testInvaildConfigs(): void
    {
        $this->expectException(Exception::class);
        $request = array(
            'Attributes' => array(
                'mail' => array('bob@institutionalmail.org'),
                'notification-mail' => array('bob@gmail.com')
            ),
            'Destination' => array(
                'entityid' => 'https://tesztsp.hu/shibboleth',
                'attributes' => array('mail')
            ),
            'Source' => array()
        );

        $config = array(
            'bilateralAttributes' => array(
                'notification-mail' => 'https://tesztsp.hu/shibboleth'
            ),
            'bilateralSPs' => array(
                'https://tesztsp.hu/shibboleth' => 'notification-mail'
            ),
            'invalidConfigKey' => array()
        );
        $result = self::processFilter($config, $request);
    }

    /**
     * Inherited tests from core:AttributeLimit
     */

    /**
     * Test reading IdP Attributes.
     */
    public function testIdPAttrs(): void
    {
        $config = array(
            'cn', 'mail'
        );

        $request = array(
            'Attributes' => array(
                 'eduPersonTargetedID' => array('eptid@example.org'),
                 'eduPersonAffiliation' => array('member'),
                 'cn' => array('user name'),
                 'mail' => array('user@example.org'),
             ),
            'Destination' => array(
             ),
            'Source' => array(
                'attributes' => array('cn','mail'),
             ),
        );

        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
        $this->assertArrayNotHasKey('eduPersonTargetedID', $attributes);
        $this->assertArrayNotHasKey('eduPersonAffiliation', $attributes);
        $this->assertCount(2, $attributes);

        $config = array(
            'cn',
            'default' => TRUE,
        );

        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
        $this->assertArrayNotHasKey('eduPersonTargetedID', $attributes);
        $this->assertArrayNotHasKey('eduPersonAffiliation', $attributes);
        $this->assertCount(2, $attributes);


    }

    /**
     * Tests when no attributes are in metadata.
     */
    public function testNULLMetadataAttrs(): void
    {
        $config = array(
            'cn', 'mail'
        );

        $request = array(
            'Attributes' => array(
                 'eduPersonTargetedID' => array('eptid@example.org'),
                 'eduPersonAffiliation' => array('member'),
                 'cn' => array('user name'),
                 'mail' => array('user@example.org'),
             ),
            'Destination' => array(
             ),
            'Source' => array(
             ),
        );

        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
        $this->assertArrayNotHasKey('eduPersonTargetedID', $attributes);
        $this->assertArrayNotHasKey('eduPersonAffiliation', $attributes);
        $this->assertCount(2, $attributes);

        $config = array(
            'cn',
            'default' => TRUE,
        );

        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayNotHasKey('mail', $attributes);
        $this->assertArrayNotHasKey('eduPersonTargetedID', $attributes);
        $this->assertArrayNotHasKey('eduPersonAffiliation', $attributes);
        $this->assertCount(1, $attributes);

        $config = array(
        );

        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertCount(4, $attributes);
        $this->assertArrayHasKey('eduPersonTargetedID', $attributes);
        $this->assertArrayHasKey('eduPersonAffiliation', $attributes);
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
    }

    /**
     * setUpBeforeClass a request that will be used for the following tests.
     * note the above tests don't use self::$request for processFilter input.
     */
    protected static $request;

    public static function setUpBeforeClass(): void
    {
        self::$request = array(
            'Attributes' => array(
                 'eduPersonTargetedID' => array('eptid@example.org'),
                 'eduPersonAffiliation' => array('member'),
                 'cn' => array('common name'),
                 'mail' => array('user@example.org'),
             ),
            'Destination' => array(
        'attributes' => array('cn','mail'),
             ),
            'Source' => array(
             ),
        );
    }

    /**
     * Test the most basic functionality.
     */
    public function testBasic(): void
    {
        $config = array(
            'cn', 'mail'
        );

        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
        $this->assertCount(2, $attributes);
    }

    /**
     * Test defaults with metadata available.
     */
    public function testDefaultWithMetadata(): void
    {
        $config = array(
            'default' => TRUE,
        );

        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
        $this->assertCount(2, $attributes);
    }

    /**
     * Test defaults with attributes and metadata
     */
    public function testDefaultWithAttrs(): void
    {
        $config = array(
            'default' => TRUE,
            'eduPersonTargetedID', 'eduPersonAffiliation',
        );

        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(2, $attributes);
        $this->assertArrayHasKey('cn', $attributes);
        $this->assertArrayHasKey('mail', $attributes);
        $this->assertArrayNotHasKey('eduPersonTargetedID', $attributes);
        $this->assertArrayNotHasKey('eduPersonAffiliation', $attributes);
    }

    /**
     * Test for exception with illegal config.
     *
     * @expectedException Exception
     */
    public function testInvalidConfig()
    {
        $this->expectException(Exception::class);
        $config = array(
            'invalidArg' => TRUE,
        );

        $result = self::processFilter($config, self::$request);
    }

    /**
     * Test for invalid attribute name
     *
     * @expectedException Exception
     */
    public function testInvalidAttributeName(): void
    {
        $this->expectException(Exception::class);
        $config = array(
        null
        );

        $result = self::processFilter($config, self::$request);
    }


    /**
     * Test for attribute value matching
     */
    public function testMatchAttributeValues(): void
    {
        $config = array(
        'eduPersonAffiliation' => array('member')
        );

        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(1, $attributes);
        $this->assertArrayHasKey('eduPersonAffiliation', $attributes);
        $this->assertEquals($attributes['eduPersonAffiliation'], array('member'));

        $config = array(
        'eduPersonAffiliation' => array('member','staff')
        );

        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(1, $attributes);
        $this->assertArrayHasKey('eduPersonAffiliation', $attributes);
        $this->assertEquals($attributes['eduPersonAffiliation'], array('member'));

        $config = array(
        'eduPersonAffiliation' => array('student')
        );
        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(0, $attributes);

        $config = array(
        'eduPersonAffiliation' => array('student','staff')
        );
        $result = self::processFilter($config, self::$request);
        $attributes = $result['Attributes'];
        $this->assertCount(0, $attributes);
    }

    /**
     * Test for allowed attributes not an array.
     *
     * This test is very unlikely and would require malformed metadata processing.
     * Cannot be generated via config options.
     *
     * @expectedException Exception
     */
    public function testMatchAttributeValuesNotArray(): void
    {
        $this->expectException(Exception::class);
        $config = array(
        );

        $request = array(
            'Attributes' => array(
                 'eduPersonTargetedID' => array('eptid@example.org'),
                 'eduPersonAffiliation' => array('member'),
                 'cn' => array('user name'),
                 'mail' => array('user@example.org'),
                 'discardme' => array('somethingiswrong'),
             ),
            'Destination' => array(
                'attributes' => array('eduPersonAffiliation' => 'student'),
             ),
            'Source' => array(
             ),
        );


        $result = self::processFilter($config, $request);
    }

    /**
     * Test attributes not intersecting
     */
    public function testNoIntersection(): void
    {
        $config = array(
            'default' => TRUE,
        );

        $request = array(
            'Attributes' => array(
                 'eduPersonTargetedID' => array('eptid@example.org'),
                 'eduPersonAffiliation' => array('member'),
                 'cn' => array('user name'),
                 'mail' => array('user@example.org'),
                 'discardme' => array('somethingiswrong'),
             ),
            'Destination' => array(
                'attributes' => array('urn:oid:1.2.840.113549.1.9.1'),
             ),
            'Source' => array(
             ),
        );

        $result = self::processFilter($config, $request);
        $attributes = $result['Attributes'];
        $this->assertCount(0, $attributes);
        $this->assertEmpty($attributes);
    }

}
