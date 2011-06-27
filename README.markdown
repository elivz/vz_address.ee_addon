VZ Address
==========

An address fieldtype for Expression Engine 2.

Template Tags
-------------

### Single Tags ###

    {address_field [markup="microformat|schema|rdfa"]}

Will output the complete address, in a standard format. Use the `markup=""` attribute for code that supports one of the markup standards for parseable content.

    {address_field:street}
    {address_field:street_2}
    {address_field:city}
    {address_field:region}
    {address_field:postal_code}
    {address_field:country}

Output particular pieces of the address.

### Tag Pair ###

    {address_field}
        {address}
        {address_2}
        {city}, {region} {postal_code}
        {country}
    {/address_field}

If you need more control over the output, use the tag pair to output each part of the address individually.

Installation
------------

Download and unzip the archive. Upload the `vz_address` folder to /system/expressionengine/third_party/.