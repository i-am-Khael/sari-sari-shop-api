<?php

  declare(strict_types=1);

  namespace Controllers;
  use Core\Helper;

  class Products {

    private array $data = [];
    private string $key = '';
    private string $id = '';
    private string $product = '';
    private string $category_id = '';
    private string $stock = '';
    private string $price = '';
    private string $description = '';
    private array $file = [];

    public function __construct() {
      header('Content-Type: application/json');
      $this->data = json_decode(file_get_contents('php://input'), true) ?? [];
      $this->key = trim($_SERVER['HTTP_X_API_KEY']) ?? '';
      $this->id = Helper::sanitize($this->data['id'] ?? '');
      $this->product = Helper::sanitize($this->data['product'] ?? '');
      $this->category_id = Helper::sanitize($this->data['category_id'] ?? '');
      $this->stock = Helper::sanitize($this->data['stock'] ?? '');
      $this->price = Helper::sanitize($this->data['price'] ?? '');
      $this->description = Helper::sanitize($this->data['description'] ?? '');
      $this->file = $_FILES['file'] ?? [];
    }


    public function add(): string {

      $errors = [];
      // $id = Helper::sanitize($_POST['id'] ?? '');
      $product = Helper::sanitize($_POST['product'] ?? '');
      $stock = Helper::sanitize($_POST['stock'] ?? '');
      $price = Helper::sanitize($_POST['price'] ?? '');
      $description = Helper::sanitize($_POST['description'] ?? '');
      $category_id = Helper::sanitize($_POST['category_id'] ?? '');
      $file = $_FILES['file'] ?? [];

      try {

        if ( Helper::validate($this->key, 'key') === 'error' ) return Helper::error(400, [ 'apiKey' => INVALID_KEY ]);

      } catch (Exception $e) {

        return Helper::error(500, [ 'server' => SERVER_ERROR_500 ]);

      }

    }

  }
