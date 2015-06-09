<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * VZ Address Class
 *
 * @author    Eli Van Zoeren <eli@elivz.com>
 * @copyright Copyright (c) 2011-2015 Eli Van Zoeren
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */

class Vz_address_ft extends EE_Fieldtype {

    public $info = array(
        'name'      => 'VZ Address',
        'version'   => '1.5.4',
    );

    public $has_array_data = true;
    private $debug = false;

    protected $country_codes = array('AF','AX','AL','DZ','AS','AD','AO','AI','AQ','AG','AR','AM','AW','AU','AT','AZ','BS','BH','BD','BB','BY','BE','BZ','BJ','BM','BT','BO','BQ','BA','BW','BV','BR','IO','BN','BG','BF','MM','BI','KH','CM','CA','CV','KY','CF','TD','CL','CN','CX','CC','CO','KM','CG','CK','CR','HR','CU','CW','CY','CZ','CD','DK','DJ','DM','DO','EC','EG','SV','GQ','ER','EE','ET','FK','FO','FJ','FI','FR','GF','PF','TF','GA','GM','GE','DE','GH','GI','GR','GL','GD','GP','GU','GT','GG','GN','GW','GY','HT','HM','HN','HK','HU','IS','IN','ID','IR','IQ','IE','IM','IL','IT','CI','JM','JP','JE','JO','KZ','KE','KI','KP','KR','KW','KG','LA','LV','LB','LS','LR','LY','LI','LT','LU','MO','MK','MG','MW','MY','MV','ML','MT','MH','MQ','MR','MU','YT','MX','FM','MD','MC','MN','ME','MS','MA','MZ','NA','NR','NP','NL','NC','NZ','NI','NE','NG','NU','NF','MP','NO','OM','PK','PS','PW','PA','PG','PY','PE','PH','PN','PL','PT','11','PR','QA','RE','RS','RO','RU','RW','BL','GS','KN','LC','MF','VC','WS','SM','ST','SA','SN','SC','SL','SG','SX','SK','SI','SB','SO','ZA','SS','ES','LK','SH','PM','SD','SR','SJ','SZ','SE','CH','SY','TW','TJ','TZ','TH','TL','TG','TK','TO','TT','TN','TR','TM','TC','TV','UG','UA','AE','GB','US','UM','UY','UZ','VU','VA','VE','VN','VG','VI','WF','EH','YE','ZM','ZW');

    protected $fields = array(
        'name'        => '',
        'street'      => '',
        'street_2'    => '',
        'city'        => '',
        'region'      => '',
        'postal_code' => '',
        'country'     => 'US'
    );


    // --------------------------------------------------------------------


    /**
     * Fieldtype Constructor
     */
    public function __construct()
    {
        parent::__construct();

        ee()->lang->loadfile('vz_address');

        // Cache the array of country names
        $countries = array();
        foreach ($this->country_codes as $country) {
            $countries[$country] = ee()->lang->line('vz_address_'.$country);
        }
        ee()->session->set_cache(__CLASS__, 'countries', $countries);
    }

    /*
     * Register acceptable content types
     */
    public function accepts_content_type($name)
    {
        return ($name == 'channel' || $name == 'grid');
    }


    // --------------------------------------------------------------------


    /**
     * Include the CSS files,
     * but only the first time
     */
    private function _include_css()
    {
        if (!ee()->session->cache(__CLASS__, 'css') )
        {
            // Output stylesheet
            $css = file_get_contents(PATH_THIRD . '/vz_address/assets/styles' . ($this->debug ? '' : '.min') . '.css');
            ee()->cp->add_to_head('<style type="text/css">' . $css . '</style>');

            // Output Javascript
            $this->EE->cp->add_js_script(
                array('ui' => array('core', 'autocomplete'))
            );
            $scripts = file_get_contents(PATH_THIRD . '/vz_address/assets/scripts' . ($this->debug ? '' : '.min') . '.js');
            ee()->javascript->output($scripts);

            ee()->session->set_cache(__CLASS__, 'css', TRUE);
        }
    }


    // --------------------------------------------------------------------


    /**
     * Field settings UI
     */
    public function display_settings($settings)
    {
        $display_name = ! empty($settings['display_name']);
        $display_previous = !empty($settings['display_previous']);

        ee()->table->add_row(array(
            lang('vz_address_display_name'),
            form_radio('vz_address_display_name', 'y', $display_name, 'id="vz_address_display_name_yes"') . ' ' .
            form_label(lang('yes'), 'display_previous_yes') .
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
            form_radio('vz_address_display_name', '', !$display_name, 'id="vz_address_display_name_no"') . ' ' .
            form_label(lang('no'), 'vz_address_display_name_no')
        ));

        ee()->table->add_row(array(
            lang('vz_address_display_previous'),
            form_radio('vz_address_display_previous', 'y', $display_previous, 'id="display_previous_yes"') . ' ' .
            form_label(lang('yes'), 'display_previous_yes') .
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
            form_radio('vz_address_display_previous', '', !$display_previous, 'id="display_previous_no"') . ' ' .
            form_label(lang('no'), 'display_previous_no')
        ));
    }

    /**
     * Display Grid Cell Settings
     */
    public function grid_display_settings($settings)
    {
        $display_name = ! empty($settings['display_name']);

        return array(
            $this->grid_checkbox_row(
                lang('vz_address_display_name'),
                'vz_address_display_name',
                'y',
                $display_name
            )
        );
    }


    /**
     * Display Matrix Cell Settings
     */
    public function display_cell_settings($settings)
    {
        $display_name = ! empty($settings['display_name']);

        return array(
            array(
                lang('vz_address_display_name'),
                form_checkbox('display_name', 'y', $display_name)
            )
        );
    }

    /**
     * Low Variables settings UI
     */
    public function display_var_settings($settings)
    {
        $display_name = ! empty($settings['display_name']);

        return array(array(
            lang('vz_address_display_name'),
            form_radio('variable_settings[vz_address][display_name]', 'y', $display_name, 'id="display_name_yes"') . ' ' .
            form_label(lang('yes'), 'display_previous_yes') .
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
            form_radio('variable_settings[vz_address][display_name]', '', !$display_name, 'id="display_name_no"') . ' ' .
            form_label(lang('no'), 'display_name_no')
        ));
    }


    // --------------------------------------------------------------------


    /**
     * Save Field Settings
     */
    function save_settings($settings)
    {
        return array(
            'display_name'     => empty($settings['vz_address_display_name']) ? '' : 'y',
            'display_previous' => empty($settings['vz_address_display_previous']) ? '' : 'y',
        );
    }

    /**
     * Save Matrix Cell Settings
     */
    function save_cell_settings($settings)
    {
        return array_merge(array(
            'display_name'       => '',
            'vz_url_limit_local' => ''
        ), $settings);
    }

    /**
     * Save Low Variables Settings
     */
    public function save_var_settings($settings)
    {
        return array(
            'display_name'     => empty($settings['display_name']) ? '' : 'y',
            'display_previous' => empty($settings['display_previous']) ? '' : 'y',
        );
    }


    // --------------------------------------------------------------------


    /**
     * Display Field
     */
    public function display_field($field_data)
    {
        return $this->_address_form($this->field_name, $field_data);
    }

    /**
     * Display Grid Cell
     */
    public function grid_display_field($field_data)
    {
        return $this->_address_form($this->field_name, $field_data, true);
    }

    /**
     * Display Matrix Cell
     */
    public function display_cell($cell_data)
    {
        return $this->_address_form($this->cell_name, $cell_data, true);
    }

    /**
     * Display for Low Variables
     */
    public function display_var_field($field_data)
    {
        return $this->_address_form($this->field_name, $field_data);
    }

    /**
     * Generate the publish page UI
     */
    private function _address_form($name, $data, $is_cell=false)
    {
        ee()->load->helper('form');
        $this->_include_css();

        $form = '';

        // Set default values
        if (!is_array($data) ) {
            $data = $this->pre_process($data);
        }
        if (!is_array($data)) {
            $data = array();
        }
        $data = array_merge($this->fields, $data);

        foreach(array_keys($this->fields) as $field)
        {
            // Should we display the Name field?
            if ($field == 'name' && empty($this->settings['display_name'])) continue;

            $form .= '<div class="vz_address vz_address_'.$field.($is_cell ? '_cell' : '_field').'">';
            $form .= form_label(ee()->lang->line('vz_address_'.$field), $name.'_'.$field);

            if ($field == 'country')
            {
                // Output a select box for the country
                $form .= form_dropdown($name.'['.$field.']', ee()->session->cache(__CLASS__, 'countries'), $data[$field], 'id="'.$name.'_'.$field.'"');
            }
            else
            {
                // All other fields are just text inputs
                $form .= form_input($name.'['.$field.']', $data[$field], 'id="'.$name.'_'.$field.'" class="vz_address_'.$field.'"');
            }
            $form .= '</div>';
        }

        // Allow for picking from previous addresses
        if (!empty($this->settings['display_previous']) )
        {
            // Get the previous values from the database
            $field_name = 'field_id_'.$this->field_id;
            ee()->db->select($field_name);
            ee()->db->where($field_name." <> ''");
            ee()->db->distinct();
            $query = ee()->db->get('exp_channel_data')->result_array();

            if (count($query))
            {
                // Condense the query into the data we need
                $select_values = array('');
                $json_values = array($this->fields);
                foreach ($query as $row)
                {
                    $row = array_shift($row);
                    $row = $this->pre_process($row);
                    if (is_array($row) && $row != $this->fields)
                    {
                        $select_values[] = implode(', ', array_filter($row));
                        $json_values[] = $row;
                    }
                }

                // Create the markup
                $form .= '<div class="vz_address_previous"><label for="'.$name.'_previous">'.ee()->lang->line('vz_address_previous').':</label> ';
                $form .= form_dropdown('', $select_values, null, 'id="'.$name.'_previous"');
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


    // --------------------------------------------------------------------


    /**
     * Validate Field
     */
    public function validate($data)
    {
        if (
            (isset($this->settings['field_required']) && $this->settings['field_required'] == 'y')
            ||
            (isset($this->settings['col_required']) && $this->settings['col_required'] == 'y' )
        ) {
            // If the field is required, we need at least a street address and city
            if (empty($data['street']) || empty($data['city']))
            {
                return lang('vz_address_required');
            }
        }

        return true;
    }

    /**
     * Validate Matrix Cell
     */
    public function validate_cell($data)
    {
        return $this->validate($data);
    }


    // --------------------------------------------------------------------



    /**
     * Save Field
     */
    public function save($data)
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
    public function save_cell($data)
    {
        return $this->save($data);
    }

    /**
     * Save Low Variable
     */
    public function save_var_field($data)
    {
        return $this->save($data);
    }


    // --------------------------------------------------------------------


    /**
     * Unserialize the data
     */
    public function pre_process($data)
    {
        $data = html_entity_decode($data, ENT_QUOTES);
        $decoded = json_decode($data);
        $decoded = $decoded ? $decoded : @unserialize($data);
        $address = array_merge($this->fields, (array) $decoded);
        $address['country_name'] = ee()->lang->line('vz_address_'.$address['country']);
        return $address;
    }


    // --------------------------------------------------------------------


    /**
     * Display Tag
     */
    public function replace_tag($address, $params=array(), $tagdata=false)
    {
        $wrapper_attr = isset($params['wrapper_attr']) ? $params['wrapper_attr'] : false;
        $style = isset($params['style']) ? $params['style'] : 'microformat';

        if (!$tagdata) // Single tag
        {
            switch ($style)
            {
                case 'inline' :
                    $output = (!empty($address['name']) ? $address['name'].', ' : '').($address['street'] ? $address['street'].', ' : '').($address['street_2'] ? $address['street_2'].', ' : '')."{$address['city']}, {$address['region']} {$address['postal_code']}, {$address['country_name']}";
                    break;
                case 'plain' :
                    $output = "
                        " . (!empty($address['name']) ? "{$address['name']}" : '') . "
                        {$address['street']}
                        {$address['street_2']}
                        {$address['city']}, {$address['region']} {$address['postal_code']}
                        {$address['country_name']}";
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
                            <div property='v:contry-name' class='country'>{$address['country_name']}</div>
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
                            <div itemprop='addressCountry' class='country'>{$address['country_name']}</div>
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
                            <div class='country'>{$address['country_name']}</div>
                        </div>";
                    if (!empty($address['name'])) $output .= "</div>";
            }
        }
        else // Tag pair
        {
            $address['country_code'] = $address['country'];
            $address['country'] = $address['country_name'];

            // Replace the variables
            $output = ee()->TMPL->parse_variables($tagdata, array($address));
        }

        return $output;
    }

    /*
     * Individual address pieces
     */
    public function replace_name($address, $params=array(), $tagdata=false)
    {
        return !empty($address['name']) ? $address['name'] : '';
    }

    public function replace_street($address, $params=array(), $tagdata=false)
    {
        return $address['street'];
    }

    public function replace_street_2($address, $params=array(), $tagdata=false)
    {
        return $address['street_2'];
    }

    public function replace_city($address, $params=array(), $tagdata=false)
    {
        return $address['city'];
    }

    public function replace_region($address, $params=array(), $tagdata=false)
    {
        return $address['region'];
    }

    public function replace_postal_code($address, $params=array(), $tagdata=false)
    {
        return $address['postal_code'];
    }

    public function replace_country($address, $params=array(), $tagdata=false)
    {
        if (isset($params['code']) && $params['code'] == 'yes')
        {
            return $address['country'];
        }
        else
        {
            return $address['country_name'];
        }
    }

    public function replace_country_code($address, $params=array(), $tagdata=FALSE)
    {
        return $address['country'];
    }

    /*
     * Check if the address is empty
     */
    public function replace_is_empty($address, $params=array(), $tagdata=false)
    {
        $address = array_merge($this->fields, $address);
        return $address == $this->fields ? 'y' : '';
    }

    public function replace_is_not_empty($address, $params=array(), $tagdata=false)
    {
        $address = array_merge($this->fields, $address);
        return $address == $this->fields ? '' : 'y';
    }


    // --------------------------------------------------------------------


    /*
     * Output a URL to the address in one of several mapping websites
     */
    public function replace_map_url($address, $params=array(), $tagdata=false)
    {
        $include_name = isset($params['include_name']) && ($params['include_name'] == 'yes' || $params['include_name'] == 'true');
        $source = isset($params['source']) ? strtolower($params['source']) : 'google';
        $params = isset($params['params']) ? '&' . strtolower($params['params']) : '';

        // Create the url-encoded address
        if (!$include_name && isset($address['name']) ) unset($address['name']);
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
     * Output a static map image url
     */
    public function replace_static_map($address, $params=array(), $tagdata=false)
    {
        $source = isset($params['source']) ? strtolower($params['source']) : 'google';
        $width  = isset($params['width']) ? strtolower($params['width']) : '400';
        $height = isset($params['height']) ? strtolower($params['height']) : '200';
        $scale  = isset($params['scale']) ? strtolower($params['scale']) : '1';
        $zoom   = isset($params['zoom']) ? strtolower($params['zoom']) : '14';
        $format = isset($params['format']) ? strtolower($params['format']) : 'png';
        $type   = isset($params['type']) ? strtolower($params['type']) : 'roadmap';
        $size   = isset($params['marker:size']) ? strtolower($params['marker:size']) : false;
        $label  = isset($params['marker:label']) ? strtoupper($params['marker:label']) : false;
        $color  = isset($params['marker:color']) ? strtolower($params['marker:color']) : false;

        // Normalize the color parameter
        $color = str_replace('#', '0x', $color);

        // Create the url-encoded address
        if (isset($address['name'])) {
            unset($address['name']);
        }
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
            case 'google':
            default:
                $marker .= $size ? 'size:'.$size.'|' : '';
                $marker .= $color ? 'color:'.$color.'|' : '';
                $marker .= $label ? 'label:'.$label.'|' : '';
                $output .= "://maps.googleapis.com/maps/api/staticmap?zoom={$zoom}&size={$width}x{$height}&scale={$scale}&format={$format}&maptype={$type}&markers={$marker}{$address_string}&sensor=false";
                break;
        }

        return $output;
    }

    /*
     * Output a static map image url
     */
    public function replace_static_map_tag($address, $params=array(), $tagdata=false)
    {
        $width  = isset($params['width']) ? strtolower($params['width']) : '400';
        $height = isset($params['height']) ? strtolower($params['height']) : '200';
        $map_url = $this->replace_static_map($address, $params);
        $address_string = $this->replace_tag($address, array('style'=>'inline'));
        return '<img src="'.$map_url.'" alt="'.$address_string.'" width="'.$width.'" height="'.$height.'">';
    }

    // --------------------------------------------------------------------


    /**
     * Display Low Variables tag
     */
    public function display_var_tag($var_data, $tagparams, $tagdata)
    {
        $data = $this->pre_process($var_data);
        return $this->replace_tag($data, $tagparams, $tagdata);
    }

}

/* End of file ft.vz_address.php */
