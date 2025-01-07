<?php

declare(strict_types=1);

// Source code credit to: https://youthsforum.com/2020/08/build-a-simple-calendar-in-website-using-php-with-source-code/

class Calendar
{

    /* Constructor */
    public function __construct($roomId)
    {
        $this->naviHref = htmlentities($_SERVER['PHP_SELF']);
        $this->roomId = $roomId;
        // Add debug output
        error_log("Calendar created for room_id: " . $roomId);
    }

    /********************* PROPERTY ********************/

    private $dayLabels = array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");
    private $currentYear = 0;
    private $currentMonth = 0;
    private $currentDay = 0;
    private $currentDate = null;
    private $daysInMonth = 0;
    private $naviHref = null;
    private $roomId = null;

    /********************* PUBLIC **********************/

    /* Function to print out the calendar */
    public function show()
    {
        $year  = 2025; //Fixed year
        $month = 1; //Fixed month

        $this->currentYear = $year;
        $this->currentMonth = $month;
        $this->daysInMonth = $this->_daysInMonth($month, $year);

        $content = '<div id="calendar" class="room-' . $this->roomId . '">' .
            '<div class="box">' .
            $this->_createNavi() .
            '</div>' .
            '<div class="box-content">' .
            '<ul class="label">' . $this->_createLabels() . '</ul>';
        $content .= '<div class="clear"></div>';
        $content .= '<ul class="dates">';

        $weeksInMonth = $this->_weeksInMonth($month, $year);

        // Create weeks in a month
        for ($i = 0; $i < $weeksInMonth; $i++) {
            //Create days in a week
            for ($j = 1; $j <= 7; $j++) {
                $content .= $this->_showDay($i * 7 + $j);
            }
        }

        $content .= '</ul>';
        $content .= '<div class="clear"></div>';
        $content .= '</div>';
        $content .= '</div>';

        return $content;
    }

    /********************* PRIVATE **********************/

    /* Function to create the li element for ul */

    private function _showDay($cellNumber)
    {
        // Only fetch booked dates once per calendar instance
        static $bookedDates = null;
        static $lastRoomId = null;

        // Reset bookedDates if we're showing a different room
        if ($lastRoomId !== $this->roomId) {
            $bookedDates = null;
            $lastRoomId = $this->roomId;
        }

        if ($bookedDates === null) {
            $bookedDates = $this->_getBookedDates($this->currentYear, $this->currentMonth);
            // Add debug output
            error_log("Fetched booked dates for room_id {$this->roomId}: " . print_r($bookedDates, true));
        }

        if ($this->currentDay == 0) {
            $firstDayOfTheWeek = date('N', strtotime($this->currentYear . '-' . $this->currentMonth . '-01'));
            if (intval($cellNumber) == intval($firstDayOfTheWeek)) {
                $this->currentDay = 1;
            }
        }

        if (($this->currentDay != 0) && ($this->currentDay <= $this->daysInMonth)) {
            $this->currentDate = date('Y-m-d', strtotime($this->currentYear . '-' . $this->currentMonth . '-' . ($this->currentDay)));
            $cellContent = $this->currentDay;
            $this->currentDay++;
        } else {
            $this->currentDate = null;
            $cellContent = null;
        }

        $isBooked = in_array($this->currentDate, $bookedDates);
        $class = ($cellNumber % 7 == 1 ? ' start ' : ($cellNumber % 7 == 0 ? ' end ' : ' ')) .
            ($cellContent == null ? 'mask' : '') .
            ($isBooked ? ' booked' : '');

        return '<li id="li-' . $this->currentDate . '" class="' . $class . '" data-room="' . $this->roomId . '">' . $cellContent . '</li>';
    }
    /* create navigation */
    private function _createNavi()
    {

        $nextMonth = $this->currentMonth == 12 ? 1 : intval($this->currentMonth) + 1;

        $nextYear = $this->currentMonth == 12 ? intval($this->currentYear) + 1 : $this->currentYear;

        $preMonth = $this->currentMonth == 1 ? 12 : intval($this->currentMonth) - 1;

        $preYear = $this->currentMonth == 1 ? intval($this->currentYear) - 1 : $this->currentYear;

        return
            '<div class="header">' .

            //Commented this out to hide the "Previous" button
            // '<a class="prev" href="' . $this->naviHref . '?month=' . sprintf('%02d', $preMonth) . '&year=' . $preYear . '">Prev</a>' . 

            '<span class="title"> Availability: ' . date('F Y', strtotime($this->currentYear . '-' . $this->currentMonth . '-1')) . '</span>' .

            //Commented this out to hide the "Next" button
            // '<a class="next" href="' . $this->naviHref . '?month=' . sprintf("%02d", $nextMonth) . '&year=' . $nextYear . '">Next</a>' .

            '</div>';
    }

    /* Function to create calendar week labels */
    private function _createLabels()
    {

        $content = '';

        foreach ($this->dayLabels as $index => $label) {

            $content .= '<li class="' . ($label == 6 ? 'end title' : 'start title') . ' title">' . $label . '</li>';
        }

        return $content;
    }



    /* Function to calculate number of weeks in a particular month */
    private function _weeksInMonth($month = null, $year = null)
    {

        if (null == ($year)) {
            $year =  date("Y", time());
        }

        if (null == ($month)) {
            $month = date("m", time());
        }

        // find number of days in this month
        $daysInMonths = $this->_daysInMonth($month, $year);

        $numOfweeks = ($daysInMonths % 7 == 0 ? 0 : 1) + intval($daysInMonths / 7);

        $monthEndingDay = date('N', strtotime($year . '-' . $month . '-' . $daysInMonths));

        $monthStartDay = date('N', strtotime($year . '-' . $month . '-01'));

        if ($monthEndingDay < $monthStartDay) {

            $numOfweeks++;
        }

        return $numOfweeks;
    }

    /* Function to calculate number of days in a particular month */
    private function _daysInMonth($month = null, $year = null)
    {

        if (null == ($year))
            $year =  date("Y", time());

        if (null == ($month))
            $month = date("m", time());

        return date('t', strtotime($year . '-' . $month . '-01'));
    }

    /* Fetch booked dates from the database for the specified month and year. */

    private function _getBookedDates($year, $month)
    {
        try {
            $db = new PDO('sqlite:app/database/bookings.db');
            $query = "SELECT arrival_date, departure_date FROM bookings 
                     WHERE strftime('%Y', arrival_date) = :year 
                     AND strftime('%m', arrival_date) = :month
                     AND room_id = :room_id";

            $stmt = $db->prepare($query);
            $stmt->bindValue(':year', $year, PDO::PARAM_STR);
            $stmt->bindValue(':month', sprintf('%02d', $month), PDO::PARAM_STR);
            $stmt->bindValue(':room_id', $this->roomId, PDO::PARAM_INT);
            $stmt->execute();

            // Add debug output
            error_log("Running query for room_id: {$this->roomId}, year: {$year}, month: {$month}");

            $bookedDates = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $start = new DateTime($row['arrival_date']);
                $end = new DateTime($row['departure_date']);

                // Generate all dates between arrival and departure
                while ($start <= $end) {
                    $bookedDates[] = $start->format('Y-m-d');
                    $start->modify('+1 day');
                }
            }

            // Add debug output
            error_log("Found " . count($bookedDates) . " booked dates for room_id {$this->roomId}");
            return $bookedDates;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }
}
