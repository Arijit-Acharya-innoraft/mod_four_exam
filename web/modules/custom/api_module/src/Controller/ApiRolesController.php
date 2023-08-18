<?php

namespace Drupal\api_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class for sending the json data.
 */
class ApiRolesController extends ControllerBase {

  /**
   * Callback function for the API endpoint.
   */
  public function getData() {
    // Check if the user has the required role(s) to access the data.
    if ($this->currentUser()->hasPermission('access content')) {
      $query = \Drupal::database()->select('users_field_data', 'u');
      $query->fields('u', ['uid', 'name', 'mail'])
        ->condition('u.uid', 1, '>')
        ->orderBy('u.uid');

      $result = $query->execute()->fetchAll();
      $store = [];
      foreach ($result as $row) {
        $uid = $row->uid;
        $name = $row->name;
        $mail = $row->mail;
        $store[$uid][] = [$name, $mail];
      }

      $data = [$store];

      return new JsonResponse($data);
    }
    else {
      throw new AccessDeniedHttpException();
    }
  }

}
