<?php

if ( ! defined('EXT')) exit('Invalid file request');


/**
 * Image Hotspot
 *
 * @package   Image Hotspot
 * @author    Fusionary <info@fusionary.com>
 * @copyright Copyright (c) 2010 Fusionary
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */
class Hotspotter extends Fieldframe_Fieldtype {

  /**
   * Fieldtype Info
   * @var array
   */
  var $info = array(
    'name'     => 'Hotspotter',
    'version'  => '1.1.0',
    'docs_url' => '',
    'no_lang'  => TRUE
  );

  var $default_site_settings = array(
  );

  var $default_field_settings = array(
    'file_field' => '',
    'initial_height' => '30px',
    'initial_width' => '30px',
    'resizable' => 'yes',
    'user_defined_text' => 'no',
  );

  var $requires = array(
    'ff'        => '1.0.5',
    'cp_jquery' => '1.1',
  );

  // var $hooks = array();
  // var $postpone_saves = TRUE;
  // var $cache = array();

  function _get_fields()
  {
    global $DB;

    $query_fields = $DB->query("SELECT field_label, field_id, site_id, group_id
                                FROM exp_weblog_fields
                                ORDER BY group_id, field_order");

    $query_field_groups = $DB->query("SELECT exp_field_groups.group_id, exp_field_groups.group_name, exp_field_groups.site_id,
                                      COUNT(exp_weblog_fields.group_id) as count
                                      FROM exp_field_groups
                                      LEFT JOIN exp_weblog_fields ON (exp_field_groups.group_id = exp_weblog_fields.group_id)
                                      GROUP BY exp_field_groups.group_id
                                      ORDER BY exp_field_groups.site_id, exp_field_groups.group_name");

    $query_sites = $DB->query("SELECT site_id, site_label FROM exp_sites");

    $r = array();
    foreach ($query_field_groups->result as $group)
    {
      $r[$group['group_name']] = array();
      foreach ($query_fields->result as $field)
      {
        if ($group['group_id'] == $field['group_id']) {
          $r[$group['group_name']][$field['field_id']] = $field['field_label'];
        }
      }

    }

    return $r;
  }

  /**
   * Display Field Settings
   *
   * @param  array  $field_settings  The field's settings
   * @return array  Settings HTML (cell1, cell2, rows)
   */
  function display_field_settings($field_settings)
  {
    global $DSP, $LANG;

    $SD = new Fieldframe_SettingsDisplay();

    // $r = $SD->block('Hotspotter Settings');

    $r = '<table>';

    $r .= $SD->row(array(
      $DSP->qdiv('defaultBold', 'Select Your File Field'),
      $SD->select('file_field', $field_settings['file_field'], $this->_get_fields()),
    ));
    $r .= $SD->row(array(
      $DSP->qdiv('defaultBold', 'Select Your Matrix Field'),
      $SD->select('matrix_field', $field_settings['matrix_field'], $this->_get_fields()),
    ));
    $r .= $SD->row(array(
          $SD->label('Initial Height'),
          $SD->text('initial_height', $field_settings['initial_height'])
    ));
    $r .= $SD->row(array(
      $SD->label('Initial Width'),
      $SD->text('initial_width', $field_settings['initial_width'])
    ));
    $r .= $SD->row(array(
      $SD->label('Should Hotspots be Resizable?'),
      $SD->radio_group('resizable', $field_settings['resizable'], array('yes' => 'yes', 'no' => 'no'))
    ));
    $r .= '</table>';
    // $r .= $SD->block_c();
    return array('cell2' => $r);
    // return array('cell2' => $cell2);
  }

// $r .= $this->_build_row($field_name, $n, $v, $usertext);

  function _build_row($row_id, $index, $values, $usertext)
  {
    global $DSP;

    // $r = '<tr id="hotspot-' . $index . '" class="hotspot">';
    // $cell_width = $usertext ? '50%' : '100%';
    //
    // $r .= $DSP->td('tableCellOne');
    // $r .= $DSP->qdiv('defaultSmall', $index);
    // $r .= '<input type="hidden" class="hs_id" value="' . $index . '">';
    //
    // $hsdims = array('top','left','height','width');
    //
    // foreach ($hsdims as $hsdim) {
    //   $r .= '<input type="hidden" class="hs_' . $hsdim . '" name="' . $row_id . '[hotspots][' . $index . '][' . $hsdim . ']" value="' . $values[$hsdim] . '">';
    // }
    //
    // $r .= $DSP->td_c();
    //
    // $r .= $DSP->td('tableCellOne', $cell_width);
    // $r .= $DSP->input_text($row_id . '[hotspots][' . $index . '][link]', $values['link'],'', '', 'hs_link', '80%');
    // $r .= $DSP->td_c();
    //
    // if ($usertext):
    //   $r .= $DSP->td('tableCellOne', $cell_width);
    //   $r .= '<textarea style="width: 80%;" class="hs_link_text" name="' . $row_id . '[hotspots][' . $index . '][link_text]">' . $values['link_text'] . '</textarea>';
    //   // $r .= $DSP->input_text($row_id . '[hotspots][' . $index . '][link_text]', $values['link_text'],'', '', 'hs_link_text', '100%');
    //   $r .= $DSP->td_c();
    //
    // endif;
    // $r .= $DSP->td('tableCellOne');
    // $r .= '<a class="hs_del" href="#">Remove</a>';
    // $r .= $DSP->td_c();
    //
    // $r .= '</tr>';
    // return $r;
    return '';
  }

  /**
   * Display Field
   *
   * @param  string  $field_name      The field's name
   * @param  mixed   $field_data      The field's current value
   * @param  array   $field_settings  The field's settings
   * @return string  The field's HTML
   */
  function display_field($field_name, $field_data, $field_settings)
  {
    global $DB, $FF, $IN, $DSP;
    $r = '';
    $entry_id = $IN->GBL('entry_id');
    $file_field = $field_settings['file_field'];
    $matrix_field = $field_settings['matrix_field'];

    // $r .= '<pre id="karlsw">' . print_r($matrix_field, true) . '</pre>';

    $file_dir_id = $FF->ftypes_by_field_id[$file_field]['settings']['options'];
    $file_dir = $DB->query("SELECT url
                            FROM exp_upload_prefs
                            WHERE id = $file_dir_id");

    if (!$entry_id) {
      return $DSP->qdiv('box', 'Please save entry to begin configuring image hotspots.');
    }

    $image_query = $DB->query("SELECT field_id_" . $file_field . "
                               FROM exp_weblog_data
                               WHERE entry_id = '" . $entry_id . "'");

    $image = $image_query->row['field_id_' . $file_field];
    $image_path = $file_dir->row['url'] . $image;

    if (!$image) {
     return $DSP->qdiv('box', 'Please upload an image to the file field before configuring hotspots.');
    }

    // display the image
    // $r  = $DSP->qdiv('box', 'Press the Add Hotspot button to create a hotspot on the image.');
    $r .= '<div id="hotspot-' . $field_name . '" class="hotspot-wrapper">';
    $r  .= '<div style="display:block; margin: 0 0 10px;"><div class="hotspot-img-wrap"><img class="hotspot-img" src="' . $image_path . '" alt="" /></div></div>';

    $default_data = $field_settings;

    $default_data['top'] = '0';
    $default_data['left'] = '0';
    $default_data['height'] = $field_settings['initial_height'];
    $default_data['width'] = $field_settings['initial_width'];
    $default_data['id'] = $field_name;


    $r .= '</div>';

    $default_json = json_encode($default_data);
    $inlinejs = 'var HOTSPOTTER = HOTSPOTTER || [];' ."\n";
    $inlinejs .= 'HOTSPOTTER.push(' . $default_json . ');' . "\n";
    $this->insert_js($inlinejs);
    $this->include_js('scripts/mustache.js');
    $this->include_js('scripts/hotspotter.js');
    $this->include_css('styles/hotspotter.css');

    return $r;
  }

  /**
   * Save Field
   *
   * @param  string  $field_data      The field's data
   * @param  array   $field_settings  The field's settings
   * @return string  Modified $field_data
   */
  function save_field($field_data, $field_settings)
  {
    return $field_data;
  }

  /**
   * Display Tag
   *
   * @param  array   $params          Name/value pairs from the opening tag
   * @param  string  $tagdata         Chunk of tagdata between field tag pairs
   * @param  string  $field_data      Currently saved field value
   * @param  array   $field_settings  The field's settings
   * @return string  relationship references
   */
  function display_tag($params, $tagdata, $field_data, $field_settings)
  {
    global $TMPL, $FF;
    $r = '';
    $this->prep_iterators($tagdata);
    foreach ($field_data['hotspots'] as $row_count => $row)
    {
      $row_tagdata = $tagdata;
      $this->parse_iterators($row_tagdata);
      $row_tagdata = $TMPL->swap_var_single('top', $field_data['hotspots'][$row_count]['top'], $row_tagdata);
      $row_tagdata = $TMPL->swap_var_single('left', $field_data['hotspots'][$row_count]['left'], $row_tagdata);
      $row_tagdata = $TMPL->swap_var_single('height', $field_data['hotspots'][$row_count]['height'], $row_tagdata);
      $row_tagdata = $TMPL->swap_var_single('width', $field_data['hotspots'][$row_count]['width'], $row_tagdata);
      $row_tagdata = $TMPL->swap_var_single('link', $field_data['hotspots'][$row_count]['link'], $row_tagdata);
      if ( isset($field_data['hotspots'][$row_count]['link_text']) ):
        $row_tagdata = $TMPL->swap_var_single('link_text', $field_data['hotspots'][$row_count]['link_text'], $row_tagdata);
      endif;
      $r .= $row_tagdata;
    }

    return $r;
  }


}

