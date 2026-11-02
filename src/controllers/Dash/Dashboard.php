<?php

declare(strict_types=1);

namespace Controllers\Dash;

use DateTimeImmutable;
use Core\Helper;
use Core\Constants as MSG;
use Models\Dashboard as Dash;


class Dashboard
{

  private array $data = [];


  public function __construct()
  {
    $this->data = Helper::initializeRequest();
  }


  public function totals(): string
  {
    try {

      $result = Dash::totals();

      if ( $result ) return Helper::success($result[0] ?? []);

      return Helper::error([ 'msg' => 'Failed to get totals.' ]);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


  public function sales(): string
  {
    try {

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

      $result = Dash::sales([
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


  public function orders(): string
  {
    try {

      $result = Dash::orders();
      return Helper::success($result);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }


  public function categories(): string
  {
    try {

      $result = Dash::categories();
      return Helper::success($result);

    } catch (\Exception $e) {

      return Helper::serverError();

    }
  }

}
