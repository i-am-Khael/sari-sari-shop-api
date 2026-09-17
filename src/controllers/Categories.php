<?php

  declare(strict_types=1);

  namespace Controllers;

  use Core\Helper;
  use Models\Category;

  class Categories {

    private array $data = [];
    private string $key = '';
    private string $id = '';
    private string $category = '';
    private string $description = '';

    public function __construct() {
      header('Content-Type: application/json');
      $this->data = json_decode(file_get_contents('php://input'), true) ?? [];
      $this->key = trim($_SERVER['HTTP_X_API_KEY']) ?? '';
      $this->id = Helper::sanitize($this->data['id'] ?? '');
      $this->category = Helper::sanitize($this->data['category'] ?? '');
      $this->description = Helper::sanitize($this->data['description'] ?? '');
    }


    public function add(): string {

      $errors = [];

      try {

        if ( Helper::validate($this->key, 'key') === 'error' ) {
          return Helper::error(400, [ 'apiKey' => INVALID_KEY ]);
        }

        if ( Helper::validate($this->category, 'category') === 'error' ) $errors['category'] = CATEGORY_EXISTS;
        if ( $errors ) return Helper::error(400, $errors);

        $result = Category::addCategory([ $this->category, $this->description ]);

        if ( $result ) {
          return Helper::success(200, [
            'status' => 200,
            'msg' => CATEGORY_OK
          ]);
        }


      } catch (\PDOException $e) {

        return Helper::error(500, [ 'server' => SERVER_ERROR_500 ]);

      }

    }


    public function getCategories(): string {

      try {

        if ( Helper::validate($this->key, 'key') === 'error' ) {
          return Helper::error(400, [ 'apiKey' => INVALID_KEY ]);
        }

        $result = Category::getCategories();
        return Helper::success(200, $result);

      } catch (\PDOException $e) {

        return Helper::error(500, [ 'server' => SERVER_ERROR_500 ]);

      }

    }


    public function update(): string {

      $errors = [];

      try {

        if ( Helper::validate($this->key, 'key') === 'error' ) {
          return Helper::error(400, [ 'apiKey' => INVALID_KEY ]);
        }

        $category = Helper::validate($this->category);
        $description = Helper::validate($this->description);
        $check = Category::checkCategory($category, $this->id);

        if ( !empty($check) ) Helper::error(400, [ 'category' => CATEGORY_EXISTS ]);

        $result = Category::updateCategory([ $this->category, $this->description, $this->id ]);
        if ( $result ) return Helper::success(200, [ 'msg' => CATEGORY_UPDATED ]);

      } catch (\PDOException $e) {

        return Helper::error(500, [ 'server' => SERVER_ERROR_500 ]);

      }

    }


    public function delete(): string {

      try {

        if ( Helper::validate($this->key, 'key') === 'error' ) {
          return Helper::error(400, [ 'apiKey' => INVALID_KEY ]);
        }

        $result = Category::deleteCategory($this->id);
        if ( $result ) return Helper::success(200, [ 'msg' => DELETE_CATEGORY ]);

      } catch (\PDOException $e) {

        return Helper::error(500, [ 'server' => SERVER_ERROR_500 ]);

      }


    }

  }
