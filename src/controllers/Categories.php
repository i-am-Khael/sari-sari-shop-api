<?php

  declare(strict_types=1);

  namespace Controllers;

  use Core\Constants as MSG;
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

      $errors = [];

      $category = Helper::sanitize($this->category);
      $description = Helper::sanitize($this->description);

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error([ 'key' => MSG::INVALID_KEY ]);

        $check = Category::getCategory($category);

        if ( $check ) $errors['category'] = MSG::CATEGORY_ALREADY_EXISTS;
        if ( $this->icon === '' ) $errors['icon'] = MSG::CATEGORY_ICON_REQUIRED;
        if ( $this->color === '' ) $errors['color'] = MSG::CATEGORY_ICON_COLOR_REQUIRED;
        if ( $category === '' ) $errors['category'] = MSG::CATEGORY_REQUIRED;
        if ( $errors ) return Helper::error($errors);

        $result = Category::addCategory([ $category, $description, $this->icon, $this->color ]);

        if ( $result ) return Helper::success([ 'msg' => MSG::CATEGORY_ADDED_SUCCESS ], 201);

        return Helper::error([ 'msg' => MSG::CATEGORY_ADDED_FAILED ]);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }


    public function getCategories(): string {

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error([ 'key' => MSG::INVALID_KEY ]);

        $result = Category::getCategories();
        return Helper::success($result);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }


    public function update(): string {

      $errors = [];

      $id = Helper::sanitize($this->id);
      $category = Helper::sanitize($this->category);
      $description = Helper::sanitize($this->description);

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error([ 'key' => MSG::INVALID_KEY ]);

        $check = Category::checkCategory($category, $this->id);

        if ( !empty($check) ) $errors['category'] = MSG::CATEGORY_ALREADY_EXISTS;
        if ( $this->icon === '' ) $errors['icon'] = MSG::CATEGORY_ICON_REQUIRED;
        if ( $this->color === '' ) $errors['color'] = MSG::CATEGORY_ICON_COLOR_REQUIRED;
        if ( $category === '' ) $errors['category'] = MSG::CATEGORY_REQUIRED;
        if ( $errors ) return Helper::error($errors);

        $result = Category::updateCategory([ $this->icon, $this->color, $category, $description, $id ]);
        if ( $result ) return Helper::success([ 'msg' => MSG::CATEGORY_UPDATED_SUCCESS ]);

        return Helper::error(['msg' => MSG::CATEGORY_UPDATED_FAILED]);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }

    }


    public function delete(): string {

      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error([ 'key' => MSG::INVALID_KEY ]);

        $result = Category::deleteCategory($this->id);
        if ( $result ) return Helper::success([ 'msg' => MSG::CATEGORY_DELETE_SUCCESS ]);

        return Helper::error([ 'msg' => MSG::CATEGORY_DELETE_FAILED ]);

      } catch (\PDOException $e) {

        return Helper::serverError();

      }


    }

  }
