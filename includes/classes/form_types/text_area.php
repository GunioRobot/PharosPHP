<?

$style = ($form_array['width'] != '' AND $form_array['height'] != '' ) ? 'style="width:'.$form_array['width'].';height:'.$form_array['height'].';"' : '';

$item = '<textarea name="'.$form_array['name'].'" class="'.$form_array['class'].'" rows="'.$form_array['row'].'" cols="'.$form_array['col'].'" '.$style.'>'.$value.'</textarea>';
?>