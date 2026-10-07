<?php

  declare(strict_types=1);

  namespace Controllers;

  use Core\Constants as MSG;
  use Core\Helper;
  use DateTimeImmutable;
  use Models\Dashboard as Dash;

  class Dashboard {

    private string $key = '';
    private array $data = [];


    public function __construct() {
      header('Content-Type: application/json');
      $this->key = trim($_SERVER['HTTP_X_API_KEY']) ?? '';
      $this->data = json_decode(file_get_contents('php://input'), true) ?? [];
    }


    public function totals(): string {
      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error([ 'key' => MSG::INVALID_KEY ]);

        $result = Dash::totals();

        if ( $result ) return Helper::success($result[0] ?? []);

        return Helper::error([ 'msg' => 'Failed to get totals.' ]);

      } catch (\Exception $e) {

        return Helper::serverError();

      }
    }


    public function sales(): string {
      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error([ 'key' => MSG::INVALID_KEY ]);

        $timePeriod = [ 'day', 'week', 'month', 'year' ];
        $period = strtolower($this->data['period'] ?? 'day');
        $dateInput = $this->data['date'] ?? date('Y-m-d');

        if ( !in_array($period, $timePeriod, true) ) return Helper::error([ 'msg' => 'Time period is not valid.' ]);

        $selected = DateTimeImmutable::createFromFormat('!Y-m-d', $dateInput);

        if ( !$selected || $selected->format('Y-m-d') !== $dateInput ) return Helper::error([ 'msg' => 'Date is not valid.' ]);

        $year = (int) $selected->format('Y');
        $month = (int) $selected->format('n');

        if ( $period === 'day' || $period === 'week' ) {

          $start = $selected->modify('first day of this month')->setTime(0, 0);
          $end = $start->modify('first day of next month');

        } elseif ( $period === 'month' ) {

          $start = $selected->setDate($year, 1, 1)->setTime(0, 0);
          $end = $start->modify('+1 year');

        } else {

          $start = $selected->setDate($year - 5, 1, 1)->setTime(0, 0);
          $end = $selected->setDate($year + 1, 1, 1)->setTime(0, 0);

        }

        $labels = [];
        $sales = [];

        if ( $period === 'day' ) {

          $days = (int) $start->format('t');

          for ( $day = 1; $day <= $days; $day++ ) {
            $labels[] = (string) $day;
            $sales[] = 0.0;
          }

        } elseif ( $period === 'week' ) {

          $days = (int) $start->format('t');
          $weekCount = (int) ceil($days / 7);

          for ( $week = 1; $week <= $weekCount; $week++ ) {
            $labels[] = (string) $week;
            $sales[] = 0.0;
          }

        } elseif ( $period === 'month' ) {

          $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
          $sales = array_fill(0, 12, 0.0);

        } else {

          for ( $y = $year - 5; $y <= $year; $y++ ) {
            $labels[] = (string) $y;
            $sales[] = 0.0;
          }

        }

        $result = Dash::getSales([
          $start->format('Y-m-d H:i:s'),
          $end->format('Y-m-d H:i:s'),
        ]);

        if ( is_array($result) ) {

          foreach ($result as $data) {

            $orderDate = new DateTimeImmutable($data['created_at']);

            if ( $period === 'day' ) {

              $index = (int) $orderDate->format('j') - 1;

            } elseif ( $period === 'week' ) {

              $dayOfMonth = (int) $orderDate->format('j');
              $index = intdiv($dayOfMonth - 1, 7);
              // $index = (int) floor((((int) $orderDate->format('j') - 1) / 7 ));

            } elseif ( $period === 'month' ) {

              $index = (int) $orderDate->format('n') - 1;

            } else {

              $index = (int) $orderDate->format('Y') - ($year - 5);

            }

            $sales[$index] += (float) $data['total_amount'];

          }

          return Helper::success([ 'labels' => $labels, 'sales' => $sales ]);

        }

        return Helper::error([ 'msg' => 'Failed to load sales..' ]);

      } catch (\Exception $e) {

        return Helper::serverError();

      }
    }


    public function popularCategories(): string {
      try {

        if ( !Helper::hasValidApiKey($this->key) ) return Helper::error([ 'key' => MSG::INVALID_KEY ]);

        $result = Dash::popularCategories();

        if ( $result ) return Helper::success($result);

        return Helper::error([ 'msg' => 'Failed to get popular categories.' ]);

      } catch (\Exception $e) {

        return Helper::serverError();

      }
    }

  }
