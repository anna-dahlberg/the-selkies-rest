<!-- Source code credit to: https://youthsforum.com/2020/08/build-a-simple-calendar-in-website-using-php-with-source-code/ -->

<?php
class Calendar
{

    /* Constructor */
    public function __construct()
    {
        $this->naviHref = htmlentities($_SERVER['PHP_SELF']);
    }

    /********************* PROPERTY ********************/

    private $dayLabels = array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");
    private $currentYear = 0;
    private $currentMonth = 0;
    private $currentDay = 0;
    private $currentDate = null;
    private $daysInMonth = 0;
    private $naviHref = null;

    /********************* PUBLIC **********************/

    /* Function to print out the calendar */
    public function show()
    {
        $year  = 2025; //Fixed year
        $month = 1; //Fixed month

        if (null == $year && isset($_GET['year'])) {

            $year = $_GET['year'];
        } else if (null == $year) {

            $year = date("Y", time());
        }

        if (null == $month && isset($_GET['month'])) {

            $month = $_GET['month'];
        } else if (null == $month) {

            $month = date("m", time());
        }

        $this->currentYear = $year;
        $this->currentMonth = $month;
        $this->daysInMonth = $this->_daysInMonth($month, $year);

        $content = '<div id="calendar">' .
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


        return '<li id="li-' . $this->currentDate . '" class="' . ($cellNumber % 7 == 1 ? ' start ' : ($cellNumber % 7 == 0 ? ' end ' : ' ')) .
            ($cellContent == null ? 'mask' : '') . '">' . $cellContent . '</li>';
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

            '<span class="title">' . date('Y M', strtotime($this->currentYear . '-' . $this->currentMonth . '-1')) . '</span>' .

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

    // My own functions for displaying booked dates in calendar

    private function _showDay($cellNumber)
    {
        // Only fetch booked dates once, store them in an array
        static $bookedDates = null;

        if ($bookedDates === null) {
            $bookedDates = $this->_getBookedDates($this->currentYear, $this->currentMonth);
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

        // Add class 'booked' if the current date is in the bookedDates array
        $isBooked = in_array($this->currentDate, $bookedDates);
        $class = ($cellNumber % 7 == 1 ? ' start ' : ($cellNumber % 7 == 0 ? ' end ' : ' ')) .
            ($cellContent == null ? 'mask' : '') .
            ($isBooked ? ' booked' : '');

        return '<li id="li-' . $this->currentDate . '" class="' . $class . '">' . $cellContent . '</li>';
    }

    /* Fetch booked dates from the database for the specified month and year. */

    private function _getBookedDates($year, $month)
    {
        $db = new PDO('sqlite:your-database-file.sqlite');
        $query = "SELECT arrival_date, departure_date FROM bookings 
              WHERE strftime('%Y', arrival_date) = :year 
              AND strftime('%m', arrival_date) = :month";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':year', $year, PDO::PARAM_STR);
        $stmt->bindValue(':month', sprintf('%02d', $month), PDO::PARAM_STR);
        $stmt->execute();

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

        return $bookedDates;
    }
}
