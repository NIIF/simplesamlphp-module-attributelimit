# README
[![Build Status](https://travis-ci.org/NIIF/simplesamlphp-module-attributelimit.svg?branch=master)](https://travis-ci.org/NIIF/simplesamlphp-module-attributelimit)
[![Total Downloads](https://poser.pugx.org/niif/simplesamlphp-module-attributelimit/d/total.png)](https://packagist.org/packages/niif/simplesamlphp-module-attributelimit)
[![Latest Stable Version](https://poser.pugx.org/niif/simplesamlphp-module-attributelimit/v/stable.png)](https://packagist.org/packages/niif/simplesamlphp-module-attributelimit)

# Install
`composer require niif/simplesamlphp-module-attributelimit`

# Usage
This module is a forked version of SimpleSAMLphp ```core:AttributeLimit```. It adds the functionality of specifying bilateral attribute relations in addition to the attribute rules defined in the peer's metadata. You can both specify SP entityIDs where you send some additional attributes (_bilateralSPs_) and special attributes to send to some SPs (_bilateralAttributes_).

Example configuration:

```
$config = array(
    'class' => 'niif:AttributeLimit',
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
    ),
    'attribute_x', 'attribute_y',
)
```
