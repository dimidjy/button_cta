<?php

namespace Drupal\button_cta\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\link\Plugin\Field\FieldWidget\LinkWidget;

/**
 * Plugin implementation of the 'button_cta_field_widget' widget.
 *
 * @FieldWidget(
 *   id = "button_cta_widget",
 *   label = @Translation("Button link"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class ButtonCTAWidget extends LinkWidget {

  /**
   * {@inheritdoc}
   */

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    /** @var \Drupal\link\LinkItemInterface $item */
    $item = $items[$delta];
    $options = $item->get('options')->getValue();
       /** @var \Drupal\link\LinkItemInterface $item */
      $default_value = !empty($options['attributes']['button-type']) ? $options['attributes']['button-type'] : '';
      $element['options']['attributes']['button-type'] = [
        '#type' => 'select',
        '#title' => $this->t('Select type'),
        '#options' => [
          'button-none' => $this->t('None'),
          'button-default' => $this->t('Default'),
          'button-rounded' => $this->t('Rounded'),
          'button-square' => $this->t('Square'),
          'button-white' => $this->t('White'),
          'button-flat' => $this->t('Flat'),
          'button-danger' => $this->t('Danger'),
        ],
        '#default_value' => $default_value,
      ];

      $default_value = !empty($options['attributes']['button_icon']) ? $options['attributes']['button_icon'] : '';
      $element['options']['attributes']['button_icon'] = [
        '#type' => 'select',
        '#title' => $this->t('Select icon'),
        '#options' => [
          'icon-none' => $this->t('None'),
          'icon-default' => $this->t('Default'),
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
        '#default_value' => $default_value,
      ];
  
    return $element;
  }
}
