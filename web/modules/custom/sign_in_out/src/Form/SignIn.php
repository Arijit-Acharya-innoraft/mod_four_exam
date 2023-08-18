<?php

namespace Drupal\sign_in_out\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the signin form.
 */
class SignIn extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sign_in_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['full_name'] = [
      '#type' => 'textfield',
      '#title' => 'Full Name',
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => 'Email',
    ];

    $form['password'] = [
      '#type' => 'password',
      '#title' => 'Password',
    ];

    $form['checkbox '] = [
      '#type' => 'checkbox',
      '#title' => 'Enable OTP Verification',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
    ];
    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    // Validation of full name.
    if (preg_match("/^[a-zA-Z]+$/", $form_state->getValue('full_name')) == FALSE) {
      $form_state->setErrorByName('full_name', $this->t('Only text allowed'));
    }
    // Validation of email.
    if (preg_match("/^[a-zA-Z0-9\+\-\_\~\.\@]+$/", $form_state->getValue('email')) == FALSE) {
      $form_state->setErrorByName('email', $this->t('Input proper email id'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->messenger()->addStatus($this->t('Your form is submitted'));
    parent::submitForm($form, $form_state);
  }

}
