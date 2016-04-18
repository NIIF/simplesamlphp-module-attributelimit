# README
[![Build Status](https://travis-ci.org/NIIF/simplesamlphp-module-attributelimit.svg?branch=master)](https://travis-ci.org/NIIF/simplesamlphp-module-attributelimit)
[![Total Downloads](https://poser.pugx.org/niif/simplesamlphp-module-attributelimit/d/total.png)](https://packagist.org/packages/niif/simplesamlphp-module-attributelimit)
[![Latest Stable Version](https://poser.pugx.org/niif/simplesamlphp-module-attributelimit/v/stable.png)](https://packagist.org/packages/niif/simplesamlphp-module-attributelimit)

# Install
`composer require niif/simplesamlphp-module-attributelimit`

# Usage
TODO

```
$config = array(
    'class' => 'niif:AttributeLimit',
    'allowedAttributes' => array(),
    'bilateralSPs' => array(
        'entityid1' => array(
            'attr1',
            'attr2'
        ),
        'entityid2' => array(
            'attr1',
            'attr2'
        ),
    ),
    'bilateralAttributes' => array(
        'attr1' => array(
            'entityid1',
            'entityid2'
        ),
        'attr2' => array(
            'entityid1',
            'entityid2'
        ),
    )
)
```