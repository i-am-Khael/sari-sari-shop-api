<?php

  namespace Core;

  final class Constants {

    public const FILE_PATH = __DIR__ . '/../storage/';
    public const SERVER_ERROR_500 = 'An internal server error occured. Please try again later.';
    public const INVALID_KEY = 'The API key provided is invalid.';
    public const INVALID_PAYLOAD = 'Invalid or no payload pass.';
    public const INVALID_IMAGE = 'The image must not exceed 1MB and must be in JPG, PNG and WebP format.';
    public const INVALID_USER = 'The username or password is incorrect.';
    public const INVALID_USERNAME = 'Username must be at least 3 characters long and may contain only letters and numbers.';
    public const INVALID_PASSWORD = 'Password must be at least 8 characters long.';
    public const USERNAME_ALREADY_EXISTS = 'This username is already in use.';
    public const REGISTRATION_SUCCESS = 'Your account has been registered successfully.';
    public const REGISTRATION_FAILED = 'Failed to register account.';
    public const CATEGORY_REQUIRED = 'Category is required.';
    public const CATEGORY_ICON_REQUIRED = 'Category icon is required.';
    public const CATEGORY_ICON_COLOR_REQUIRED = 'Category icon color is required.';
    public const CATEGORY_ALREADY_EXISTS = 'This category already exists.';
    public const CATEGORY_ADDED_SUCCESS = 'The category was added successfully.';
    public const CATEGORY_UPDATED_SUCCESS = 'The category was updated successfully.';
    public const CATEGORY_DELETE_SUCCESS = 'The category was deleted successfully.';
    public const CATEGORY_ADDED_FAILED = 'Failed to add a category.';
    public const CATEGORY_UPDATED_FAILED = 'Failed to update a category.';
    public const CATEGORY_DELETE_FAILED = 'Failed to delete a category.';
    public const PRODUCT_REQUIRED = 'Product is required.';
    public const PRODUCT_ID_REQUIRED = 'Product ID is required.';
    public const PRODUCT_ADDED_SUCCESS = 'The product was added successfully.';
    public const PRODUCT_UPDATED_SUCCESS = 'The product was updated successfully.';
    public const PRODUCT_DELETED_SUCCESS = 'The product was deleted successfully.';
    public const PRODUCT_ADDED_FAILED = 'Failed to add a product.';
    public const PRODUCT_UPDATE_FAILED = 'Failed to update a product.';
    public const PRODUCT_DELETE_FAILED = 'Failed to delete a product.';

  }
