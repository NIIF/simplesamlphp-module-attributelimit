<?php

class Test_sspmod_niif_Auth_Process_AttributeLimit extends PHPUnit_Framework_TestCase
{

    /**
     * Helper function to run the filter with a given configuration.
     *
     * @param  array $config The filter configuration.
     * @param  array $request The request state.
     * @return array  The state array after processing.
     */
    private static function processFilter(array $config, array $request)
    {
        $filter = new sspmod_niif_Auth_Process_AttributeLimit($config, null);
        $filter->process($request);
        return $request;
    }

    /**
     * Test releasing attribute
     */
    public function testBilateralSPs()
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

    public function testBilateralAttributes()
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

}
