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
        'version'   => '1.3.3',
    );

    public $has_array_data = TRUE;

    protected $country_codes = array("AF","AL","DZ","AS","AD","AO","AI","AQ","AG","AR","AM","AW","AU","AT","AZ","BS","BH","BD","BB","BY","BE","BZ","BJ","BM","BT","BO","BA","BW","BV","BR","IO","BN","BG","BF","BI","KH","CM","CA","CV","KY","CF","TD","CL","CN","CX","CC","CO","KM","CG","CD","CK","CR","CI","HR","CU","CY","CZ","DK","DJ","DM","DO","TP","EC","EG","SV","GQ","ER","EE","ET","FK","FO","FJ","FI","FR","FX","GF","PF","TF","GA","GM","GE","DE","GH","GI","GR","GL","GD","GP","GU","GT","GN","GW","GY","HT","HM","VA","HN","HK","HU","IS","IN","ID","IR","IQ","IE","IL","IT","JM","JP","JO","KZ","KE","KI","KP","KR","KW","KG","LA","LV","LB","LS","LR","LY","LI","LT","LU","MO","MK","MG","MW","MY","MV","ML","MT","MH","MQ","MR","MU","YT","MX","FM","MD","MC","MN","MS","MA","MZ","MM","NA","NR","NP","NL","AN","NC","NZ","NI","NE","NG","NU","NF","MP","NO","OM","PK","PW","PA","PG","PY","PE","PH","PN","PL","PT","PR","QA","RE","RO","RU","RW","KN","LC","VC","WS","SM","ST","SA","SN","SC","SL","SG","SK","SI","SB","SO","ZA","GS","ES","LK","SH","PM","SD","SR","SJ","SZ","SE","CH","SY","TW","TJ","TZ","TH","TG","TK","TO","TT","TN","TR","TM","TC","TV","UG","UA","AE","GB","US","UM","UY","UZ","VU","VE","VN","VG","VI","WF","EH","YE","YU","ZM","ZW");

    protected $fields = array(
        'name' => '',
        'street' => '',
        'street_2' => '',
        'city' => '',
        'region' => '',
        'postal_code' => '',
        'country' => 'US'
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
	private function _include_css()
	{
        if ( !$this->cache['css'] )
        {
            $this->EE->cp->add_to_head('<style type="text/css">
                .vz_address { padding-bottom:0.5em; }
                .vz_address label { display:block; }
                .vz_address input { width:97%; padding:4px; }
                .vz_address select { width:101%; }
                .vz_address_name_field input { width:98.5%; }
                .vz_address_street_field, .vz_address_city_field { float:left; width:49%; padding-right:1%; }
                .vz_address_street_2_field { float:right; width:49%; padding-left:1%; }
                .vz_address_region_field { float:left; width:24%; padding-left:1%; }
                .vz_address_postal_code_field { float:right; width:24%; }
                .vz_address_region_field input, .vz_address_postal_code_field input { width:94%; }
                .vz_address_country_field { width:48%; }
                .matrix .vz_address input { width:98.5%; }
                .matrix .vz_address select { width:100%; }
                .vz_address_region_cell { float:left; width:48%; }
                .vz_address_postal_code_cell { float:right; width:48%; }
                .matrix .vz_address_region_cell input, .matrix .vz_address_postal_code_cell input { width:97%; }
                .vz_address_previous { margin-top:1em; padding:4px 5px; background:#E1E8ED; border:1px solid #D0D7DF; -webkit-border-radius: 4px; -moz-border-radius: 4px; border-radius: 4px; }
                .vz_address_previous label { display:inline; margin-right:4px; font:italic 1.25em "Times New Roman",Times,serif; }
            </style>');

        	$this->cache['css'] = TRUE;
        }
    }


	// --------------------------------------------------------------------


	/**
     * Field settings UI
     */
    function display_settings($settings)
    {
		$this->EE->load->library('table');
		$this->EE->lang->loadfile('vz_address');

        $display_name = !empty($settings['display_name']);
		$this->EE->table->add_row(array(
			lang('display_name'),
			form_radio('vz_address_display_name', 'y', $display_name, 'id="vz_address_display_name_yes"') . ' ' .
			form_label(lang('yes'), 'vz_address_display_previous_yes') .
			'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
			form_radio('vz_address_display_name', '', !$display_name, 'id="vz_address_display_name_no"') . ' ' .
			form_label(lang('no'), 'vz_address_display_name_no')
		));

        $display_previous = !empty($settings['display_previous']);
		$this->EE->table->add_row(array(
			lang('display_previous'),
			form_radio('vz_address_display_previous', 'y', $display_previous, 'id="vz_address_display_previous_yes"') . ' ' .
			form_label(lang('yes'), 'vz_address_display_previous_yes') .
			'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
			form_radio('vz_address_display_previous', '', !$display_previous, 'id="vz_address_display_previous_no"') . ' ' .
			form_label(lang('no'), 'vz_address_display_previous_no')
		));
    }

    /**
     * Save Field Settings
     */
    function save_settings()
    {
        return array(
            'display_name' => $this->EE->input->post('vz_address_display_name'),
            'display_previous' => $this->EE->input->post('vz_address_display_previous')
        );
    }

	/**
     * Cell settings UI
     */
    function display_cell_settings($settings)
    {
		$this->EE->lang->loadfile('vz_address');

        return array(
            array(lang('display_name'), form_checkbox('display_name', 'y', (!empty($settings['display_name']))))
        );
    }

    /**
     * Low Variables settings UI
     */
    function display_var_settings($settings)
    {
        $display_name = !empty($settings['display_name']);
		return array(array(
			lang('display_name'),
			form_radio('variable_settings[vz_address][display_name]', 'y', $display_name, 'id="vz_address_display_name_yes"') . ' ' .
			form_label(lang('yes'), 'vz_address_display_previous_yes') .
			'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
			form_radio('variable_settings[vz_address][display_name]', '', !$display_name, 'id="vz_address_display_name_no"') . ' ' .
			form_label(lang('no'), 'vz_address_display_name_no')
		));
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

        $form = '';

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
            // Should we display the Name field?
            if ($field == 'name' && empty($this->settings['display_name'])) continue;

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

        // Allow for picking from previous addresses
        if (!empty($this->settings['display_previous']))
        {
            // Get the previous values from the database
            $field_name = 'field_id_'.$this->field_id;
            $this->EE->db->select($field_name);
            $this->EE->db->where($field_name." <> ''");
            $this->EE->db->distinct();
            $query = $this->EE->db->get('exp_channel_data')->result_array();

            if (count($query))
            {
                // Condense the query into the data we need
                $select_values = array('');
                $json_values = array($this->fields);
                foreach ($query as $row)
                {
                    $row = array_shift($row);
                    $row = htmlspecialchars_decode($row);
                    $decoded = (array) json_decode($row);
                    $row = $decoded ? $decoded : unserialize($row);
                    if (is_array($row) && $row != $this->fields)
                    {
                        $select_values[] = implode(', ', array_filter($row));
                        $json_values[] = $row;
                    }
                }

                // Create the markup
                $form .= '<div class="vz_address_previous"><label for="'.$name.'_previous">'.$this->EE->lang->line('previous').':</label> ';
                $form .= form_dropdown('', $select_values, NULL, 'id="'.$name.'_previous"');
                $form .= '<script type="text/javascript">';
                $form .= 'var vz_address_previous_values_'.$this->field_id.' = '.json_encode($json_values).';';
                $form .= '$("#'.$name.'_previous").change(function(){ ';
                $form .= '$.each(vz_address_previous_values_'.$this->field_id.'[$(this).val()], function(key, value) { ';
                $form .= '$("[name=\''.$name.'["+key+"]\']").val(value); ';
                $form .= '}); });</script></div>';
            }
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
                    $output = (!empty($address['name']) ? $address['name'].', ' : '')."{$address['street']}, ".($address['street_2'] ? $address['street_2'].', ' : '')."{$address['city']}, {$address['region']} {$address['postal_code']}, {$this->EE->lang->line($address['country'])}";
                    break;
                case 'plain' :
                    $output = "
                        " . (!empty($address['name']) ? "{$address['name']}" : '') . "
                        {$address['street']}
                        {$address['street_2']}
                        {$address['city']}, {$address['region']} {$address['postal_code']}
                        {$this->EE->lang->line($address['country'])}";
                    break;
                case 'rdfa' :
                    $output = "
                        <div xmlns:v='http://rdf.data-vocabulary.org/#' typeof='v:Address' class='adr' {$wrapper_attr}>
                            " . (!empty($address['name']) ? "<div property='v:name'>{$address['name']}</div>" : '') . "
                            <div property='v:street-address'>
                                <div property='v:street-address'>{$address['street']}</div>
                                <div property='v:extended-address'>{$address['street_2']}</div>
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
                            " . (!empty($address['name']) ? "<div itemprop='name'>{$address['name']}</div>" : '') . "
                            <div itemprop='streetAddress'>
                                <div itemprop='street-address'>{$address['street']}</div>
                                <div itemprop='extended-address'>{$address['street_2']}</div>
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
                    $output = '';
                    if (!empty($address['name'])) $output .= "
                        <div class='vcard'><div class='fn'>{$address['name']}</div>";
                    $output .= "
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
                    if (!empty($address['name'])) $output .= "</div>";
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

    /*
     * Individual address pieces
     */
    function replace_name($address, $params=array(), $tagdata=FALSE)
    {
        return !empty($address['name']) ? $address['name'] : '';
    }
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

    /*
     * Check if the address is empty
     */
    function replace_is_empty($address, $params=array(), $tagdata=FALSE)
    {
		$address = array_merge($this->fields, $address);
        return $address == $this->fields ? 'y' : '';
    }

    function replace_is_not_empty($address, $params=array(), $tagdata=FALSE)
    {
		$address = array_merge($this->fields, $address);
        return $address == $this->fields ? '' : 'y';
    }

    /*
     * Output a URL to the address in one of several mapping websites
     */
    function replace_map_url($address, $params=array(), $tagdata=FALSE)
    {
        $include_name = isset($params['include_name']) && ($params['include_name'] == 'yes' || $params['include_name'] == 'true');
        $source = isset($params['source']) ? strtolower($params['source']) : 'google';
        $params = isset($params['params']) ? '&' . strtolower($params['params']) : '';

        // Create the url-encoded address
        if (!$include_name && isset($address['name'])) unset($address['name']);
        $query = urlencode(implode(', ', array_filter($address)));

        switch ($source)
        {
            case 'yahoo':
                $output = "http://maps.yahoo.com/#q={$query}{$params}";
                break;
            case 'bing':
                $output = "http://www.bing.com/maps/?v=2&where1={$query}{$params}";
                break;
            case 'mapquest':
                $output = "http://mapq.st/map?q={$query}{$params}";
                break;
            case 'google': default:
                $output = "http://maps.google.com/maps?q={$query}{$params}";
                break;
        }

        return $output;
    }

    /*
     * Output a static map image
     */
    function replace_static_map($address, $params=array(), $tagdata=FALSE)
    {
        $source = isset($params['source']) ? strtolower($params['source']) : 'google';
        $width = isset($params['width']) ? strtolower($params['width']) : '400';
        $height = isset($params['height']) ? strtolower($params['height']) : '200';
        $zoom = isset($params['zoom']) ? strtolower($params['zoom']) : '14';
        $type = isset($params['type']) ? strtolower($params['type']) : 'roadmap';
        $size = isset($params['marker:size']) ? strtolower($params['marker:size']) : FALSE;
        $label = isset($params['marker:label']) ? strtoupper($params['marker:label']) : FALSE;
        $color = isset($params['marker:color']) ? strtolower($params['marker:color']) : FALSE;

        // Normalize the color parameter
        $color = str_replace('#', '0x', $color);

        // Create the url-encoded address
        if (isset($address['name'])) unset($address['name']);
        $address_string = urlencode(implode(', ', array_filter($address)));

        $output = isset($params['secure']) && $params['secure'] == 'yes' ? 'https' : 'http';
        $marker = '';
        switch ($source)
        {
            case 'yahoo':
                // TODO
            case 'bing':
                // TODO
            case 'mapquest':
                // TODO
            case 'google': default:
                $marker .= $size ? 'size:'.$size.'|' : '';
                $marker .= $color ? 'color:'.$color.'|' : '';
                $marker .= $label ? 'label:'.$label.'|' : '';
                $output .= "://maps.googleapis.com/maps/api/staticmap?zoom={$zoom}&size={$width}x{$height}&maptype={$type}&markers={$marker}{$address_string}&sensor=false";
                break;
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
}

/* End of file ft.vz_address.php */