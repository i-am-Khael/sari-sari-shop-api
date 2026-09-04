<?php

  declare(strict_types=1);

  namespace Controllers;

  use Core\Helper;
  use Models\Category;

  class Categories {

    private array $data = [];


    public function __construct() {
      header('Content-Type: application/json');
      $this->data = json_decode(file_get_contents('php://input'), true) ?? [];
    }


    public function add(): string {

      $errors = [];
      $category = ucfirst($this->data['category']);
      $description = Helper::sanitize(ucfirst($this->data['description']));
      $key = $_GET['key'] ?? '';

      try {

        if ( Helper::validate($key, 'key') === 'error' ) return Helper::error(400, [ 'apiKey' => INVALID_KEY ]);
        if ( Helper::validate($category, 'category') === 'error' ) $errors['category'] = CATEGORY_EXISTS;
        if ( !empty($errors) ) return Helper::error(400, $errors);

        $result = Category::addCategory([ $category, $description ]);

        if ( $result ) {
          return Helper::success(200, [
            'status' => 200,
            'msg' => CATEGORY_OK
          ]);
        }


      } catch (\PDOException $e) {

        return Helper::error(500, [ 'server' => SERVER_500 ]);

      }

    }


    public function getCategories() {

      try {

        $result = Category::getCategories();
        if ( count($result) > 0 ) return Helper::success(200, $result);

      } catch (\PDOException $e) {

        return Helper::error(500, [ 'server' => SERVER_500 ]);

      }

    }


    public function delete() {

      try {

        $id = Helper::sanitize( (string) $this->data['id'] );
        $result = Category::deleteCategory($id);
        $key = $_GET['key'] ?? '';

        if ( Helper::validate($key, 'key') === 'error' ) return Helper::error(400, [ 'apiKey' => INVALID_KEY ]);
        if ( $result ) return Helper::success(200, [ 'msg' => DELETE_CATEGORY ]);

      } catch (Exception $e) {

        return Helper::error(500, [ 'server' => SERVER_500 ]);

      }


    }

  }
