<?php

  declare(strict_types=1);

  namespace Controllers;

  use Core\Helper;
  use Models\Category;

  class Categories {

    private array $data = [];
    private string $key = '';
    private string $id = '';
    private string $icon = '';
    private string $color = '';
    private string $category = '';
    private string $description = '';

    public function __construct() {
      header('Content-Type: application/json');
      $this->data = json_decode(file_get_contents('php://input'), true) ?? [];
      $this->key = trim($_SERVER['HTTP_X_API_KEY']) ?? '';
      $this->id = Helper::sanitize($this->data['id'] ?? '');
      $this->icon = Helper::sanitize($this->data['icon'] ?? '');
      $this->color = Helper::sanitize($this->data['color'] ?? '');
      $this->category = Helper::sanitize($this->data['category'] ?? '');
      $this->description = Helper::sanitize($this->data['description'] ?? '');
    }


    public function add(): string {

      $category = Helper::sanitize($this->category);
      $description = Helper::sanitize($this->description);
      $errors = [];

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error(400, [ 'key' => INVALID_KEY ]);

        $check = Category::getCategory($category);

        if ( $check ) $errors['category'] = CATEGORY_EXISTS;
        if ( $this->icon === '' ) $errors['icon'] = 'Icon is required.';
        if ( $this->color === '' ) $errors['color'] = 'Color is required.';
        if ( $category === '' ) $errors['category'] = 'Category is required.';
        if ( $errors ) return Helper::error(400, $errors);

        $result = Category::addCategory([ $category, $description, $this->icon, $this->color ]);

        if ( $result ) {
          return Helper::success(200, [
            'status' => 200,
            'msg' => CATEGORY_OK
          ]);
        }


      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }


    public function getCategories(): string {

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error(400, [ 'key' => INVALID_KEY ]);

        $result = Category::getCategories();
        return Helper::success(200, $result);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }


    public function update(): string {

      $id = Helper::sanitize($this->id);
      $category = Helper::sanitize($this->category);
      $description = Helper::sanitize($this->description);
      $errors = [];

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error(400, [ 'key' => INVALID_KEY ]);

        $check = Category::checkCategory($category, $this->id);

        if ( !empty($check) ) $errors['category'] = CATEGORY_EXISTS ;
        if ( $this->icon === '' ) $errors['icon'] = 'Icon is required.' ;
        if ( $this->color === '' ) $errors['color'] = 'Color is required.' ;
        if ( $category === '' ) $errors['category'] = 'Category is required.' ;
        if ( $errors ) return Helper::error(400, $errors);

        $result = Category::updateCategory([ $this->icon, $this->color, $category, $description, $id ]);
        if ( $result ) return Helper::success(200, [ 'msg' => CATEGORY_UPDATED ]);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }


    public function delete(): string {

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error(400, [ 'key' => INVALID_KEY ]);

        $result = Category::deleteCategory($this->id);
        if ( $result ) return Helper::success(200, [ 'msg' => DELETE_CATEGORY ]);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }


    }

  }
