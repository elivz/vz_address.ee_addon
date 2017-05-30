VZ Address
==========

A simple address fieldtype for Expression Engine. Also supports Grid, Matrix, and Low Variables.

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
    {address_field:country}
    {address_field:country_code}

Output particular pieces of the address.

    {address_field:map_url [source="google|yahoo|bing|mapquest"] [include_name="yes"] [params=""]}

Output a URL to the address on any one of a variety of mapping services. Specify which service you want to use with the `source` parameter (Google Maps is the default). Setting `include_name="yes"` will cause the "name" field to be included in the URL. This may give better or worse results, depending on whether the name is included in the mapping service's database. Anything you put in the `params` parameter will be added to the end of the map URL, use it to specify zoom levels, map types, etc.

    {address_field:static_map}

Returns the URL to an static map image of the address. Currently, Google Maps' API is used, although I hope to add additional services in the future. There are a number of parameters you can use to modify the map display:

* `width` and `height` - The size in pixels of the image that is generated. (default: 400 x 200)
* `scale` - Number of pixels returned. Set this to `2` for retina/hidpi support. (default: 1)
* `zoom` - Zoom level of the map. (default: 14)
* `format` - Specifies the image format to return. One of: png, png32, jpg, jpg-baseline, or gif. (default: png)
* `type` - One of: roadmap, satellite, hybrid, or terrain. (default: roadmap)
* `marker:size` - The relative size of the pushpin that marks the address location. One of: normal, mid, small, or tiny. (default: normal)
* `marker:color` - The color of the pushpin. Either a named color (black, brown, green, purple, yellow, blue, gray, orange, red, or white) or a 6-digit hex-code, like "#ff0000". Three-digit color codes are not supported. (default: red)
* `marker:label` - Instead of the normal dot, you can specify a single letter or number to appear on the pushpin. (default: none)

    {address_field:static_map_tag}

Accepts all the same parameters as the `static_map` tag, but instead of just returning the map URL, it outputs a full image tag, with the address itself as the alt text.

    {address_field:is_empty} / {address_field:is_not_empty}

Since the Address field contains complex data, you can use these tags in conditional to determine if the field has been filled out or not. Ex. `{if "{address_field:is_not_empty}"}Address: {address_field}{/if}`

### Tag Pair ###

    {address_field}
        {street}
        {street_2}
        {city}, {region} {postal_code}
        {country} ({country_code})
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

Download and unzip the archive. 
For ee2, upload the `vz_address` folder to /system/expressionengine/third_party/.
For ee3, upload it instead to /system/user/addons/ and install it in the Add-on Manager.

Requirements
------------

VZ Address requires PHP >= 5.2.

Thanks
------

Initial Low Variables support was added by <a href="https://twitter.com/adrienneleigh">@adrienneleigh</a>.