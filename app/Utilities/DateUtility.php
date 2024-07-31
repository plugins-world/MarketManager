<?php

namespace Plugins\MarketManager\Utilities;

use Plugins\MarketManager\Traits\DataTime;

class DateUtility
{
    public static function getRnageDay($pastDayNum, $featureDayNum)
    {
        $pastDays = DateUtility::getNextRangeDayInfo($pastDayNum, 'past', true);
        $currentDays = DateUtility::getNextRangeDayInfo(1, 'feature', false);
        $featureDays = DateUtility::getNextRangeDayInfo($featureDayNum, 'feature', true);

        $days = array_merge($pastDays, $currentDays, $featureDays);

        return $days;
    }

    public static function getNextRangeDayInfo($dayNum = 7, $direction = 'feature', $excludeToday = false)
    {
        $direction = match ($direction) {
            default => 'feature',
            'feature' => 'feature',
            'past' => 'past',
        };

        // 获取当前日期
        $currentDate = new \DateTime();

        // 循环获取接下来的一周日期
        $data = [];
        if ($direction == 'past') {
            if ($excludeToday) {
                $subDayNum = $dayNum;
            } else {
                $subDayNum = $dayNum - 1;
            }

            $dateInterval = new \DateInterval('P' . $subDayNum . 'D'); // 创建一个新的日期间隔
            $currentDate = $currentDate->sub($dateInterval);
        } else {
            if ($excludeToday) {
                $addDayNum = 1;
            } else {
                $addDayNum = 0;
            }

            $dateInterval = new \DateInterval('P' . $addDayNum . 'D'); // 创建一个新的日期间隔
            $currentDate = $currentDate->add($dateInterval);
        }

        for ($i = 0; $i < $dayNum; $i++) {
            $dateInterval = new \DateInterval('P' . $i . 'D'); // 创建一个新的日期间隔
            $day = $currentDate->add($dateInterval); // 获取未来的日期

            $item = DateUtility::getDateInfo($day->format('Y-m-d'));
            if (!$item) {
                continue;
            }

            // 为下一次迭代重设$currentDate
            $currentDate = $currentDate->sub($dateInterval);

            $data[] = $item;
        }

        // 按日期排序
        array_multisort(array_column($data, 'date'), SORT_ASC, $data);

        return $data;
    }

    public static function getDateInfo(?string $date)
    {
        if (!$date) {
            return null;
        }

        $day = new \DateTime($date);

        $yesterdayDateStr = date('Y-m-d', strtotime('yesterday'));
        $currentDateStr = date('Y-m-d');
        $tomorrowDateStr = date('Y-m-d', strtotime('tomorrow'));

        $item['is_yesterday_day'] = false;
        $item['is_today'] = false;
        $item['is_tomorrow_day'] = false;
        $item['date_desc'] = '';
        if ($day->format('Y-m-d') == $yesterdayDateStr) {
            $item['is_yesterday_day'] = true;
            $item['date_desc'] = '昨天';
        }
        if ($day->format('Y-m-d') == $currentDateStr) {
            $item['is_today'] = true;
            $item['date_desc'] = '今天';
        }
        if ($day->format('Y-m-d') == $tomorrowDateStr) {
            $item['is_tomorrow_day'] = true;
            $item['date_desc'] = '明天';
        }
        $item['date'] = $day->format('Y-m-d');
        $item['day'] = DateUtility::getDateDay($item['date']);
        $item['zhou'] = DateUtility::getChineseWeekday($item['day']);
        $item['xingqi'] = str_replace('周', '星期', $item['zhou']);

        return $item;
    }

    // 定义一个函数将星期数字转化为中文
    public static function getChineseWeekday($weekdayNum)
    {
        $weekdayNumIndex = $weekdayNum - 1;

        $weekdays = array("周一", "周二", "周三", "周四", "周五", "周六", "周日");
        return $weekdays[$weekdayNumIndex];
    }

    public static function getDateDay(?string $date)
    {
        if (!$date) {
            return null;
        }
        try {
            $date = new \DateTime($date);
        } catch (\Throwable $e) {
            info("{$date} 数据不是正确的日期格式, 错误信息: " . $e->getMessage());
            return null;
        }
        $dayOfWeek = intval($date->format('w') ?: '7');

        return $dayOfWeek;
    }

    public static function getYearMonth($year = null, $limitToCurrentMonth = true)
    {
        $currentYear = $year;

        // 获取当前年份
        if (!$currentYear) {
            $currentYear = date("Y");
        }

        // 创建一个数组来保存12个月份
        $monthsList = [];

        for ($i = 1; $i <= 12; $i++) {
            // 数字月份前面补零，比如 '01'，'02'，...，'12'
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);

            // 将年份和月份结合，格式为 'YYYY-MM'
            $monthsList[] = "{$currentYear}-{$month}-01";
        }


        $months = [];
        foreach ($monthsList as $monthItem) {
            $currentMonthItem = \Carbon\Carbon::createFromDate($monthItem);

            if ($limitToCurrentMonth) {
                if ($currentMonthItem->gt(now())) {
                    continue;
                }
            }

            $months[] = $monthItem;
        }

        return $months;
    }

    public static function getYearMonthRange(array $monthRange)
    {
        $data = [];
        foreach ($monthRange as $item) {
            $startDay = static::getYearMonthDay($item, 'first');
            $endDay = static::getYearMonthDay($item, 'last');

            $data[] = [
                $startDay,
                $endDay,
            ];
        }

        return $data;
    }

    public static function getYearMonthDay($monthDay = null, $type = 'first')
    {
        $type = match ($type) {
            default => 'first',
            'first' => 'first',
            'last' => 'last',
        };

        $date = new \DateTime($monthDay);
        $date->modify("{$type} day of this month");

        $dateString = $date->format('Y-m-d');

        $dateTimeString = match ($type) {
            default => $dateString . ' 00:00:00',
            'first' => $dateString . ' 00:00:00',
            'last' => $dateString . ' 23:59:59',
        };

        return $dateTimeString;
    }

    /**
     * 仪表盘日志使用
     * 根据类型获取开始结束时间戳数组
     * @param
     */
    public function getDateTimeInfoByDateType($type = 'today')
    {
        switch ($type) {
            case 'yesterday' :
                $timeArr = DataTime::yesterday();
                $timeArr['last_time'] = DataTime::yesterday(1);
                break;
            case 'week' :
                $timeArr = DataTime::week();
                $timeArr['last_time'] = DataTime::lastWeek();
                break;
            case 'lastWeek' :
                $timeArr = DataTime::lastWeek();
                $timeArr['last_time'] = DataTime::lastWeek(1);
                break;
            case 'month' :
                $timeArr = DataTime::month();
                $timeArr['last_time'] = DataTime::lastMonth();
                break;
            case 'lastMonth' :
                $timeArr = DataTime::lastMonth();
                $timeArr['last_time'] = DataTime::lastMonth(1);
                break;
            case 'quarter' :
                //本季度
                $month = date('m');
                if ($month == 1 || $month == 2 || $month == 3) {
                    $daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-03-31 23:59:59"));
                } elseif ($month == 4 || $month == 5 || $month == 6) {
                    $daterange_start_time = strtotime(date('Y-04-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-06-30 23:59:59"));
                } elseif ($month == 7 || $month == 8 || $month == 9) {
                    $daterange_start_time = strtotime(date('Y-07-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-09-30 23:59:59"));
                } else {
                    $daterange_start_time = strtotime(date('Y-10-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-12-31 23:59:59"));
                }

                //上季度
                $month = date('m');
                if ($month == 1 || $month == 2 || $month == 3) {
                    $year = date('Y') - 1;
                    $daterange_start_time_last_time = strtotime(date($year . '-10-01 00:00:00'));
                    $daterange_end_time_last_time = strtotime(date($year . '-12-31 23:59:59'));
                } elseif ($month == 4 || $month == 5 || $month == 6) {
                    $daterange_start_time_last_time = strtotime(date('Y-01-01 00:00:00'));
                    $daterange_end_time_last_time = strtotime(date("Y-03-31 23:59:59"));
                } elseif ($month == 7 || $month == 8 || $month == 9) {
                    $daterange_start_time_last_time = strtotime(date('Y-04-01 00:00:00'));
                    $daterange_end_time_last_time = strtotime(date("Y-06-30 23:59:59"));
                } else {
                    $daterange_start_time_last_time = strtotime(date('Y-07-01 00:00:00'));
                    $daterange_end_time_last_time = strtotime(date("Y-09-30 23:59:59"));
                }
                $timeArr = array($daterange_start_time, $daterange_end_time);
                $timeArr['last_time'] = array($daterange_start_time_last_time, $daterange_end_time_last_time);
                break;
            case 'lastQuarter' :
                //上季度
                $month = date('m');
                if ($month == 1 || $month == 2 || $month == 3) {
                    $year = date('Y') - 1;
                    $daterange_start_time = strtotime(date($year . '-10-01 00:00:00'));
                    $daterange_end_time = strtotime(date($year . '-12-31 23:59:59'));
                } elseif ($month == 4 || $month == 5 || $month == 6) {
                    $daterange_start_time = strtotime(date('Y-01-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-03-31 23:59:59"));
                } elseif ($month == 7 || $month == 8 || $month == 9) {
                    $daterange_start_time = strtotime(date('Y-04-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-06-30 23:59:59"));
                } else {
                    $daterange_start_time = strtotime(date('Y-07-01 00:00:00'));
                    $daterange_end_time = strtotime(date("Y-09-30 23:59:59"));
                }
                //上季度
                $month = date('m');
                if ($month == 1 || $month == 2 || $month == 3) {
                    $year = date('Y') - 2;
                    $daterange_start_time_last_time = strtotime(date($year . '-10-01 00:00:00'));
                    $daterange_end_time_last_time = strtotime(date($year . '-12-31 23:59:59'));
                } elseif ($month == 4 || $month == 5 || $month == 6) {
                    $daterange_start_time_last_time = strtotime(date('Y-01-01 00:00:00'));
                    $daterange_end_time_last_time = strtotime(date("Y-03-31 23:59:59"));
                } elseif ($month == 7 || $month == 8 || $month == 9) {
                    $daterange_start_time_last_time = strtotime(date('Y-04-01 00:00:00'));
                    $daterange_end_time_last_time = strtotime(date("Y-06-30 23:59:59"));
                } else {
                    $daterange_start_time_last_time = strtotime(date('Y-07-01 00:00:00'));
                    $daterange_end_time_last_time = strtotime(date("Y-09-30 23:59:59"));
                }
                $timeArr = array($daterange_start_time, $daterange_end_time);
                $timeArr['last_time'] = array($daterange_start_time_last_time, $daterange_end_time_last_time);
                break;
            case 'year' :
                $timeArr = DataTime::year();
                $timeArr['last_time'] = DataTime::lastYear();
                break;
            case 'lastYear' :
                $timeArr = DataTime::lastYear();
                $timeArr['last_time'] = DataTime::lastYear(1);
                break;
            case 'recent60' :
                $timeArr = DataTime::recent60();
                break;
            case 'recent30' :
                $timeArr = DataTime::recent30();
                break;
            default :
                $timeArr = DataTime::today();
                $timeArr['last_time'] = DataTime::yesterday();
                break;
        }
        return $timeArr;
    }
}
