VZ Address
==========

An address fieldtype for Expression Engine 2. Also works as a Matrix cell-type or a Low Variable.

Template Tags
-------------

### Single Tags ###

    {address_field [style="microformat|schema|rdfa|plain|inline"]}

Will output the complete address, in a standard format. Use the `style=""` attribute to get code that supports one of the markup standards for parseable content (defaults to microformats) or to get the address with no html markup at all (plain or inline).

    {address_field:street}
    {address_field:street_2}
    {address_field:city}
    {address_field:region}
    {address_field:postal_code}
    {address_field:country [code="yes"]}

Output particular pieces of the address. If you use the parameter `code="yes"` on the country tag, you will get the the international country code rather than the full name.
    
    {address_field:map_url [source="google|yahoo|bing|mapquest"] [params=""]}

Output a URL to the address on any one of a variety of mapping services. Specify which service you want to use with the `source` parameter (Google Maps is the default). Anything you put in the `params` parameter will be added to the end of the map URL, use it to specify zoom levels, map types, etc.
    
    {address_field:static_map}

Returns the URL to an static map image of the address. Currently, Google Maps' API is used, although I plan to add additional services in the future. There are a number of parameters you can use to modify the map display:

* `width` and `height` - The size in pixels of the image that is generated. (default: 400 x 200)
* `zoom` - Zoom level of the map. (default: 14)
* `type` - One of: roadmap, satellite, hybrid, or terrain. (default: roadmap)
* `marker:size` - The relative size of the pushpin that marks the address location. One of: normal, mid, small, or tiny. (default: normal)
* `marker:color` - The color of the pushpin. Either a named color (black, brown, green, purple, yellow, blue, gray, orange, red, or white) or a 6-digit hex-code, like "#ff0000". Three-digit color codes are not supported. (default: red)
* `marker:label` - Instead of the normal dot, you can specify a single letter or number to appear on the pushpin. (default: none)

### Tag Pair ###

    {address_field}
        {street}
        {street_2}
        {city}, {region} {postal_code}
        {country}
		{lat}
		{lng}
    {/address_field}

If you need more control over the output, use the tag pair to output each part of the address individually.

### Low Variables Support ###

Note that you *must* use the `{exp:low_variables:parse}` syntax to parse VZ Address variables created with Low Variables.

For single tags:

    {exp:low_variables:parse var="address_field_name" [style="microformat|schema|rdfa|plain|inline"]}
    
For tag pairs:

    {exp:low_variables:parse var="address_field_name" multiple="yes"}
        {street} {!-- note lack of address_field_name: prefix! --}
        {street_2}
        {city}, {region}, {postal_code}
        {country}
    {/exp:low_variables:parse}

Installation
------------

Download and unzip the archive. Upload the `vz_address` folder to /system/expressionengine/third_party/.

Thanks
------

Initial Low Variables support was added by <a href="https://twitter.com/adrienneleigh">@adrienneleigh</a>.