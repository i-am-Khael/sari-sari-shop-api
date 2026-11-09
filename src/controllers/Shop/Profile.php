<?php

declare(strict_types=1);

namespace Controllers\Shop;

use Core\Helper;
use Core\Constants as MSG;
use Models\User;


class Profile
{

    public function profile(): string
    {
      try {

        $input = Helper::initializeRequest();
        $id = (int) $input['id'];

        $result = User::getUser('id', $id);

        $profile = [
          'id' => $result['id'],
          'username' => $result['username'],
          'firstName' => $result['firstName'],
          'lastName' => $result['lastName'],
          'email' => $result['email'],
          'phone' => $result['phone'],
          'region' => $result['region'],
          'province' => $result['province'],
          'province' => $result['province'],
          'municipality' => $result['municipality'],
          'brgy' => $result['brgy'],
          'street' => $result['street'],
        ];

        return Helper::success($profile);

      } catch (\Exception $e) {

        return Helper::serverError();

      }
    }


    public function update(): string
    {
      try {

        $input = Helper::initializeRequest();
        $id = Helper::sanitize($input['id']);
        $firstName = Helper::sanitize($input['firstName']);
        $lastName = Helper::sanitize($input['lastName']);
        $email = Helper::sanitize($input['email']);
        $phone = Helper::sanitize($input['phone']);
        $region = Helper::sanitize($input['region']);
        $province = Helper::sanitize($input['province']);
        $municipality = Helper::sanitize($input['municipality']);
        $brgy = Helper::sanitize($input['brgy']);
        $street = Helper::sanitize($input['street']);

        $result = User::update([
          ':id' => $id,
          ':firstName' => $firstName,
          ':lastName' => $lastName,
          ':email' => $email,
          ':phone' => $phone,
          ':region' => $region,
          ':province' => $province,
          ':municipality' => $municipality,
          ':brgy' => $brgy,
          ':street' => $street
        ]);

        if ( $result ) {

          return Helper::success([ 'msg' => 'Profile updated successfully!' ]);

        } else {

          return Helper::error([ 'msg' => 'Updating profile failed!' ]);

        }

      } catch (\Exception $e) {

        return Helper::serverError();

      }
    }

}
