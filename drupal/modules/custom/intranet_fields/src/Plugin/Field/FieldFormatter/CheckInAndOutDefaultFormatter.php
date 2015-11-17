<?php

/**
 * @file
 * Contains \Drupal\intranet_fields\Plugin\Field\FieldFormatter\CheckInAndOutDefaultFormatter.
 */

namespace Drupal\intranet_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'check_in_and_out_default' formatter.
 *
 * @FieldFormatter(
 *   id = "check_in_and_out_default",
 *   label = @Translation("Check in and checkout"),
 *   field_types = {
 *     "check_in_and_out",
 *   },
 * )
 */
class CheckInAndOutDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'check_in_date_format' => DATETIME_DATETIME_STORAGE_FORMAT,
      'check_out_date_format' => DATETIME_DATETIME_STORAGE_FORMAT,
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    $check_in_date_format = $this->getSetting('check_in_date_format');
    $check_out_date_format = $this->getSetting('check_out_date_format');

    foreach ($items as $delta => $item) {
      $output = '';

      if (!empty($item->check_in)) {
        $check_in = $item->check_in;
        $output = $this->t('Check in') . ': ' . date($check_in_date_format, $check_in);
      }

      if (!empty($item->check_out)) {
        $check_out = !empty($item->check_out) ? $item->check_out : NULL;

        if ($check_out) {
          $output .= ', ' . $this->t('Check out') . ': ' . date($check_out_date_format, $check_out);
        }
      }

      $elements[$delta] = array(
        '#markup' => $output,
        '#cache' => array(
          'contexts' => array(
            'timezone',
          ),
        ),
      );
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form = parent::settingsForm($form, $form_state);

    $form['check_in_date_format'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Check-in date/time format'),
      '#description' => $this->t('See <a href=":url" target="_blank">the documentation for PHP date formats</a>.', [':url' => 'http://php.net/manual/function.date.php']),
      '#default_value' => $this->getSetting('check_in_date_format'),
    );

    $form['check_out_date_format'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Check-out date/time format'),
      '#description' => $this->t('See <a href=":url" target="_blank">the documentation for PHP date formats</a>.', [':url' => 'http://php.net/manual/function.date.php']),
      '#default_value' => $this->getSetting('check_out_date_format'),
    );

    return $form;
  }

}
