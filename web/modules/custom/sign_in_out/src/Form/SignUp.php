<?php

namespace Drupal\sign_in_out\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * A form for taking input from  student and providing notification on submit.
 */
class SignUp extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sign_up_form';
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

    $form['mobile'] = [
      '#type' => 'tel',
      '#title' => 'Mobile No',
    ];

    $form['stream'] = [
      '#type' => 'textfield',
      '#title' => 'Stream',
    ];

    $form['joining_year'] = [
      '#type' => 'date',
      '#title' => 'Joining year',
    ];

    $form['passing_year'] = [
      '#type' => 'date',
      '#title' => 'Passing year',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
    ];

    // Increasing the value of the serial no.
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
    if (preg_match("/^[a-zA-Z]+$/", $form_state->getValue('stream')) == FALSE) {
      $form_state->setErrorByName('stream', $this->t('Only text allowed'));
    }
    // Validation of mobile number.
    if (strlen($form_state->getValue('mobile')) != 10) {
      $form_state->setErrorByName('mobile', $this->t('Enter a valid phone no'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Storing the mail id of the user.
    $email = $form_state->getValue('email');

    // Running dynamic query to store the uid of the user.
    $query = \Drupal::database()->select('users_field_data', 'ufd')
      ->fields('ufd', ['uid'])
      ->condition('ufd.mail', $email)
      ->execute();
    $query_result = $query->fetchAll();
    $id = $query_result[0]->uid;

    // Storing the content of the mail.
    $store = "Name = " . $form_state->getValue('full_name') . ", Email =  " . $form_state->getValue('email') .
            ", Mobile = " . $form_state->getValue('mobile') . ", stream =  " . $form_state->getValue('stream') .
            ", Your joining year = " . $form_state->getValue('joining_year') . ", Your passing year = " . $form_state->getValue('passing_year') .
            "Your id is = " . $id;

    $params = [
      'message' => $store,
    ];
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = TRUE;
    $result = \Drupal::service('plugin.manager.mail')
      ->mail('sign_in_out', 'custom_email_key', $email, $langcode, $params, NULL, $send);
    if ($result['result'] !== TRUE) {
      $this->messenger()->addStatus($this->t('There was a problem sending the email.'));
    }
    else {
      $this->messenger()->addStatus($this->t('Email sent successfully.'));
    }

    $query = \Drupal::database()->select('users_field_data', 'ufd')
      ->fields('ufd', ['mail'])
      ->condition('ufd.uid', 1)
      ->execute();

    $result_query = $query->fetchAll();
    $admin_mail = $result_query[0]->mail;
    $result = \Drupal::service('plugin.manager.mail')
      ->mail('sign_in_out', 'custom_email_key', $admin_mail, $langcode, $params, NULL, $send);
    if ($result['result'] !== TRUE) {
      $this->messenger()->addStatus($this->t('There was a problem sending the email.'));
    }
    else {
      $this->messenger()->addStatus($this->t('Email sent successfully.'));
    }

  }

}
