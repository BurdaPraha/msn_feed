<?php

namespace Drupal\msn_feed\Plugin\views\row;

use Drupal\views\Plugin\views\row\RssFields;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Render\Markup;

/**
 * Renders an RSS item based on fields.
 *
 * @ViewsRow(
 *   id = "msn_feed__rss_fields",
 *   title = @Translation("MSN Feed Fields"),
 *   help = @Translation("Display fields as RSS items."),
 *   theme = "views_view_row_rss",
 *   display_types = {"feed"}
 * )
 */
class MsnFeedRssFields extends RssFields {

    protected function defineOptions() {
    $options = parent::defineOptions();
    $options['content_field'] = ['default' => ''];
    $options['teaser_image_field'] = ['default' => ''];
    $options['teaser_text_field'] = ['default' => ''];
    return $options;
  }

  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $initial_labels = ['' => $this->t('- None -')];
    $view_fields_labels = $this->displayHandler->getFieldLabels();
    $view_fields_labels = array_merge($initial_labels, $view_fields_labels);

    $form['content_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Content field'),
      '#description' => $this->t('Provides Content:encoded field.'),
      '#options' => $view_fields_labels,
      '#default_value' => $this->options['content_field'],
      '#required' => TRUE,
    ];
    $form['teaser_image_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Teaser image field'),
      '#description' => $this->t('Shows Image on top of the content field.'),
      '#options' => $view_fields_labels,
      '#default_value' => $this->options['teaser_image_field'],
      '#required' => TRUE,
    ];
    $form['teaser_text_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Teaser text field'),
      '#description' => $this->t('Shows teaser text on top of the content field.'),
      '#options' => $view_fields_labels,
      '#default_value' => $this->options['teaser_text_field'],
      '#required' => TRUE,
    ];
  }

   public function render($row) {
    $build = parent:: render($row);
    static $row_index;
    if (!isset($row_index)) {
      $row_index = 0;
    }
    $xml_rdf_namespaces['xmlns:' . 'content'] = 'http://purl.org/rss/1.0/modules/content/';
    //$this->view->style_plugin->namespaces += ['xmlns:content'] = ["http://purl.org/rss/1.0/modules/content/"];
    $this->view->style_plugin->namespaces += $xml_rdf_namespaces;
    //dpm($row_index,'r index');
    $item = $build['#row'];

    $field_teaser_image = $this->getField($row_index, $this->options['teaser_image_field']);
    $item_teaser_image = is_array($field_teaser_image) ? $field_teaser_image : ['#markup' => $field_teaser_image];
    //$item->teaser_image = $item_teaser_image;

    $teaser_image_string = $item_teaser_image['#markup'];
    //$teaser_image_string = $teaser_image_markup->__toString();
    $teaser_image_patched = str_replace('src="/sites/default/files', 'src="http://ma.vps5.romanpro.cz/sites/default/files', $teaser_image_string);

    $item->teaser_image = Markup::create($teaser_image_patched);


    $field_teaser_text = $this->getField($row_index, $this->options['teaser_text_field']);
    $item_teaser_text = is_array($field_teaser_text) ? $field_teaser_text : ['#markup' => $field_teaser_text];
    $item->teaser_text = $item_teaser_text;

    $field_content = $this->getField($row_index, $this->options['content_field']);
    $item_content = is_array($field_content) ? $field_content : ['#markup' => $field_content];
    //workaround:
    $content_markup = $item_content['#markup'];
    $content_string = $content_markup->__toString();
    $content_patched = str_replace('src="/sites/', 'src="http://ma.vps5.romanpro.cz/sites/', $content_string);

    $item->content = Markup::create($content_patched);

    // Re-populate the $build array with the updated row.
    $build['#row'] = $item;
    $row_index++;

    return $build;
  }

}
