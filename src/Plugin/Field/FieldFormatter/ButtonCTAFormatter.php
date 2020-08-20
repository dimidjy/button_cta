<?php

namespace Drupal\button_cta\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'link_separate' formatter.
 *
 * @FieldFormatter(
 *   id = "button_cta",
 *   label = @Translation("Link as Button"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class ButtonCTAFormatter extends LinkFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'trim_length' => 80,
      'rel' => '',
      'target' => '',
      'link_text' => '',
      'button-type' => 'button-default',
      'button_icon' => 'fa-home',
    ) + parent::defaultSettings();
  }

  public function settingsForm(array $parentForm, FormStateInterface $form_state) {
    $parentForm = parent::settingsForm($parentForm, $form_state);
    $settings = $this->getSettings();

    $form['link_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link text, leave empty for default'),
      '#default_value' => $settings['link_text'],
    ];
	
    $form['button-type'] = [
      '#type' => 'select',
      '#title' => $this->t('Button type'),
      '#default_value' => $settings['button-type'],
      '#options' => [
        'button-default' => $this->t('Default'),
        'button-rounded' => $this->t('Rounded'),
        'button-square' => $this->t('Square'),
        'button-white' => $this->t('White'),
        'button-flat' => $this->t('Flat'),
        'button-danger' => $this->t('Danger'),
      ],
      '#required' => TRUE,
    ];

    $form['button_icon'] = [
      '#type' => 'select',
      '#title' => $this->t('Button icon'),
      '#default_value' => $settings['button_icon'],
      '#options' => [
        'fa-address-card' => $this->t('Address card'),
        'fa-home' => $this->t('Home'),
        'fa-envelope' => $this->t('Envelope'),
        'fa-edit' => $this->t('Edit'),
        'fa-phone' => $this->t('Phone'),
        'fa-save' => $this->t('Save'),
        'fa-file-alt' => $this->t('File'),
        'fa-paste' => $this->t('Paste'),
        'fa-chart-bar' => $this->t('Chart'),
        'fa-folder-plus' => $this->t('Folder'),
        'fa-print' => $this->t('Print'),
        'fa-tags' => $this->t('Tags'),
        'fa-tasks' => $this->t('Tasks'),
        'fa-copy' => $this->t('Copy')
      ],
      '#required' => TRUE,
    ];
    return $form + $parentForm;
  }

  public function settingsSummary() {
    $settings = $this->getSettings();

    $summary[] = $this->t('Button type: @text', ['@text' => $settings['button-type']]);

    $summary[] = $this->t('Icon class: "@text"', ['@text' => $settings['button_icon']]);

    if (!empty($settings['link_text'])) {
      $summary[] = $this->t('Link text: @text', ['@text' => $settings['link_text']]);
    }
    if (!empty($settings['target'])) {
      $summary[] = $this->t('Open link in new window');
    }

    return $summary;
  }


  
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = array();
    $entity = $items->getEntity();
    $settings = $this->getSettings();

    foreach ($items as $delta => $item) {
      // By default use the full URL as the link text.
      $url = $this->buildUrl($item);
      $link_title = $url->toString();

      // If the link text field value is available, use it for the text.
      if (empty($settings['url_only']) && !empty($item->title)) {
        // Unsanitized token replacement here because the entire link title
        // gets auto-escaped during link generation in
        // \Drupal\Core\Utility\LinkGenerator::generate().
        $link_title = \Drupal::token()->replace($item->title, [$entity->getEntityTypeId() => $entity], ['clear' => TRUE]);
      }

      if (!empty($settings['link_text'])) {
        $link_title = $this->t($settings['link_text']);
      }

      // The link_separate formatter has two titles; the link text (as in the
      // field values) and the URL itself. If there is no link text value,
      // $link_title defaults to the URL, so it needs to be unset.
      // The URL version may need to be trimmed as well.
      if (empty($item->title) && empty($settings['link_text'])) {
        $link_title = NULL;
      }
      $url_title = $url->toString();
      if (!empty($settings['trim_length'])) {
        $link_title = Unicode::truncate($link_title, $settings['trim_length'], FALSE, TRUE);
        $url_title = Unicode::truncate($url_title, $settings['trim_length'], FALSE, TRUE);
      }

      $element[$delta] = array(
        '#theme' => 'link_formatter_button_cta',
        '#title' => $link_title,
        '#url_title' => $url_title,
        '#url' => $url,
        '#button-type' => $settings['button-type'],
        '#button_icon' => $settings['button_icon'],
      );

      if (!empty($item->_attributes)) {
        // Set our RDFa attributes on the <a> element that is being built.
        $url->setOption('attributes', $item->_attributes);

        // Unset field item attributes since they have been included in the
        // formatter output and should not be rendered in the field template.
        unset($item->_attributes);
      }
    }
    return $element;
  }

}
