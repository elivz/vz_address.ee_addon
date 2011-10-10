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
        'version'   => '1.1.0',
    );
	    
    var $has_array_data = TRUE;
    
    var $country_codes = array("AF","AL","DZ","AS","AD","AO","AI","AQ","AG","AR","AM","AW","AU","AT","AZ","BS","BH","BD","BB","BY","BE","BZ","BJ","BM","BT","BO","BA","BW","BV","BR","IO","BN","BG","BF","BI","KH","CM","CA","CV","KY","CF","TD","CL","CN","CX","CC","CO","KM","CG","CD","CK","CR","CI","HR","CU","CY","CZ","DK","DJ","DM","DO","TP","EC","EG","SV","GQ","ER","EE","ET","FK","FO","FJ","FI","FR","FX","GF","PF","TF","GA","GM","GE","DE","GH","GI","GR","GL","GD","GP","GU","GT","GN","GW","GY","HT","HM","VA","HN","HK","HU","IS","IN","ID","IR","IQ","IE","IL","IT","JM","JP","JO","KZ","KE","KI","KP","KR","KW","KG","LA","LV","LB","LS","LR","LY","LI","LT","LU","MO","MK","MG","MW","MY","MV","ML","MT","MH","MQ","MR","MU","YT","MX","FM","MD","MC","MN","MS","MA","MZ","MM","NA","NR","NP","NL","AN","NC","NZ","NI","NE","NG","NU","NF","MP","NO","OM","PK","PW","PA","PG","PY","PE","PH","PN","PL","PT","PR","QA","RE","RO","RU","RW","KN","LC","VC","WS","SM","ST","SA","SN","SC","SL","SG","SK","SI","SB","SO","ZA","GS","ES","LK","SH","PM","SD","SR","SJ","SZ","SE","CH","SY","TW","TJ","TZ","TH","TG","TK","TO","TT","TN","TR","TM","TC","TV","UG","UA","AE","GB","US","UM","UY","UZ","VU","VE","VN","VG","VI","WF","EH","YE","YU","ZM","ZW");
  
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
	private function _include_css()
	{
        if ( !$this->cache['css'] )
        {
            $this->EE->cp->add_to_head('<style type="text/css">
                .vz_address { padding-bottom: 0.5em; }
                .vz_address label { display:block; }
                .vz_address input { width:99%; padding:4px; }
                .vz_address_street_field, .vz_address_street_2_field, .vz_address_city_field { float:left; width:48%; padding-right:2%; }
                .vz_address_region_field, .vz_address_postal_code_field { float:left; width:23%; padding-right:2%; }
                .vz_address_region_cell, .vz_address_postal_code_cell { float:left; width:48%; padding-right:2%; }
            </style>');
        	
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
		
        $this->_include_css();
		
        $form = "";
        $fields = array(
            'street' => '',
            'street_2' => '',
            'city' => '',
            'region' => '',
            'postal_code' => '',
            'country' => 'US'
        );
        
        // Set default values
        $data = unserialize(htmlspecialchars_decode($data));
        if (!is_array($data)) $data = array();
        $data = array_merge($fields, $data);
        
        foreach(array_keys($fields) as $field)
        {
            $form .= '<div class="vz_address vz_address_'.$field.($is_cell ? '_cell' : '_field').'">';
            $form .= form_label($this->EE->lang->line($field), $name.'_'.$field);
            
            if ($field == 'country')
            {
                // Output a select box for the country
                $form .= form_dropdown($name.'['.$field.']', $this->cache['countries'], $data[$field], 'id="'.$name.'_'.$field.'"');
            }
            else
            {
                // All other fields are just text inputs
                $form .= form_input($name.'['.$field.']', $data[$field], 'id="'.$name.'_'.$field.'" class="vz_address_'.$field.'"');
            }
            $form .= '</div>';
        }
        
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
    	return serialize($data);
    }
    
    /**
     * Save Cell
     */
    function save_cell($data)
    {
        return serialize($data);
    }
	
    /**
     * Save Low Variable
     */
    function save_var_field($data)
    {
        return serialize($data);
    }

	
	// --------------------------------------------------------------------

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
        $data = unserialize(htmlspecialchars_decode($var_data);
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