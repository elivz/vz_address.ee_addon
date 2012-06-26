<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VZ Address Class
 *
 * @author    Eli Van Zoeren <eli@elivz.com>
 * @copyright Copyright (c) 2011 Eli Van Zoeren
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */
 
class Vz_address_ft extends EE_Fieldtype {

    public $info = array(
        'name'      => 'VZ Address',
        'version'   => '1.2.0',
    );
	    
    public $has_array_data = TRUE;
    
    protected $country_codes = array("AF","AL","DZ","AS","AD","AO","AI","AQ","AG","AR","AM","AW","AU","AT","AZ","BS","BH","BD","BB","BY","BE","BZ","BJ","BM","BT","BO","BA","BW","BV","BR","IO","BN","BG","BF","BI","KH","CM","CA","CV","KY","CF","TD","CL","CN","CX","CC","CO","KM","CG","CD","CK","CR","CI","HR","CU","CY","CZ","DK","DJ","DM","DO","TP","EC","EG","SV","GQ","ER","EE","ET","FK","FO","FJ","FI","FR","FX","GF","PF","TF","GA","GM","GE","DE","GH","GI","GR","GL","GD","GP","GU","GT","GN","GW","GY","HT","HM","VA","HN","HK","HU","IS","IN","ID","IR","IQ","IE","IL","IT","JM","JP","JO","KZ","KE","KI","KP","KR","KW","KG","LA","LV","LB","LS","LR","LY","LI","LT","LU","MO","MK","MG","MW","MY","MV","ML","MT","MH","MQ","MR","MU","YT","MX","FM","MD","MC","MN","MS","MA","MZ","MM","NA","NR","NP","NL","AN","NC","NZ","NI","NE","NG","NU","NF","MP","NO","OM","PK","PW","PA","PG","PY","PE","PH","PN","PL","PT","PR","QA","RE","RO","RU","RW","KN","LC","VC","WS","SM","ST","SA","SN","SC","SL","SG","SK","SI","SB","SO","ZA","GS","ES","LK","SH","PM","SD","SR","SJ","SZ","SE","CH","SY","TW","TJ","TZ","TH","TG","TK","TO","TT","TN","TR","TM","TC","TV","UG","UA","AE","GB","US","UM","UY","UZ","VU","VE","VN","VG","VI","WF","EH","YE","YU","ZM","ZW");
    
    protected $fields = array(
        'street' => '',
        'street_2' => '',
        'city' => '',
        'region' => '',
        'postal_code' => '',
        'country' => 'US',
        'lat' => '',
        'lng' => ''
    );
  
	/**
	 * Fieldtype Constructor
	 */
	function Vz_address_ft()
	{
        parent::EE_Fieldtype();

        if (!isset($this->EE->session->cache['vz_address']))
        {
            $this->EE->session->cache['vz_address'] = array('css' => FALSE, 'countries' => array());
        }
        $this->cache =& $this->EE->session->cache['vz_address'];
		
        // Cache the array of country names
		$this->EE->lang->loadfile('vz_address');
        foreach ($this->country_codes as $country)
        {
            $this->cache['countries'][$country] = $this->EE->lang->line($country);
        }
	}
	
	/**
	 * Include the CSS styles, but only once
	 */
	private function _include_css_js()
	{
        if ( !$this->cache['css'] )
        {
            $this->EE->cp->add_to_head('<style type="text/css">
    .publish_vz_address { position:relative; }
    .publish_vz_address > div, .publish_vz_address > label { position:relative; z-index:5; }
    .publish_vz_address > .vz_address_map_container { position:absolute; z-index:0; left:0; top:0; width:100%; height:100%; opacity:0.3; }
    .vz_address { position:relative; z-index:5; padding-bottom: 0.5em; }
    .vz_address label { display:block; }
    .vz_address input, .vz_address select { width:99%; padding:4px; }
    .vz_address select { height:25px; font:normal 110% Arial, "Helvetica Neue", Helvetica, sans-serif; color:#5F6C74; border:1px solid #8195A0; -webkit-appearance:menulist-button; }
    .vz_address_street_field, .vz_address_street_2_field, .vz_address_city_field { float:left; width:48%; padding-right:2%; }
    .vz_address_region_field, .vz_address_postal_code_field { float:left; width:23%; padding-right:2%; }
    .vz_address_country_field, .vz_address_lat_field, .vz_address_lng_field { float:left; width:31.3333%; padding-right:2%; }
    .vz_address_region_cell, .vz_address_postal_code_cell, .vz_address_lat_cell, .vz_address_lng_cell { float:left; width:48%; padding-right:2%; }
    .vz_address_country_cell { width:98%; padding-right:2%; }
</style>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBPgPzHQ87MKqZRo-OZY0Ff5TTmK6DgqxU&sensor=false"></script>
<script type="text/javascript">
(function() {
    var Vz_address = {
        geocoder : new google.maps.Geocoder(),

        init : function() {
            Vz_address.map = new google.maps.Map(
                $("<div class=\'vz_address_map_container\'/>").prependTo(".publish_vz_address").get(0),
                {
                    center: new google.maps.LatLng(25, 0),
                    zoom: 2,
                    disableDefaultUI: true,
                    mapTypeId: google.maps.MapTypeId.TERRAIN
                }
            );

            $(".vz_address_get_missing").live("click", Vz_address.get_missing);
        },

        get_missing : function(e) {
            e.preventDefault();
            $this = $(this);

            $this.prepend(\'<img src="'.PATH_CP_GBL_IMG.'loader.gif" /> \');
            var $fields = $this.parent().find("input,select"),
                address = $fields.not(".vz_address_lat, .vz_address_lng")
                    .map(function() {
                        var val = $(this).val();
                        if (val) return encodeURI(val);
                    }).get().join(", ");
            
            Vz_address.geocoder.geocode({"address": address}, function(results, status) {
              if (status == google.maps.GeocoderStatus.OK) {
                results = results[0];

                Vz_address.map.setCenter(results.geometry.location);
                Vz_address.map.setZoom(14);

                var fields = {
                    "city" : "locality",
                    "region" : "administrative_area_level_1",
                    "country" : "country",
                    "postal_code" : "postal_code"
                };

                $.each(results.address_components, function(i, component) {
                    $.each(fields, function(field_name, google_name) { 
                        if (
                            $.inArray(google_name, component.types) > -1 &&
                            $fields.filter(".vz_address_"+field_name).first().val() === ""
                        ) {
                            $fields.filter(".vz_address_"+field_name).val(component.short_name);
                        }
                    });
                });

                if (typeof results.geometry.location === "object") {
                    $fields.filter(".vz_address_lat").val(results.geometry.location.lat());
                    $fields.filter(".vz_address_lng").val(results.geometry.location.lng());
                }

                $this.find("img").remove();
              } else {
                alert("Geocode was not successful for the following reason: " + status);
              }
            });
        }
    };

    jQuery("document").ready(function($) {
        Vz_address.init();
    });
})();
</script>');
        	
        	$this->cache['css'] = TRUE;
        }
    }


	// --------------------------------------------------------------------
	
	
	/**
     * Generate the publish page UI
     */
    private function _address_form($name, $data, $is_cell=FALSE)
    {
		$this->EE->load->helper('form');
		$this->EE->lang->loadfile('vz_address');
		
        $this->_include_css_js();
		
        $form = '<div id="vz_address_map_container"></div>';
        
        // Set default values
        if (!is_array($data)) {
            $data = htmlspecialchars_decode($data);
            $decoded = (array) json_decode($data);
            $data = $decoded ? $decoded : unserialize($data);
        }
        if (!is_array($data)) $data = array();
        $data = array_merge($this->fields, $data);
        
        foreach(array_keys($this->fields) as $field)
        {
            $form .= '<div class="vz_address vz_address_'.$field.($is_cell ? '_cell' : '_field').'">';
            $form .= form_label($this->EE->lang->line($field), $name.'_'.$field);
            
            if ($field == 'country')
            {
                // Output a select box for the country
                $form .= form_dropdown($name.'['.$field.']', $this->cache['countries'], $data[$field], 'id="'.$name.'_'.$field.'" class="vz_address_'.$field.'"');
            }
            else
            {
                // All other fields are just text inputs
                $form .= form_input($name.'['.$field.']', $data[$field], 'id="'.$name.'_'.$field.'" class="vz_address_'.$field.'"');
            }
            $form .= '</div>';
        }
        
        $form .= '<a href="#" class="vz_address_get_missing">' . $this->EE->lang->line('missing') . '</a>';
        
        return $form;
    }
    
    /**
     * Display Field
     */
    function display_field($field_data)
    {
        return $this->_address_form($this->field_name, $field_data);
    }
    
    /**
     * Display Cell
     */
    function display_cell($cell_data)
    {
        return $this->_address_form($this->cell_name, $cell_data, TRUE);
    }
	
    /**
     * Display for Low Variables
     */
    function display_var_field($field_data)
    {
        return $this->_address_form($this->field_name, $field_data);
    }

	
	// --------------------------------------------------------------------
    
    
    /**
     * Save Field
     */
    function save($data)
    {
        if ($data == $this->fields)
        {
            return '';
        }
        else
        {
        	return json_encode($data);
        }
    }
    
    /**
     * Save Cell
     */
    function save_cell($data)
    {
        return $this->save($data);
    }
	
    /**
     * Save Low Variable
     */
    function save_var_field($data)
    {
        return $this->save($data);
    }

	
	// --------------------------------------------------------------------


    /**
     * Unserialize the data
     */
    function pre_process($data)
    {
        $data = htmlspecialchars_decode($data);
        $decoded = (array) json_decode($data);
        return $decoded ? $decoded : unserialize($data);
    }

    /**
     * Display Tag
     */
    function replace_tag($address, $params=array(), $tagdata=FALSE)
    {
        $wrapper_attr = isset($params['wrapper_attr']) ? $params['wrapper_attr'] : FALSE;
        $style = isset($params['style']) ? $params['style'] : 'microformat';
        
        if (!$tagdata) // Single tag
        {
            switch ($style)
            {
                case 'inline' :
                    $output = "{$address['street']}, ".($address['street_2'] ? $address['street_2'].', ' : '')."{$address['city']}, {$address['region']} {$address['postal_code']}, {$this->EE->lang->line($address['country'])}";
                    break;
                case 'plain' :
                    $output = "
                        {$address['street']}
                        {$address['street_2']}
                        {$address['city']}, {$address['region']} {$address['postal_code']}
                        {$this->EE->lang->line($address['country'])}";
                    break;
                case 'rdfa' :
                    $output = "
                        <div xmlns:v='http://rdf.data-vocabulary.org/#' typeof='v:Address' class='adr' {$wrapper_attr}>
                            <div property='v:street-address'>
                                <div class='street-address'>{$address['street']}</div>
                                <div class='extended-address'>{$address['street_2']}</div>
                            </div>
                            <div>
                                <span property='v:locality' class='locality'>{$address['city']}</span>,
                                <span property='v:region' class='region'>{$address['region']}</span>
                                <span property='v:postal-code' class='postal-code'>{$address['postal_code']}</span>
                            </div>
                            <div property='v:contry-name' class='country'>{$this->EE->lang->line($address['country'])}</div>
                        </div>";
                    break;
                case 'schema' :
                    $output = "
                        <div itemprop='address' itemscope itemtype='http://schema.org/PostalAddress' class='adr' {$wrapper_attr}>
                            <div itemprop='streetAddress'>
                                <div class='street-address'>{$address['street']}</div>
                                <div class='extended-address'>{$address['street_2']}</div>
                            </div>
                            <div>
                                <span itemprop='addressLocality' class='locality'>{$address['city']}</span>,
                                <span itemprop='addressRegion' class='region'>{$address['region']}</span>
                                <span itemprop='postalCode' class='postal-code'>{$address['postal_code']}</span>
                            </div>
                            <div itemprop='addressCountry' class='country'>{$this->EE->lang->line($address['country'])}</div>
                        </div>";
                    break;
                case 'microformat' : default :
                    $output = "
                        <div class='adr' {$wrapper_attr}>
                            <div class='street-address'>{$address['street']}</div>
                            <div class='extended-address'>{$address['street_2']}</div>
                            <div>
                                <span class='locality'>{$address['city']}</span>,
                                <span class='region'>{$address['region']}</span>
                                <span class='postal-code'>{$address['postal_code']}</span>
                            </div>
                            <div class='country'>{$this->EE->lang->line($address['country'])}</div>
                        </div>";
            }
    	}
    	else // Tag pair
    	{
            $address['country'] = $this->EE->lang->line($address['country']);
            
            // Replace the variables            
            $output = $this->EE->TMPL->parse_variables($tagdata, array($address));
    	}
            
        return $output;
    }
	
	/**
     * Display Low Variables tag
	 */
    function display_var_tag($var_data, $tagparams, $tagdata) 
    {
        $data = htmlspecialchars_decode($var_data);
        $decoded = (array) json_decode($data);
        $data = $decoded ? $decoded : unserialize($data);
        return $this->replace_tag($data, $tagparams, $tagdata);
    }
    
    /*
     * Individual address pieces
     */
    function replace_street($address, $params=array(), $tagdata=FALSE)
    {
        return $address['street'];
    }
    function replace_street_2($address, $params=array(), $tagdata=FALSE)
    {
        return $address['street_2'];
    }
    function replace_city($address, $params=array(), $tagdata=FALSE)
    {
        return $address['city'];
    }
    function replace_region($address, $params=array(), $tagdata=FALSE)
    {
        return $address['region'];
    }
    function replace_postal_code($address, $params=array(), $tagdata=FALSE)
    {
        return $address['postal_code'];
    }
    function replace_country($address, $params=array(), $tagdata=FALSE)
    {
        if (isset($params['code']) && $params['code'] == 'yes')
        {
            return $address['country'];
        }
        else
        {
            return $this->EE->lang->line($address['country']);
        }
    }
}

/* End of file ft.vz_address.php */