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
    public function testAddAttribte()
    {
        // TODO
        $this->assertFalse(true, "Must implement!!!");
        return;

        $attrs = array(
            'mail' => array('teszt@teszt.hu')
        );

        $expectedData = array(
            'IDPEmail' => array('teszt@teszt.hu')
        );

        $request = array(
            'Attributes' => $attrs,
            'Destination' => array(
                'entityid' => 'https://tesztsp.hu/shibboleth'
            )
        );

        // Test with entity ID that does NOT match the Source
        $config = array(
            'localAttributeConfig' => array(
                array(
                    'sps' => array(
                        'https://tesztsp.hu/shibboleth'
                        ),
                    'config' => array(
                        'attrfrom' => 'mail',
                        'attrto' => 'IDPEmail'
                    )
                )
            )
        );
        $result = self::processFilter($config, $request);

        $attributes = $result['Attributes'];
        $this->assertEquals($attributes['IDPEmail'], $expectedData['IDPEmail'], "OK");
    }
}
